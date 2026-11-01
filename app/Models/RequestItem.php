<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'distribution_proof',
        'signed_proof',
        'signed_at',
        'signed_by',
        'created_at',
    ];

    protected $casts = [
        'is_procurement' => 'boolean',
        'verified_at' => 'datetime',
        'distributed_at' => 'datetime',
        'purchase_date' => 'date',
        'signed_at' => 'datetime',
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

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function details()
    {
        return $this->hasMany(RequestDetail::class, 'request_id');
    }

    /**
     * Scope untuk memfilter permohonan sesuai dengan divisi PIC Distribusi
     */
    public function scopeForPic($query, User $picUser)
    {
        // Kasubbag TU & Pegawai Gudang yang ditugaskan sebagai distributor mengelola seluruh permohonan distribusi fisik
        if ($picUser->isKasubagTU() || ($picUser->isPegawaiGudang() && $picUser->isDistributor())) {
            return $query;
        }

        // Pegawai Gudang yang TIDAK ditugaskan oleh Kasubbag TU tidak menerima antrean distribusi
        if ($picUser->isPegawaiGudang() && !$picUser->isDistributor()) {
            return $query->whereRaw('1 = 0');
        }

        if ($picUser->isPicTataUsaha()) {
            return $query->where(function ($q) {
                $q->where('type', 'individu')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('role', 'pegawai')
                         ->orWhere(function ($subQ) {
                             $subQ->where('role', 'subdit')
                                  ->where(function ($tuQ) {
                                      $tuQ->where('subdit_name', 'like', '%tata usaha%')
                                          ->orWhere('subdit_name', 'like', '%subbagian tu%')
                                          ->orWhere('position', 'like', '%tata usaha%')
                                          ->orWhere('position', 'like', '%subbagian tu%')
                                          ->orWhere('name', 'like', '%tata usaha%')
                                          ->orWhere('name', 'like', '%subbagian tu%');
                                  });
                         });
                  });
            });
        }

        $key = $picUser->getDivisionKey();

        return $query->where('type', 'subdit')
            ->whereHas('user', function ($uq) use ($key, $picUser) {
                $uq->where('role', '!=', 'pegawai')
                   ->where(function ($subQ) use ($key, $picUser) {
                       if ($key === 'sdk') {
                           $subQ->where('subdit_name', 'like', '%sumber daya%')
                                ->orWhere('subdit_name', 'like', '%sdk%')
                                ->orWhere('position', 'like', '%sdk%')
                                ->orWhere('name', 'like', '%sdk%');
                       } elseif ($key === 'pjf') {
                           $subQ->where('subdit_name', 'like', '%fungsional%')
                                ->orWhere('subdit_name', 'like', '%pjf%')
                                ->orWhere('position', 'like', '%pjf%')
                                ->orWhere('name', 'like', '%pjf%');
                       } elseif ($key === 'kbr') {
                           $subQ->where('subdit_name', 'like', '%kendala%')
                                ->orWhere('subdit_name', 'like', '%kbr%')
                                ->orWhere('position', 'like', '%kbr%')
                                ->orWhere('name', 'like', '%kbr%');
                       } else {
                           if (!empty($picUser->subdit_name)) {
                               $subQ->where('subdit_name', $picUser->subdit_name);
                           }
                           if (!empty($picUser->position)) {
                               $subQ->orWhere('position', $picUser->position);
                           }
                       }
                   });
            });
    }

    public function scopeForPicDivision($query, User $picUser)
    {
        return $this->scopeForPic($query, $picUser);
    }

    public function getIsPurchasedAttribute(): bool
    {
        if (!$this->is_procurement) {
            return false;
        }
        return !empty($this->purchase_invoice) || !empty($this->purchase_date);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi TU',
            'approved' => $this->is_procurement
                ? ($this->is_purchased || ($this->relationLoaded('details') ? $this->details->where('is_fulfilled', true)->isNotEmpty() : $this->details()->where('is_fulfilled', true)->exists())
                    ? 'Disetujui TU (Siap Diserahkan)'
                    : 'Disetujui Kasubbag TU (Dalam Proses Pembelian)')
                : 'Disetujui TU (Siap Diserahkan)',
            'distributed' => 'Selesai (Sudah Diserahkan)',
            'rejected' => 'Ditolak Subbag TU',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-warning text-dark',
            'approved' => ($this->is_procurement && !$this->is_purchased && !($this->relationLoaded('details') ? $this->details->where('is_fulfilled', true)->isNotEmpty() : $this->details()->where('is_fulfilled', true)->exists()))
                ? 'bg-info text-white'
                : 'bg-success text-white',
            'distributed' => 'bg-primary text-white',
            'rejected' => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function getDistributionProofUrlAttribute(): ?string
    {
        if (!$this->distribution_proof) {
            return null;
        }
        return asset('storage/' . $this->distribution_proof);
    }

    public function getSignedProofUrlAttribute(): ?string
    {
        if (!$this->signed_proof) {
            return null;
        }
        return asset('storage/' . $this->signed_proof);
    }

    public function getIsSignedProofPdfAttribute(): bool
    {
        if (!$this->signed_proof) {
            return false;
        }
        return strtolower(pathinfo($this->signed_proof, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Cache request-level pending summary to avoid duplicate queries across Controller, Sidebar, and Navbar.
     */
    public static function getPendingSummary(): array
    {
        static $summary = null;
        if ($summary !== null) {
            return $summary;
        }

        $summary = \Illuminate\Support\Facades\Cache::store('file')->remember('requests_pending_summary', 30, function () {
            $res = DB::table('requests')
                ->where('status', 'pending')
                ->selectRaw("
                    COUNT(*) as total,
                    COALESCE(SUM(CASE WHEN is_procurement = 1 THEN 1 ELSE 0 END), 0) as procurement,
                    COALESCE(SUM(CASE WHEN is_procurement = 0 OR is_procurement IS NULL THEN 1 ELSE 0 END), 0) as stock
                ")
                ->first();

            return [
                'total' => (int) ($res->total ?? 0),
                'procurement' => (int) ($res->procurement ?? 0),
                'stock' => (int) ($res->stock ?? 0),
            ];
        });

        return $summary;
    }

    /**
     * Cache request-level ready distribution count.
     */
    public static function getReadyDistribusiCount(): int
    {
        static $count = null;
        if ($count !== null) {
            return $count;
        }

        $count = (int) \Illuminate\Support\Facades\Cache::store('file')->remember('requests_ready_distribusi_count', 30, function () {
            return (int) DB::table('requests')
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('is_procurement', false)->orWhereNull('is_procurement');
                })
                ->count();
        });

        return $count;
    }
}
