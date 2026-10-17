<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRegistrationAndResetPasswordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_page_displays_nip_field_and_does_not_display_subdit_role(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Nomor Induk Pegawai (NIP)');
        $response->assertSee('Pegawai');
        $response->assertDontSee('subdit_choice');
        $response->assertDontSee('Pemohon Kolektif');
    }

    public function test_register_rejects_subdit_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Admin Subdit SDK Baru',
            'email' => 'subdit_sdk_baru@perumahan.go.id',
            'nip' => '19950510 202012 1 002',
            'role' => 'subdit',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', [
            'email' => 'subdit_sdk_baru@perumahan.go.id',
        ]);
    }

    public function test_register_pegawai_stores_position_and_nip(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pegawai Baru KemenPKP',
            'email' => 'pegawai_baru@perumahan.go.id',
            'nip' => '19980817 202301 1 005',
            'role' => 'pegawai',
            'position' => 'Analis Kebijakan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'pegawai_baru@perumahan.go.id',
            'role' => 'pegawai',
            'position' => 'Analis Kebijakan',
            'nip' => '19980817 202301 1 005',
        ]);
    }

    public function test_login_page_displays_forgot_password_link(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee(route('password.forgot'));
        $response->assertSee('Lupa Password?');
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get(route('password.forgot'));

        $response->assertStatus(200);
        $response->assertSee('Reset Kata Sandi');
        $response->assertSee('Nomor Induk Pegawai (NIP)');
        $response->assertSee('Simpan & Perbarui Password', false);
    }

    public function test_reset_password_succeeds_with_matching_email_and_nip(): void
    {
        $user = User::create([
            'name' => 'Tester Lupa Password',
            'email' => 'tester_lupa@perumahan.go.id',
            'nip' => '19930415 201802 1 009',
            'position' => 'Staf Evaluasi',
            'role' => 'pegawai',
            'password' => Hash::make('password_lama'),
        ]);

        // Kirim permintaan reset password dengan NIP berformat (misal tanpa spasi)
        $response = $this->post(route('password.reset.nip'), [
            'email' => 'tester_lupa@perumahan.go.id',
            'nip' => '199304152018021009',
            'password' => 'password_baru_123',
            'password_confirmation' => 'password_baru_123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('password_baru_123', $user->password));
    }

    public function test_reset_password_fails_when_nip_does_not_match(): void
    {
        $user = User::create([
            'name' => 'Tester Salah NIP',
            'email' => 'tester_salah_nip@perumahan.go.id',
            'nip' => '19930415 201802 1 009',
            'position' => 'Staf Evaluasi',
            'role' => 'pegawai',
            'password' => Hash::make('password_lama'),
        ]);

        $response = $this->post(route('password.reset.nip'), [
            'email' => 'tester_salah_nip@perumahan.go.id',
            'nip' => '199999999999999999',
            'password' => 'password_baru_123',
            'password_confirmation' => 'password_baru_123',
        ]);

        $response->assertSessionHas('error');

        $user->refresh();
        $this->assertTrue(Hash::check('password_lama', $user->password));
    }
}
