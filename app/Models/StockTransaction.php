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
}
