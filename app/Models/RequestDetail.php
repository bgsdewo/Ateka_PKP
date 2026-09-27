<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    protected $fillable = [
        'request_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class, 'request_id');
    }
}
