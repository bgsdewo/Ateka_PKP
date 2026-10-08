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

        // Akun-akun dummy sebelumnya dinonaktifkan karena sistem beralih ke data asli pengguna.
        // Hanya akun Kasubag TU yang dipertahankan.
        /*
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

        // 3. Role: PIC Distribusi (4 Petugas PIC Distribusi Unit/Divisi)
        // 3A. PIC Distribusi SDK - Fairuz Armina Nur Azizah, S.T.
        User::updateOrCreate(
            ['email' => 'fairuz@gmail.com'],
            [
                'name' => 'Fairuz Armina Nur Azizah, S.T.',
                'position' => 'PIC Distribusi Subdirektorat SDK',
                'nip' => '200202262025062007',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 3B. PIC Distribusi PJF - Desi
        User::updateOrCreate(
            ['email' => 'desi@gmail.com'],
            [
                'name' => 'Desi',
                'position' => 'PIC Distribusi Subdirektorat PJF',
                'nip' => '19960412 202101 2 005',
                'subdit_name' => 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 3C. PIC Distribusi KBR - Hira
        User::updateOrCreate(
            ['email' => 'hira@gmail.com'],
            [
                'name' => 'Hira',
                'position' => 'PIC Distribusi Subdirektorat KBR',
                'nip' => '19970928 202203 2 006',
                'subdit_name' => 'Subdirektorat Kendala Bangunan Rumah',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 3D. PIC Distribusi Subbag TU - Devi Ismiyanti
        User::updateOrCreate(
            ['email' => 'pic.tu@gmail.com'],
            [
                'name' => 'Devi Ismiyanti',
                'position' => 'PIC Distribusi Subbagian Tata Usaha',
                'nip' => '19900815 201503 2 004',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'pic_distribusi',
            ]
        );

        // 4. Role: Subdirektorat (Pemohon Kolektif - Akses Murni Requester Sama Seperti Pegawai)
        // 4A. SUBDIT SDK
        User::updateOrCreate(
            ['email' => 'sdk@gmail.com'],
            [
                'name' => 'Subdirektorat SDK',
                'position' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'nip' => '19940115 201902 1 001',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4B. SUBDIT PJF
        User::updateOrCreate(
            ['email' => 'pjf@gmail.com'],
            [
                'name' => 'Subdirektorat PJF',
                'position' => 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi',
                'nip' => '19930720 201801 1 002',
                'subdit_name' => 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4C. SUBDIT TU (Subbag TU Pemohon Kolektif)
        User::updateOrCreate(
            ['email' => 'tu@gmail.com'],
            [
                'name' => 'Subbagian Tata Usaha',
                'position' => 'Subbagian Tata Usaha',
                'nip' => '19920310 201603 2 003',
                'subdit_name' => 'Subbagian Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 4D. SUBDIT KBR
        User::updateOrCreate(
            ['email' => 'kbr@gmail.com'],
            [
                'name' => 'Subdirektorat KBR',
                'position' => 'Subdirektorat Kendala Bangunan Rumah',
                'nip' => '19950825 202012 1 004',
                'subdit_name' => 'Subdirektorat Kendala Bangunan Rumah',
                'password' => Hash::make('password123'),
                'role' => 'subdit',
            ]
        );

        // 5. Role: Pegawai Perorangan (Pemohon Individu - Akses Murni Requester)
        User::updateOrCreate(
            ['email' => 'pegawai@gmail.com'],
            [
                'name' => 'Pegawai Perorangan',
                'position' => 'Pegawai Staf Perorangan',
                'nip' => '19950510 202012 1 002',
                'subdit_name' => 'Pegawai Perorangan',
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
            ]
        );
        */

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
