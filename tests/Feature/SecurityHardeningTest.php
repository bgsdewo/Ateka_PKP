<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubag;
    protected User $pegawai;
    protected User $pegawaiLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubag = User::create([
            'name' => 'Kasubbag TU Security Test',
            'email' => 'kasubag_sec@perumahan.go.id',
            'nip' => '19850101 201001 1 001',
            'role' => 'kasubag_tu',
            'position' => 'Kasubbag TU',
            'password' => Hash::make('password123'),
        ]);

        $this->pegawai = User::create([
            'name' => 'Pegawai Staf Security Test',
            'email' => 'pegawai_sec@perumahan.go.id',
            'nip' => '19950202 202002 1 002',
            'role' => 'pegawai',
            'position' => 'Staf Evaluasi',
            'password' => Hash::make('password123'),
        ]);

        $this->pegawaiLain = User::create([
            'name' => 'Pegawai Lain Test',
            'email' => 'pegawai_lain@perumahan.go.id',
            'nip' => '19960303 202103 1 003',
            'role' => 'pegawai',
            'position' => 'Staf Pelaksana',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_pegawai_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->pegawai)->get(route('master-data.manajemen-pengguna.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_pegawai_cannot_promote_self_to_kasubag_tu(): void
    {
        $response = $this->actingAs($this->pegawai)->put(route('master-data.manajemen-pengguna.update', $this->pegawai->id), [
            'name' => $this->pegawai->name,
            'email' => $this->pegawai->email,
            'nip' => $this->pegawai->nip,
            'role' => 'kasubag_tu',
        ]);

        // Route middleware blocks with redirect/error
        $response->assertRedirect(route('dashboard'));

        $this->pegawai->refresh();
        $this->assertEquals('pegawai', $this->pegawai->role);
    }

    public function test_pegawai_cannot_approve_request(): void
    {
        $req = RequestItem::create([
            'user_id' => $this->pegawai->id,
            'request_code' => 'REQ-TEST-SEC-01',
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => false,
        ]);

        $response = $this->actingAs($this->pegawai)->put(route('persetujuan.update', $req->id), [
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('dashboard'));

        $req->refresh();
        $this->assertEquals('pending', $req->status);
    }

    public function test_pegawai_cannot_modify_products(): void
    {
        $category = Category::firstOrCreate(['name' => 'Kategori Security Test']);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'ATK-SEC-01',
            'name' => 'Barang Security Test',
            'unit' => 'Pcs',
            'stock' => 10,
            'minimum_stock' => 2,
            'purchase_price' => 50000,
        ]);

        $response = $this->actingAs($this->pegawai)->put(route('master-data.daftar-barang.update', $product->id), [
            'category_id' => $category->id,
            'code' => 'ATK-SEC-01',
            'name' => 'Hacked Item',
            'unit' => 'Pcs',
            'stock' => 999,
            'purchase_price' => 1,
        ]);

        $response->assertRedirect(route('dashboard'));

        $product->refresh();
        $this->assertEquals('Barang Security Test', $product->name);
        $this->assertEquals(10, $product->stock);
    }

    public function test_pegawai_cannot_print_other_users_recap_document_idor(): void
    {
        $reqLain = RequestItem::create([
            'user_id' => $this->pegawaiLain->id,
            'request_code' => 'REQ-TEST-OTHER',
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => false,
        ]);

        // Pegawai mencoba mencetak tiket milik pegawai lain
        $response = $this->actingAs($this->pegawai)->get(route('rekapitulasi-permintaan.cetak', ['id' => $reqLain->id]));
        $response->assertStatus(403);
    }

    public function test_pegawai_cannot_print_mass_recap_without_ticket_id(): void
    {
        // Pegawai mencoba mencetak rekap massal tanpa ID tiket
        $response = $this->actingAs($this->pegawai)->get(route('rekapitulasi-permintaan.cetak'));
        $response->assertStatus(403);
    }

    public function test_kasubag_tu_cannot_be_reset_via_public_form(): void
    {
        $response = $this->post(route('password.reset.nip'), [
            'nip' => $this->kasubag->nip,
            'email' => $this->kasubag->email,
            'password' => 'new_hacked_pass_123',
            'password_confirmation' => 'new_hacked_pass_123',
        ]);

        $response->assertSessionHas('error');

        $this->kasubag->refresh();
        $this->assertTrue(Hash::check('password123', $this->kasubag->password));
    }
}
