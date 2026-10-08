<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'title',
        'email',
        'password',
        'role',
        'position',
        'nip',
        'subdit_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isKasubagTU(): bool
    {
        return in_array($this->role, ['kasubag_tu', 'admin']);
    }

    public function isArsiparis(): bool
    {
        return $this->role === 'arsiparis';
    }

    public function isPicDistribusi(): bool
    {
        return $this->role === 'pic_distribusi';
    }

    /**
     * Cek apakah user adalah PIC Distribusi Subbagian Tata Usaha
     */
    public function isPicTataUsaha(): bool
    {
        if (!$this->isPicDistribusi()) {
            return false;
        }
        $text = strtolower(($this->subdit_name ?? '') . ' ' . ($this->position ?? '') . ' ' . ($this->name ?? ''));
        return str_contains($text, 'tata usaha') || preg_match('/\btu\b/i', $text);
    }

    /**
     * Dapatkan kunci unit/divisi untuk pencocokan (misal: 'sdk', 'pjf', 'kbr', 'tu')
     */
    public function getDivisionKey(): string
    {
        $text = strtolower(($this->subdit_name ?? '') . ' ' . ($this->position ?? '') . ' ' . ($this->name ?? ''));
        if (str_contains($text, 'sdk') || str_contains($text, 'sumber daya')) {
            return 'sdk';
        }
        if (str_contains($text, 'pjf') || str_contains($text, 'fungsional')) {
            return 'pjf';
        }
        if (str_contains($text, 'kbr') || str_contains($text, 'kendala')) {
            return 'kbr';
        }
        if (str_contains($text, 'tata usaha') || preg_match('/\btu\b/i', $text)) {
            return 'tu';
        }
        return trim(strtolower($this->subdit_name ?: ($this->position ?: '')));
    }

    /**
     * Cek apakah PIC ini berhak menangani pengajuan tertentu
     */
    public function canHandleRequest($requestItem): bool
    {
        if (!$this->isPicDistribusi()) {
            return false;
        }

        // Pengajuan individu (Pegawai) selalu ditangani oleh PIC Subbagian Tata Usaha
        if ($requestItem->type === 'individu' || ($requestItem->user && $requestItem->user->role === 'pegawai')) {
            return $this->isPicTataUsaha();
        }

        // Pengajuan kolektif Subdirektorat:
        // Jika PIC Tata Usaha, hanya menangani pengajuan dari Subbag TU
        if ($this->isPicTataUsaha()) {
            $requesterDivision = $requestItem->user ? $requestItem->user->getDivisionKey() : '';
            return $requesterDivision === 'tu';
        }

        // Untuk PIC Subdit lain (SDK, PJF, KBR, dll.):
        // Harus cocok antara divisi PIC dengan divisi pemohon Subdit
        $picDivision = $this->getDivisionKey();
        $requesterDivision = $requestItem->user ? $requestItem->user->getDivisionKey() : '';

        return !empty($picDivision) && $picDivision === $requesterDivision;
    }

    public function isSubdit(): bool
    {
        return $this->role === 'subdit';
    }

    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }

    public function isRequester(): bool
    {
        return in_array($this->role, ['pegawai', 'subdit', 'member']) || empty($this->role);
    }

    public function requests()
    {
        return $this->hasMany(RequestItem::class, 'user_id');
    }

    public function procurementRequests()
    {
        return $this->hasMany(RequestItem::class, 'user_id')->where('is_procurement', true);
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(UserNotification::class)->where('is_read', false)->latest();
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

    public function getRoleTitleAttribute(): string
    {
        return match ($this->role) {
            'kasubag_tu', 'admin' => 'Kasubag TU (Approver / Pimpinan)',
            'arsiparis' => 'Arsiparis (Pelaporan & Administrasi)',
            'pic_distribusi' => 'PIC Distribusi (Pengelola / Gudang)',
            'subdit' => 'Subdirektorat (Pemohon Kolektif)',
            'pegawai' => 'Pegawai (Pemohon Individu)',
            default => 'Pegawai (Pemohon Individu)',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'kasubag_tu', 'admin' => 'bg-primary',
            'arsiparis' => 'bg-info',
            'pic_distribusi' => 'bg-warning text-dark',
            'subdit' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    public function getGelarAttribute(): ?string
    {
        return $this->title;
    }

    public function setGelarAttribute(?string $value): void
    {
        $this->title = $value;
    }

    /**
     * Format nama lengkap beserta gelar (real-time)
     */
    public function getNameWithTitleAttribute(): string
    {
        if (empty($this->title)) {
            return $this->name ?? '';
        }

        $title = trim($this->title);
        $name = trim($this->name ?? '');

        if (str_contains(strtolower($name), strtolower($title))) {
            return $name;
        }

        $prefixes = ['dr.', 'dr', 'drs.', 'drs', 'dra.', 'dra', 'ir.', 'ir', 'prof.', 'prof'];
        $firstWord = strtolower(explode(' ', $title)[0]);

        if (in_array($firstWord, $prefixes)) {
            return $title . ' ' . $name;
        }

        return $name . ', ' . $title;
    }
}
