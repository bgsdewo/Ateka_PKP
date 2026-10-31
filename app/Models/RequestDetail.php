<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    protected $fillable = [
        'request_id',
        'product_id',
        'custom_product_name',
        'custom_unit',
        'specification',
        'quantity',
        'approved_quantity',
        'verification_note',
        'is_fulfilled',
        'fulfilled_at',
    ];

    protected $casts = [
        'is_fulfilled' => 'boolean',
        'fulfilled_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class, 'request_id');
    }

    /**
     * Nama barang (mengutamakan master product jika ada, atau nama barang custom)
     */
    public function getItemNameAttribute(): string
    {
        if ($this->product) {
            return $this->product->name;
        }
        return $this->custom_product_name ?: 'Barang Permintaan';
    }

    /**
     * Satuan barang
     */
    public function getItemUnitAttribute(): string
    {
        if (!empty($this->custom_unit)) {
            return $this->custom_unit;
        }

        if ($this->product && !empty($this->product->unit)) {
            return $this->product->unit;
        }

        return 'Pcs';
    }

    /**
     * Jumlah yang berlaku setelah verifikasi (jika sudah disetujui, pakai approved_quantity, default ke quantity)
     */
    public function getEffectiveQuantityAttribute(): int
    {
        return $this->approved_quantity !== null ? (int) $this->approved_quantity : (int) $this->quantity;
    }
}
