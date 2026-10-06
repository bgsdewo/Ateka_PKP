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
                'position' => 'Kasubag TU',
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
        $response->assertSee('Manajemen Pengguna & Peran (Role)', false);
        $response->assertSee('Master Data Akun Pengguna');
        $response->assertSee('pic_unit_choice');
        $response->assertSee('Perwakilan Divisi PIC (TU, KBR, SDK, PJF)');
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
}
