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
        // 1. Role: Kasubag TU (Approver & Procurement)
        User::updateOrCreate(
            ['email' => 'utami@gmail.com'],
            [
                'name' => 'Utami M',
                'position' => 'Kasubag TU',
                'password' => Hash::make('password123'),
                'role' => 'kasubag_tu',
            ]
        );

        // 2. Role: Arsiparis (Laporan)
        User::updateOrCreate(
            ['email' => 'devi@gmail.com'],
            [
                'name' => 'Devi Ismiyanti',
                'position' => 'Arsiparis Ahli Pertama',
                'password' => Hash::make('password123'),
                'role' => 'arsiparis',
            ]
        );

        // 3. Role: PIC Distribusi / Koordinator Permintaan
        User::updateOrCreate(
            ['email' => 'fairuz@gmail.com'],
            [
                'name' => 'Fairuz',
                'position' => 'PIC Distribusi & Rekapitulasi',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 4. Role: Subdit (Peminta atas nama kelompok)
        User::updateOrCreate(
            ['email' => 'sdk@gmail.com'],
            [
                'name' => 'Subdit SDK',
                'position' => 'Subdirektorat',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 5. Role: Pegawai Perorangan (Peminta individu - Dibuat Akun General)
        User::updateOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name' => 'Pegawai',
                'position' => 'Pegawai Perorangan',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );

        // Daftar Kategori ATK
        $categories = [
            'Amplop', 'Balliner', 'Ballpoint', 'Tinta', 'Batterey Alkaline',
            'Binder', 'Box', 'Buku', 'Clear Holder',
            'Celotif', 'Compact Disc', 'Container CB', 'Cutter',
            'Double Tape', 'Elevated Tray', 'Flashdisk', 'Gunting', 'Isi Stapler',
            'Kamper', 'Kertas HVS', 'Label Sticker', 'Lakband', 'Lem', 'Map', 'Mouse', 'Ordner',
            'Paper Clip', 'Penggaris', 'Penghapus', 'Pensil', 'Pita', 'POST', 'Punch/Pembolong',
            'Stabilo', 'Toner',
        ];

        foreach ($categories as $catName) {
            Category::firstOrCreate(['name' => $catName]);
        }
    }
}
