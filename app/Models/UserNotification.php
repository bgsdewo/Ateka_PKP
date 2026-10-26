<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'link',
        'request_id',
        'product_id',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class, 'request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Kirim notifikasi pengadaan barang telah dibeli dan tersedia di daftar barang / katalog
     * Dilengkapi proteksi anti-duplikasi otomatis (mencegah spam jika produk sama diajukan berulang)
     */
    public static function sendProcurementNotification(RequestItem $requestItem, Product $product, ?string $invoice = null): self
    {
        $title = 'Barang yang Diminta Sudah Tersedia!';
        $message = "Barang \"{$product->name}\" pada pengajuan #{$requestItem->request_code} telah ditambahkan di Master Data dan kini stoknya sudah tersedia di Katalog ATK.";

        // Cegah spam: jika notifikasi barang yang sama untuk user ini sudah ada dan belum dibaca, perbarui pesannya
        $existing = self::where('user_id', $requestItem->user_id)
            ->where('product_id', $product->id)
            ->where('type', 'procurement_available')
            ->where('is_read', false)
            ->first();

        if ($existing) {
            $existing->update([
                'title' => $title,
                'message' => $message,
                'request_id' => $requestItem->id,
                'link' => route('katalog-atk', ['search' => $product->code]),
                'data' => [
                    'product_name' => $product->name,
                    'request_code' => $requestItem->request_code,
                    'invoice' => $invoice,
                ],
            ]);
            $existing->touch();
            return $existing;
        }

        return self::create([
            'user_id' => $requestItem->user_id,
            'title' => $title,
            'message' => $message,
            'type' => 'procurement_available',
            'icon' => 'box',
            'link' => route('katalog-atk', ['search' => $product->code]),
            'request_id' => $requestItem->id,
            'product_id' => $product->id,
            'is_read' => false,
            'data' => [
                'product_name' => $product->name,
                'request_code' => $requestItem->request_code,
                'invoice' => $invoice,
            ],
        ]);
    }

    /**
     * Kirim notifikasi pengadaan gabungan (konsolidasi) jika satu tiket usulan memiliki banyak item barang
     */
    public static function sendConsolidatedProcurementNotification(RequestItem $requestItem, iterable $products, ?string $invoice = null): self
    {
        $productsCollection = collect($products)->filter();
        $count = $productsCollection->count();

        if ($count <= 1 && $productsCollection->isNotEmpty()) {
            return self::sendProcurementNotification($requestItem, $productsCollection->first(), $invoice);
        }

        $names = $productsCollection->map(fn($p) => is_object($p) ? $p->name : (string)$p)->values();
        $firstTwo = $names->take(2)->join(', ');
        $remainingCount = $count - 2;
        $summaryText = $remainingCount > 0 ? "{$firstTwo}, dan {$remainingCount} barang lainnya" : $names->join(', ');

        // Cegah duplikasi per tiket pengadaan
        $existing = self::where('user_id', $requestItem->user_id)
            ->where('request_id', $requestItem->id)
            ->where('type', 'procurement_available')
            ->where('is_read', false)
            ->first();

        if ($existing) {
            $existing->update([
                'title' => "{$count} Barang Usulan Telah Tersedia",
                'message' => "Barang pada usulan #{$requestItem->request_code} ({$summaryText}) telah dibeli Subbag TU dan kini tersedia di persediaan ATK.",
                'data' => [
                    'product_names' => $names->toArray(),
                    'total_items' => $count,
                    'invoice' => $invoice,
                ],
            ]);
            return $existing;
        }

        return self::create([
            'user_id' => $requestItem->user_id,
            'title' => "{$count} Barang Usulan Telah Tersedia",
            'message' => "Barang pada usulan #{$requestItem->request_code} ({$summaryText}) telah dibeli Subbag TU dan kini tersedia di persediaan ATK.",
            'type' => 'procurement_available',
            'icon' => 'box',
            'link' => route('katalog-atk'),
            'request_id' => $requestItem->id,
            'product_id' => $productsCollection->first()->id ?? null,
            'is_read' => false,
            'data' => [
                'product_names' => $names->toArray(),
                'total_items' => $count,
                'invoice' => $invoice,
            ],
        ]);
    }

    /**
     * Kirim notifikasi ke Petugas Gudang (Pemeriksa Laporan Persediaan)
     * saat usulan pengadaan disetujui oleh Kasubbag TU
     */
    public static function sendProcurementApprovedToExaminers(RequestItem $requestItem): void
    {
        // Cari pegawai gudang yang ditugaskan memeriksa laporan persediaan
        $examiners = User::where('role', 'pegawai_gudang')
            ->where('is_report_examiner', true)
            ->get();

        // Jika belum ada yang ditugaskan secara spesifik, kirimkan ke semua pegawai gudang
        if ($examiners->isEmpty()) {
            $examiners = User::where('role', 'pegawai_gudang')->get();
        }

        if ($examiners->isEmpty()) {
            return;
        }

        $pemohonName = $requestItem->user 
            ? ($requestItem->user->name_with_title ?: $requestItem->user->name) 
            : 'Pegawai';
        $unitKerja = $requestItem->user 
            ? ($requestItem->user->position ?? ($requestItem->type === 'subdit' ? 'Subdirektorat' : 'Pegawai')) 
            : 'Pegawai';

        $approvedDetails = $requestItem->details->filter(function ($d) {
            return $d->approved_quantity > 0 || (is_null($d->approved_quantity) && $d->quantity > 0);
        });

        $itemNames = $approvedDetails->map(function ($d) {
            $name = $d->product ? $d->product->name : ($d->custom_product_name ?: 'Barang');
            $qty = $d->approved_quantity ?: $d->quantity;
            $unit = $d->product ? ($d->product->unit ?: 'Stock') : ($d->custom_unit ?: 'Stock');
            return "{$name} ({$qty} {$unit})";
        })->values();

        $itemSummary = $itemNames->take(2)->join(', ');
        if ($itemNames->count() > 2) {
            $itemSummary .= ' (+' . ($itemNames->count() - 2) . ' barang lainnya)';
        } elseif ($itemNames->isEmpty()) {
            $itemSummary = 'Barang ATK';
        }

        $title = 'Usulan Pengadaan Disetujui Kasubbag TU';
        $message = "Kasubbag TU telah menyetujui usulan pengadaan #{$requestItem->request_code} dari {$pemohonName} ({$itemSummary}). Silakan lakukan penambahan stok barang di Master Data.";
        $link = route('products.index') . '#pengadaan-section';

        foreach ($examiners as $examiner) {
            $existing = self::where('user_id', $examiner->id)
                ->where('request_id', $requestItem->id)
                ->where('type', 'procurement_approved_examiner')
                ->where('is_read', false)
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                    'data' => [
                        'request_code' => $requestItem->request_code,
                        'requester' => $pemohonName,
                        'unit_name' => $unitKerja,
                        'item_summary' => $itemSummary,
                        'total_items' => $approvedDetails->count(),
                    ],
                ]);
            } else {
                self::create([
                    'user_id' => $examiner->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'procurement_approved_examiner',
                    'icon' => 'box',
                    'link' => $link,
                    'request_id' => $requestItem->id,
                    'product_id' => $approvedDetails->first()->product_id ?? null,
                    'is_read' => false,
                    'data' => [
                        'request_code' => $requestItem->request_code,
                        'requester' => $pemohonName,
                        'unit_name' => $unitKerja,
                        'item_summary' => $itemSummary,
                        'total_items' => $approvedDetails->count(),
                    ],
                ]);
            }
        }
    }
}
