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
        // 1. Role: Kasubbag TU (Approver & Procurement)
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

        // 2. Role: Pegawai (Requester)
        User::updateOrCreate(
            ['email' => 'tes@pkp.com'],
            [
                'name' => 'tes',
                'position' => 'Pegawai Staf',
                'nip' => '123455678910',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        // 3. Role: Pegawai Gudang - Distributor
        User::updateOrCreate(
            ['email' => 'irvan@pkp.com'],
            [
                'name' => 'Irvan Fauzan',
                'position' => 'Petugas Gudang',
                'nip' => '199201012015011001',
                'subdit_name' => 'Gudang Persediaan',
                'password' => Hash::make('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => true,
                'is_report_examiner' => false,
            ]
        );

        // 4. Role: Pegawai Gudang - Pemeriksa Laporan
        User::updateOrCreate(
            ['email' => 'devi@pkp.com'],
            [
                'name' => 'Devi ismiyantati',
                'position' => 'Petugas Gudang / Pemeriksa',
                'nip' => '199302022016022002',
                'subdit_name' => 'Gudang Persediaan',
                'password' => Hash::make('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => false,
                'is_report_examiner' => true,
            ]
        );

        // Sinkronisasi Katalog Standar ATK (Lembar 2) - 56 Kategori & 572 Barang ATK
        $this->call(Lembar2AtkCatalogSeeder::class);
    }
}
