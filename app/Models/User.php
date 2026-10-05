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

    public function isPegawai(): bool
    {
        return in_array($this->role, ['pegawai', 'subdit', 'member']) || empty($this->role);
    }

    public function getRoleTitleAttribute(): string
    {
        return match ($this->role) {
            'kasubag_tu', 'admin' => 'Kasubag TU (Approver / Pimpinan)',
            'arsiparis' => 'Arsiparis (Pelaporan & Administrasi)',
            'pic_distribusi' => 'PIC Distribusi (Pengelola / Gudang)',
            'subdit' => 'Perwakilan Subdirektorat (Requester)',
            default => 'Pegawai / Subdit (Requester / Pemohon)',
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
}
