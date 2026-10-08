<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestItem;
use App\Models\RequestDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PicDivisionDistributionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagTu;
    protected User $picSdk;
    protected User $picTu;
    protected User $subditSdk;
    protected User $pegawai;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'test_kasubag_div@pkp.com'],
            ['name' => 'Utami M', 'position' => 'Kasubag TU', 'subdit_name' => 'Subbagian Tata Usaha', 'password' => bcrypt('secret'), 'role' => 'kasubag_tu']
        );

        $this->picSdk = User::firstOrCreate(
            ['email' => 'test_pic_sdk_div@pkp.com'],
            ['name' => 'PIC SDK Test', 'position' => 'PIC Distribusi Subdirektorat SDK', 'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman', 'password' => bcrypt('secret'), 'role' => 'pic_distribusi']
        );

        $this->picTu = User::firstOrCreate(
            ['email' => 'test_pic_tu_div@pkp.com'],
            ['name' => 'PIC TU Test', 'position' => 'PIC Distribusi Subbagian Tata Usaha', 'subdit_name' => 'Subbagian Tata Usaha', 'password' => bcrypt('secret'), 'role' => 'pic_distribusi']
        );

        $this->subditSdk = User::firstOrCreate(
            ['email' => 'test_subdit_sdk_div@pkp.com'],
            ['name' => 'Subdit SDK User', 'position' => 'Subdirektorat SDK', 'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman', 'password' => bcrypt('secret'), 'role' => 'subdit']
        );

        $this->pegawai = User::firstOrCreate(
            ['email' => 'test_pegawai_div@pkp.com'],
            ['name' => 'Pegawai Pribadi', 'position' => 'Staf Teknis', 'subdit_name' => 'Pegawai Perorangan', 'password' => bcrypt('secret'), 'role' => 'pegawai']
        );

        $category = Category::firstOrCreate(['name' => 'Alat Tulis']);
        $this->product = Product::firstOrCreate(
            ['code' => 'TEST-P-DIV'],
            ['name' => 'Buku Tulis', 'category_id' => $category->id, 'unit' => 'Pcs', 'stock' => 100, 'min_stock' => 10, 'purchase_price' => 5000]
        );
    }

    public function test_kasubag_cannot_access_distribusi_page(): void
    {
        $response = $this->actingAs($this->kasubagTu)->get(route('distribusi.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_kasubag_sidebar_does_not_have_distribusi_menu(): void
    {
        $response = $this->actingAs($this->kasubagTu)->get(route('dashboard'));
        $response->assertStatus(200);
        // Pastikan link ke distribusi tidak ada di sidebar
        $response->assertDontSee('href="' . route('distribusi.index') . '"', false);
    }

    public function test_pic_sdk_only_receives_sdk_collective_requests(): void
    {
        // 1. Buat permintaan Subdit SDK
        $reqSdk = RequestItem::create([
            'request_code' => 'REQ-TEST-SDK-01',
            'user_id' => $this->subditSdk->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $reqSdk->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        // 2. Buat permintaan Pegawai (individu)
        $reqPegawai = RequestItem::create([
            'request_code' => 'REQ-TEST-PEG-01',
            'user_id' => $this->pegawai->id,
            'type' => 'individu',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $reqPegawai->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Akses sebagai PIC SDK: hanya melihat REQ-TEST-SDK-01
        $responseSdk = $this->actingAs($this->picSdk)->get(route('distribusi.index'));
        $responseSdk->assertStatus(200);
        $responseSdk->assertSee('REQ-TEST-SDK-01');
        $responseSdk->assertDontSee('REQ-TEST-PEG-01');

        // Navbar lonceng PIC SDK: ada notifikasi REQ-TEST-SDK-01
        $responseSdk->assertSee('REQ-TEST-SDK-01');
    }

    public function test_pic_tu_receives_pegawai_and_subbag_tu_requests(): void
    {
        // 1. Buat permintaan Subdit SDK
        $reqSdk = RequestItem::create([
            'request_code' => 'REQ-TEST-SDK-02',
            'user_id' => $this->subditSdk->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        // 2. Buat permintaan Pegawai (individu)
        $reqPegawai = RequestItem::create([
            'request_code' => 'REQ-TEST-PEG-02',
            'user_id' => $this->pegawai->id,
            'type' => 'individu',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        // Akses sebagai PIC TU: melihat REQ-TEST-PEG-02, tetapi TIDAK melihat REQ-TEST-SDK-02
        $responseTu = $this->actingAs($this->picTu)->get(route('distribusi.index'));
        $responseTu->assertStatus(200);
        $responseTu->assertSee('REQ-TEST-PEG-02');
        $responseTu->assertDontSee('REQ-TEST-SDK-02');
    }

    public function test_pic_cannot_handover_other_division_request(): void
    {
        // Permintaan SDK
        $reqSdk = RequestItem::create([
            'request_code' => 'REQ-TEST-SDK-03',
            'user_id' => $this->subditSdk->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        // PIC TU mencoba serah terima permintaan SDK -> harus 404 (tidak ditemukan dalam scope divisinya)
        $response = $this->actingAs($this->picTu)->post(route('distribusi.handover', $reqSdk->id), [
            'receiver_name' => 'Penerima Salah',
            'distribution_notes' => 'Catatan',
        ]);

        $response->assertStatus(404);
    }

    public function test_pic_can_handover_with_proof_image_upload(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $reqSdk = RequestItem::create([
            'request_code' => 'REQ-TEST-PROOF-01',
            'user_id' => $this->subditSdk->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_serah_terima.jpg', 600, 400);

        $response = $this->actingAs($this->picSdk)->post(route('distribusi.handover', $reqSdk->id), [
            'receiver_name' => 'Bagus Peneliti',
            'distribution_notes' => 'Barang diterima lengkap dengan bukti foto',
            'distribution_proof' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $reqSdk->refresh();
        $this->assertEquals('distributed', $reqSdk->status);
        $this->assertEquals('Bagus Peneliti', $reqSdk->receiver_name);
        $this->assertNotNull($reqSdk->distribution_proof);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($reqSdk->distribution_proof);

        // Cetak dokumen resmi tanda terima PIC TIDAK menyertakan lampiran foto
        $printResponse = $this->actingAs($this->picSdk)->get(route('distribusi.bukti', $reqSdk->id));
        $printResponse->assertStatus(200);
        $printResponse->assertDontSee('Lampiran Dokumentasi Foto Serah Terima Barang');

        // Tetapi Pemohon (Subdit/Pegawai) saat klik bukti HANYA melihat bukti foto yang diupload PIC
        $pemohonBuktiResponse = $this->actingAs($this->subditSdk)->get(route('distribusi.bukti', $reqSdk->id));
        $pemohonBuktiResponse->assertStatus(200);
        $pemohonBuktiResponse->assertSee('Bukti Foto Penyerahan Barang (PIC)');
        $pemohonBuktiResponse->assertSee($reqSdk->distribution_proof_url);
        $pemohonBuktiResponse->assertDontSee('Kasubag Tata Usaha');
    }

    public function test_pic_can_retroactively_upload_proof_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $reqSdk = RequestItem::create([
            'request_code' => 'REQ-TEST-PROOF-02',
            'user_id' => $this->subditSdk->id,
            'type' => 'subdit',
            'status' => 'distributed',
            'receiver_name' => 'Fairuz Subdit',
            'created_at' => now(),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_susulan.png', 800, 600);

        $response = $this->actingAs($this->picSdk)->post(route('distribusi.uploadProof', $reqSdk->id), [
            'distribution_proof' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $reqSdk->refresh();
        $this->assertNotNull($reqSdk->distribution_proof);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($reqSdk->distribution_proof);

        // Pemohon juga bisa melihat foto di detail modal pengajuan
        $pemohonResponse = $this->actingAs($this->subditSdk)->get(route('pengajuan.index'));
        $pemohonResponse->assertStatus(200);
        $pemohonResponse->assertSee('Bukti Foto Penyerahan Barang:');
        $pemohonResponse->assertSee($reqSdk->distribution_proof_url);

        // Dan saat pemohon membuka halaman bukti, isinya hanya bukti foto
        $buktiResponse = $this->actingAs($this->subditSdk)->get(route('distribusi.bukti', $reqSdk->id));
        $buktiResponse->assertStatus(200);
        $buktiResponse->assertSee('Bukti Foto Penyerahan Barang (PIC)');
        $buktiResponse->assertSee($reqSdk->distribution_proof_url);
    }
}
