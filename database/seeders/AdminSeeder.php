<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Role: Kasubbag TU (Approver & Pimpinan)
        User::updateOrCreate(
            ['email' => 'utami@gmail.com'],
            [
                'name' => 'Utami M',
                'position' => 'Kasubbag TU',
                'nip' => '19850612 201001 2 003',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'kasubag_tu',
            ]
        );

        // Sinkronisasi Katalog Standar ATK (Lembar 2) - 56 Kategori & 572 Barang ATK
        $this->call(Lembar2AtkCatalogSeeder::class);
    }
}
