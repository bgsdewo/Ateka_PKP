<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'unit',
        'stock',
        'minimum_stock',
        'purchase_price', // <--- Tambahkan baris ini
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function getStockStatusAttribute()
    {
        return $this->stock < 10 ? 'Akan Habis' : 'Aman';
    }
}
