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
                'nip' => '19850612 201001 2 003',
                'subdit_name' => 'Subbagian Tata Usaha',
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
                'nip' => '19900815 201503 2 004',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'arsiparis',
            ]
        );

        // 3. Role: PIC Distribusi
        User::updateOrCreate(
            ['email' => 'fairuz@gmail.com'],
            [
                'name' => 'Fairuz Armina Nur Azizah, S.T.',
                'position' => 'PIC Distribusi & Rekapitulasi',
                'nip' => '200202262025062007',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 4A. SUBDIT SDK (Subdirektorat Sumber Daya Konstruksi) - PIC: Fairuz Armina Nur Azizah, S.T.
        User::updateOrCreate(
            ['email' => 'sdk@gmail.com'],
            [
                'name' => 'Fairuz Armina Nur Azizah, S.T.',
                'position' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'nip' => '200202262025062007',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4B. SUBDIT PJF (Subdirektorat PJF)
        User::updateOrCreate(
            ['email' => 'pjf@gmail.com'],
            [
                'name' => 'Subdit PJF',
                'position' => 'Subdirektorat PJF',
                'nip' => '-',
                'subdit_name' => 'Subdirektorat PJF',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4C. SUBDIT TU (Subdirektorat / Subbag TU) - PIC: Devi Ismiyanti
        User::updateOrCreate(
            ['email' => 'tu@gmail.com'],
            [
                'name' => 'Devi Ismiyanti',
                'position' => 'Subbagian Tata Usaha',
                'nip' => '19900815 201503 2 004',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4D. SUBDIT KBR (Subdirektorat KBR)
        User::updateOrCreate(
            ['email' => 'kbr@gmail.com'],
            [
                'name' => 'Subdit KBR',
                'position' => 'Subdirektorat KBR',
                'nip' => '-',
                'subdit_name' => 'Subdirektorat KBR',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 5. Role: Pegawai Perorangan (Peminta individu)
        User::updateOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name' => 'Pegawai',
                'position' => 'Pegawai Perorangan',
                'nip' => '19950510 202012 1 002',
                'subdit_name' => 'Pegawai Perorangan',
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
