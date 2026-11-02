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
     * Kirim notifikasi ke Petugas Gudang Distribusi saat barang pengadaan telah diisi stoknya
     * oleh Petugas Pemeriksa Gudang dan siap untuk diserahterimakan ke pemohon.
     */
    public static function sendProcurementNotification(RequestItem $requestItem, Product $product, ?string $invoice = null): ?self
    {
        $pemohonName = $requestItem->user 
            ? ($requestItem->user->name_with_title ?: $requestItem->user->name) 
            : 'Pemohon';

        $title = 'Barang Pengadaan Siap Didistribusikan!';
        $message = "Barang pengadaan \"{$product->name}\" untuk permohonan #{$requestItem->request_code} ({$pemohonName}) telah diisi stoknya oleh Petugas Pemeriksa Gudang dan siap diserahterimakan.";

        // Cari Petugas Gudang Distribusi
        $distributors = User::where('role', 'pegawai_gudang')
            ->where('is_distributor', true)
            ->get();

        if ($distributors->isEmpty()) {
            $distributors = User::where('role', 'pegawai_gudang')->get();
        }

        if ($distributors->isEmpty()) {
            return null;
        }

        $lastNotif = null;
        foreach ($distributors as $distributor) {
            $existing = self::where('user_id', $distributor->id)
                ->where('request_id', $requestItem->id)
                ->where('product_id', $product->id)
                ->where('type', 'procurement_ready_distribution')
                ->where('is_read', false)
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'message' => $message,
                    'link' => route('distribusi.index'),
                    'data' => [
                        'product_name' => $product->name,
                        'request_code' => $requestItem->request_code,
                        'requester' => $pemohonName,
                        'invoice' => $invoice,
                    ],
                ]);
                $existing->touch();
                $lastNotif = $existing;
            } else {
                $lastNotif = self::create([
                    'user_id' => $distributor->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'procurement_ready_distribution',
                    'icon' => 'truck',
                    'link' => route('distribusi.index'),
                    'request_id' => $requestItem->id,
                    'product_id' => $product->id,
                    'is_read' => false,
                    'data' => [
                        'product_name' => $product->name,
                        'request_code' => $requestItem->request_code,
                        'requester' => $pemohonName,
                        'invoice' => $invoice,
                    ],
                ]);
            }
        }

        return $lastNotif;
    }

    /**
     * Kirim notifikasi pengadaan gabungan (konsolidasi) ke Petugas Gudang Distribusi jika satu tiket memiliki banyak item barang
     */
    public static function sendConsolidatedProcurementNotification(RequestItem $requestItem, iterable $products, ?string $invoice = null): ?self
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

        $pemohonName = $requestItem->user 
            ? ($requestItem->user->name_with_title ?: $requestItem->user->name) 
            : 'Pemohon';

        $title = "{$count} Barang Pengadaan Siap Didistribusikan!";
        $message = "Barang pengadaan untuk permohonan #{$requestItem->request_code} ({$pemohonName}: {$summaryText}) telah tersedia di gudang dan siap diserahterimakan.";

        $distributors = User::where('role', 'pegawai_gudang')
            ->where('is_distributor', true)
            ->get();

        if ($distributors->isEmpty()) {
            $distributors = User::where('role', 'pegawai_gudang')->get();
        }

        if ($distributors->isEmpty()) {
            return null;
        }

        $lastNotif = null;
        foreach ($distributors as $distributor) {
            $existing = self::where('user_id', $distributor->id)
                ->where('request_id', $requestItem->id)
                ->where('type', 'procurement_ready_distribution')
                ->where('is_read', false)
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'message' => $message,
                    'link' => route('distribusi.index'),
                    'data' => [
                        'product_names' => $names->toArray(),
                        'total_items' => $count,
                        'requester' => $pemohonName,
                        'request_code' => $requestItem->request_code,
                        'invoice' => $invoice,
                    ],
                ]);
                $lastNotif = $existing;
            } else {
                $lastNotif = self::create([
                    'user_id' => $distributor->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => 'procurement_ready_distribution',
                    'icon' => 'truck',
                    'link' => route('distribusi.index'),
                    'request_id' => $requestItem->id,
                    'product_id' => $productsCollection->first()->id ?? null,
                    'is_read' => false,
                    'data' => [
                        'product_names' => $names->toArray(),
                        'total_items' => $count,
                        'requester' => $pemohonName,
                        'request_code' => $requestItem->request_code,
                        'invoice' => $invoice,
                    ],
                ]);
            }
        }

        return $lastNotif;
    }

    /**
     * Kirim notifikasi ke Petugas Gudang (Pemeriksa Laporan Persediaan)
     * saat usulan pengadaan disetujui oleh Kasubbag TU
     */
    public static function sendProcurementApprovedToExaminers(RequestItem $requestItem): void
    {
        // Kirimkan ke seluruh staf pegawai gudang yang memeriksa barang
        $examiners = User::where('role', 'pegawai_gudang')->get();

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
        $link = route('master-data.daftar-barang.index') . '#pengadaan-section';

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

    /**
     * Kirim notifikasi ke seluruh staf Pegawai Gudang saat permintaan stok persediaan disetujui oleh Kasubbag TU
     */
    public static function sendStockApprovedToWarehouse(RequestItem $requestItem): void
    {
        $warehouseUsers = User::where('role', 'pegawai_gudang')->get();
        if ($warehouseUsers->isEmpty()) {
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

        $title = 'Permintaan Barang Disetujui Kasubbag TU';
        $message = "Kasubbag TU telah menyetujui permintaan ATK #{$requestItem->request_code} dari {$pemohonName} ({$itemSummary}). Barang siap disiapkan dan diserahkan ke pemohon.";
        $link = route('distribusi.index');

        foreach ($warehouseUsers as $user) {
            self::updateOrCreate([
                'user_id' => $user->id,
                'request_id' => $requestItem->id,
                'type' => 'stock_approved_warehouse',
            ], [
                'title' => $title,
                'message' => $message,
                'icon' => 'truck',
                'link' => $link,
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
