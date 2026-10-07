<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'user_id',
        'request_code',
        'type',
        'is_procurement',
        'status',
        'verified_at',
        'verified_by',
        'verification_notes',
        'purchase_invoice',
        'purchase_date',
        'purchase_price',
        'purchase_notes',
        'notes',
        'rejection_reason',
        'distributed_at',
        'distributed_by',
        'receiver_name',
        'distribution_notes',
        'created_at',
    ];

    protected $casts = [
        'is_procurement' => 'boolean',
        'verified_at' => 'datetime',
        'distributed_at' => 'datetime',
        'purchase_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function details()
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }

    public function getIsPurchasedAttribute(): bool
    {
        if (!$this->is_procurement) {
            return false;
        }
        if (!empty($this->purchase_invoice)) {
            return true;
        }
        return $this->relationLoaded('details')
            ? $this->details->contains(fn($d) => !empty($d->product_id))
            : $this->details()->whereNotNull('product_id')->exists();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => $this->is_procurement ? 'Menunggu Verifikasi Pengadaan' : 'Menunggu Verifikasi Subbag TU',
            'approved' => $this->is_procurement
                ? ($this->is_purchased ? 'Disetujui & Telah Dibeli (Tersedia)' : 'Disetujui TU (Akan Dibeli)')
                : 'Disetujui TU (Siap Diambil)',
            'distributed' => 'Selesai (Sudah Diserahkan)',
            'rejected' => 'Ditolak Subbag TU',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'approved' => ($this->is_procurement && !$this->is_purchased) ? 'bg-info text-white' : 'bg-success text-white',
            'distributed' => 'bg-primary text-white',
            'rejected' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }
}
