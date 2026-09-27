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
        'status',
        'notes',
        'rejection_reason',
        'distributed_at',
        'distributed_by',
        'receiver_name',
        'distribution_notes',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function details()
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan Kasubag TU',
            'approved' => 'Disetujui (Menunggu Distribusi)',
            'distributed' => 'Selesai (Sudah Diserahkan)',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-info text-white',
            'distributed' => 'bg-success text-white',
            'rejected' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }
}
