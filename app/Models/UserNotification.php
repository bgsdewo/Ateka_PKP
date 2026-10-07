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
     */
    public static function sendProcurementNotification(RequestItem $requestItem, Product $product, ?string $invoice = null): self
    {
        return self::create([
            'user_id' => $requestItem->user_id,
            'title' => 'Barang Telah Tersedia',
            'message' => "Barang \"{$product->name}\" telah dibeli Subbag TU dan kini tersedia di persediaan ATK.",
            'type' => 'procurement_available',
            'icon' => 'box',
            'link' => route('katalog-atk'),
            'request_id' => $requestItem->id,
            'product_id' => $product->id,
            'is_read' => false,
            'data' => [
                'product_name' => $product->name,
            ],
        ]);
    }
}
