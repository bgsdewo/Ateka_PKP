<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'stock_after',
        'transaction_date',

        'recipient_type', // 'subdit', 'individu', or null

        // --- Konversi Kemasan Pembelian ---
        'satuan_beli',
        'jumlah_beli',
        'isi_per_kemasan',

        // --- Kolom Baru Tambahan ---
        'no_faktur',
        'uraian',
        'kondisi',
        'harga_perolehan',

        'note',
    ];

    public function getIsSubditAttribute(): bool
    {
        return $this->recipient_type === 'subdit' || str_contains(strtolower($this->note ?? ''), 'subdit');
    }

    public function getIsIndividuAttribute(): bool
    {
        return $this->type === 'keluar' && !$this->is_subdit;
    }

    public function getRecipientLabelAttribute(): string
    {
        if ($this->type !== 'keluar') {
            return '-';
        }
        return $this->is_subdit ? 'Subdit (Kolektif)' : 'Pegawai (Individu)';
    }

    public function getHargaSatuanAttribute(): float
    {
        if ($this->harga_perolehan > 0) {
            return (float) $this->harga_perolehan;
        }
        return (float) ($this->product->purchase_price ?? 0);
    }

    public function getNilaiSaldoAttribute(): float
    {
        return (int) $this->stock_after * $this->harga_satuan;
    }

    protected $casts = [
        'transaction_date' => 'date',
        'harga_perolehan' => 'decimal:2', // Opsional, agar angka desimal formatnya rapi
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class, 'no_faktur', 'request_code');
    }

    public function getRecipientNameAttribute(): string
    {
        if ($this->type !== 'keluar') {
            return '-';
        }

        // 1. Cek dari nama penerima fisik hasil serah terima distribusi
        if ($this->requestItem && !empty($this->requestItem->receiver_name)) {
            return $this->requestItem->receiver_name;
        }

        // 2. Cek uraian penerima
        if (!empty($this->uraian)) {
            return $this->uraian;
        }

        // 3. Cek nama pemohon di tiket RequestItem
        if ($this->requestItem && $this->requestItem->user) {
            $pos = $this->requestItem->user->position ? ' (' . $this->requestItem->user->position . ')' : '';
            return $this->requestItem->user->name . $pos;
        }

        // 4. Coba ekstrak nama dari catatan transaksi pengeluaran
        if (preg_match('/Pengeluaran.*?\buntuk\s+(.*?)\s+\[/i', $this->note ?? '', $m)) {
            return trim($m[1]);
        }

        return '-';
    }

    public function getHandedOverByAttribute(): ?string
    {
        if ($this->requestItem && $this->requestItem->distributor) {
            return $this->requestItem->distributor->name_with_title ?? $this->requestItem->distributor->name;
        }
        return null;
    }

    public function getFormattedNoteAttribute(): string
    {
        $productName = $this->product->name ?? 'Barang';

        if ($this->type === 'masuk') {
            return 'Pembelian ' . $productName;
        }

        if ($this->type === 'keluar') {
            $recipient = $this->recipient_name;
            if (empty($recipient) || $recipient === '-') {
                if ($this->requestItem && $this->requestItem->user) {
                    $recipient = $this->requestItem->user->name;
                } elseif ($this->user) {
                    $recipient = $this->user->name;
                } else {
                    $recipient = 'Pegawai';
                }
            } else {
                $recipient = trim(preg_replace('/\s*\[.*?\]/', '', $recipient));
            }
            return 'Pengeluaran ' . $productName . ' Untuk ' . $recipient;
        }

        return $this->attributes['note'] ?? '-';
    }
}

