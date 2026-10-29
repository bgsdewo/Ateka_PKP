<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagTu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            [
                'name' => 'Utami M',
                'position' => 'Kasubbag TU',
                'nip' => '19850612 201001 2 003',
                'password' => bcrypt('password123'),
                'role' => 'kasubag_tu',
            ]
        );
    }

    public function test_kasubag_tu_can_view_user_management_page(): void
    {
        $response = $this->actingAs($this->kasubagTu)
            ->get(route('master-data.manajemen-pengguna.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Pengguna');
        $response->assertSee('Master Data Akun Pengguna');
        $response->assertSee('pic_unit_choice');
        $response->assertSee('Pilih Subdirektorat / Unit Kerja PIC');
    }

    public function test_kasubag_tu_can_add_pic_users_for_all_4_units_with_nip(): void
    {
        $units = [
            'TU' => [
                'name' => 'Petugas PIC TU',
                'email' => 'pic.tu.test@perumahan.go.id',
                'nip' => '19900101 201501 2 001',
                'expected_position' => 'PIC Distribusi Subbagian Tata Usaha',
                'expected_subdit' => 'Subbagian Tata Usaha',
            ],
            'KBR' => [
                'name' => 'Petugas PIC KBR',
                'email' => 'pic.kbr.test@perumahan.go.id',
                'nip' => '19900202 201502 2 002',
                'expected_position' => 'PIC Distribusi Subdirektorat KBR',
                'expected_subdit' => 'Subdirektorat Kendala Bangunan Rumah',
            ],
            'SDK' => [
                'name' => 'Petugas PIC SDK',
                'email' => 'pic.sdk.test@perumahan.go.id',
                'nip' => '19900303 201503 2 003',
                'expected_position' => 'PIC Distribusi Subdirektorat SDK',
                'expected_subdit' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
            ],
            'PJF' => [
                'name' => 'Petugas PIC PJF',
                'email' => 'pic.pjf.test@perumahan.go.id',
                'nip' => '19900404 201504 2 004',
                'expected_position' => 'PIC Distribusi Subdirektorat PJF',
                'expected_subdit' => 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi',
            ],
        ];

        foreach ($units as $unitCode => $data) {
            $response = $this->actingAs($this->kasubagTu)
                ->post(route('master-data.manajemen-pengguna.store'), [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'nip' => $data['nip'],
                    'role' => 'pic_distribusi',
                    'pic_unit_choice' => $unitCode,
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                ]);

            $response->assertRedirect(route('master-data.manajemen-pengguna.index'));
            $response->assertSessionHas('success');

            $this->assertDatabaseHas('users', [
                'email' => $data['email'],
                'nip' => $data['nip'],
                'role' => 'pic_distribusi',
                'position' => $data['expected_position'],
                'subdit_name' => $data['expected_subdit'],
            ]);
        }
    }

    public function test_add_user_requires_nip(): void
    {
        $response = $this->actingAs($this->kasubagTu)
            ->post(route('master-data.manajemen-pengguna.store'), [
                'name' => 'User Tanpa NIP',
                'email' => 'tanpa_nip@perumahan.go.id',
                'role' => 'pic_distribusi',
                'pic_unit_choice' => 'SDK',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertSessionHasErrors(['nip']);
        $this->assertDatabaseMissing('users', ['email' => 'tanpa_nip@perumahan.go.id']);
    }

    public function test_newly_added_pic_can_reset_password_with_their_nip(): void
    {
        // Tambahkan akun PIC baru oleh Kasubag TU
        $this->actingAs($this->kasubagTu)
            ->post(route('master-data.manajemen-pengguna.store'), [
                'name' => 'PIC KBR Reset Test',
                'email' => 'pic.kbr.reset@perumahan.go.id',
                'nip' => '19940712 201901 1 007',
                'role' => 'pic_distribusi',
                'pic_unit_choice' => 'KBR',
                'password' => 'password_lama_123',
                'password_confirmation' => 'password_lama_123',
            ]);

        $createdPic = User::where('email', 'pic.kbr.reset@perumahan.go.id')->firstOrFail();
        $this->assertEquals('PIC Distribusi Subdirektorat KBR', $createdPic->position);

        // Simulasi PIC logout dan lupa password
        auth()->logout();

        $response = $this->post(route('password.reset.nip'), [
            'email' => 'pic.kbr.reset@perumahan.go.id',
            'nip' => '199407122019011007', // tanpa spasi tetap valid
            'password' => 'password_baru_pic_456',
            'password_confirmation' => 'password_baru_pic_456',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $createdPic->refresh();
        $this->assertTrue(Hash::check('password_baru_pic_456', $createdPic->password));
    }

    public function test_non_kasubag_cannot_access_user_management(): void
    {
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai_biasa@perumahan.go.id'],
            ['name' => 'Pegawai Biasa', 'nip' => '19990101 202201 1 001', 'role' => 'pegawai', 'password' => bcrypt('password123')]
        );

        $response = $this->actingAs($pegawai)
            ->get(route('master-data.manajemen-pengguna.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_kasubag_tu_can_add_pegawai_gudang_without_nip(): void
    {
        $response = $this->actingAs($this->kasubagTu)
            ->post(route('master-data.manajemen-pengguna.store'), [
                'name' => 'Staf Gudang Tanpa NIP',
                'email' => 'gudang.tanpanip@perumahan.go.id',
                'nip' => '',
                'role' => 'pegawai_gudang',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('master-data.manajemen-pengguna.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'gudang.tanpanip@perumahan.go.id',
            'role' => 'pegawai_gudang',
            'nip' => null,
            'position' => 'Pegawai Gudang',
        ]);
    }

    public function test_kasubag_tu_can_add_pegawai_gudang_with_nip(): void
    {
        $response = $this->actingAs($this->kasubagTu)
            ->post(route('master-data.manajemen-pengguna.store'), [
                'name' => 'Staf Gudang Ber-NIP',
                'email' => 'gudang.ber.nip@perumahan.go.id',
                'nip' => '19951212 202101 1 005',
                'role' => 'pegawai_gudang',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect(route('master-data.manajemen-pengguna.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'gudang.ber.nip@perumahan.go.id',
            'role' => 'pegawai_gudang',
            'nip' => '19951212 202101 1 005',
            'position' => 'Pegawai Gudang',
        ]);
    }

    public function test_pegawai_gudang_permissions_and_restrictions(): void
    {
        $pegawaiGudang = User::firstOrCreate(
            ['email' => 'gudang.staff@perumahan.go.id'],
            [
                'name' => 'Budi Gudang',
                'role' => 'pegawai_gudang',
                'nip' => null,
                'position' => 'Pegawai Gudang',
                'password' => bcrypt('password123'),
            ]
        );

        // 1. Dilarang akses halaman terbatas
        $this->actingAs($pegawaiGudang)
            ->get(route('master-data.manajemen-pengguna.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pegawaiGudang)
            ->get(route('persetujuan.index'))
            ->assertRedirect(route('dashboard'));

        // 2. Berhak akses distribusi, stok barang masuk, barang keluar, daftar barang, dan laporan
        $this->actingAs($pegawaiGudang)
            ->get(route('distribusi.index'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('barang-masuk'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('barang-keluar'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('master-data.daftar-barang.index'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('reports.index'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('reports.rekapitulasi'))
            ->assertStatus(200);

        $this->actingAs($pegawaiGudang)
            ->get(route('reports.berita-acara'))
            ->assertStatus(200);

        // 3. Pegawai biasa dilarang menambah barang master
        $pegawaiBiasa = User::factory()->create(['role' => 'pegawai']);
        $storeResponse = $this->actingAs($pegawaiBiasa)
            ->post(route('master-data.daftar-barang.store'), [
                'name' => 'Coba Tambah',
                'code' => 'BRG-COBA-999',
            ]);
        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('error');
    }

    public function test_pic_distribusi_permissions_and_restrictions(): void
    {
        $pic = User::firstOrCreate(
            ['email' => 'pic.test@perumahan.go.id'],
            [
                'name' => 'PIC Test',
                'role' => 'pic_distribusi',
                'nip' => null,
                'position' => 'PIC Distribusi',
                'password' => bcrypt('password123'),
            ]
        );

        // 1. Dilarang akses Gudang & Stok, Laporan & Arsip, Master Data, dan Distribusi Barang
        $this->actingAs($pic)
            ->get(route('distribusi.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('barang-masuk'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('barang-keluar'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('reports.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('reports.rekapitulasi'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('reports.berita-acara'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('master-data.kategori-barang.index'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($pic)
            ->get(route('master-data.daftar-barang.index'))
            ->assertRedirect(route('dashboard'));

        // 2. Berhak akses Dashboard & Permintaan ATK
        $this->actingAs($pic)
            ->get(route('dashboard'))
            ->assertStatus(200);

        $this->actingAs($pic)
            ->get(route('katalog-atk'))
            ->assertStatus(200);

        $this->actingAs($pic)
            ->get(route('pengajuan.create'))
            ->assertStatus(200);

        $this->actingAs($pic)
            ->get(route('pengajuan.index'))
            ->assertStatus(200);
    }
}
