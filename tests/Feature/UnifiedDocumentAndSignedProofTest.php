<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnifiedDocumentAndSignedProofTest extends TestCase
{
    use DatabaseTransactions;

    protected User $picSdk;
    protected User $pegawai;
    protected User $gudangUser;
    protected User $kasubagTu;
    protected Product $product;
    protected RequestItem $requestItem;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'utami_test_bundle@pkp.go.id'],
            ['name' => 'Utami M.', 'position' => 'Kasubag TU', 'password' => bcrypt('password'), 'role' => 'kasubag_tu']
        );

        $this->gudangUser = User::firstOrCreate(
            ['email' => 'gudang_test_bundle@pkp.go.id'],
            ['name' => 'Devi Ismiyantati, S.K.', 'position' => 'Petugas Gudang', 'is_distributor' => true, 'password' => bcrypt('password'), 'role' => 'pegawai_gudang']
        );

        $this->picSdk = User::firstOrCreate(
            ['email' => 'fairuz_test_bundle@pkp.go.id'],
            [
                'name' => 'Fairuz Armina Nur Azizah, S.T.',
                'position' => 'PIC Distribusi Subdirektorat SDK',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'nip' => '123455678910',
                'password' => bcrypt('password'),
                'role' => 'pic_distribusi',
            ]
        );

        $this->pegawai = User::firstOrCreate(
            ['email' => 'staf_test_bundle@pkp.go.id'],
            ['name' => 'Staf Pegawai Test', 'position' => 'Staf Teknis', 'password' => bcrypt('password'), 'role' => 'pegawai']
        );

        $category = Category::firstOrCreate(['name' => 'Supplais']);
        $this->product = Product::firstOrCreate(
            ['code' => 'BAT-AA-01'],
            ['name' => 'Baterai AA (isi 2)', 'category_id' => $category->id, 'unit' => 'stock', 'stock' => 50, 'purchase_price' => 10000]
        );

        // Buat file fake foto distribusi gudang
        $proofFile = UploadedFile::fake()->image('gudang_proof.png');
        $proofPath = $proofFile->store('distribution_proofs', 'public');

        $this->requestItem = RequestItem::create([
            'request_code' => 'REQ-TEST-BUNDLE-01',
            'user_id' => $this->picSdk->id,
            'type' => 'subdit',
            'status' => 'distributed',
            'receiver_name' => 'Fairuz Armina Nur Azizah',
            'distributed_at' => now(),
            'distributed_by' => $this->gudangUser->id,
            'distribution_notes' => 'Diserahkan dalam kondisi baik',
            'distribution_proof' => $proofPath,
        ]);

        $this->requestItem->details()->create([
            'product_id' => $this->product->id,
            'item_name' => 'Baterai AA (isi 2)',
            'item_unit' => 'stock',
            'quantity' => 4,
            'approved_quantity' => 4,
        ]);
    }

    public function test_distribusi_bukti_contains_all_three_sheets_in_one_file(): void
    {
        $response = $this->actingAs($this->picSdk)->get(route('distribusi.bukti', $this->requestItem->id));

        $response->assertStatus(200);

        // Lembar 1: Usulan Kebutuhan (Gambar 1)
        $response->assertSee('DAFTAR KEBUTUHAN ALAT TULIS KANTOR DAN SUPPLAIS KOMPUTER');
        $response->assertSee('Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman');
        $response->assertSee('TIM SDK');
        $response->assertSee('Fairuz Armina Nur Azizah');

        // Lembar 2: Berita Acara Serah Terima ATK (Gambar 2)
        $response->assertSee('BUKTI SERAH TERIMA BARANG ATK');
        $response->assertSee('REQ-TEST-BUNDLE-01');
        $response->assertSee('Yang Menyerahkan');
        $response->assertSee('Yang Menerima');
        $response->assertSee('Mengetahui &amp; Menyetujui', false);

        // Lembar 3: Lampiran Foto Dokumentasi Fisik
        $response->assertSee('LAMPIRAN RESMI DOKUMENTASI BUKTI SERAH TERIMA ATK');
        $response->assertSee($this->requestItem->distribution_proof_url);
        $response->assertSee('Foto fisik serah terima barang persediaan ATK');

        // Pastikan elemen-elemen yang diminta dihapus tidak muncul
        $response->assertDontSee('A. Foto Dokumentasi');
        $response->assertDontSee('B. Berkas Dokumen');
        $response->assertDontSee('Berkas Dokumen Serah Terima Bertanda Tangan');
        $response->assertDontSee('Petunjuk Pengunggahan Berkas Tanda Tangan');
        $response->assertDontSee('Catatan Petugas');
        $response->assertDontSee('Catatan Khusus PIC Distribusi');
        $response->assertDontSee('Lembar 1 dari 3');
        $response->assertDontSee('Lembar 2 dari 3');
        $response->assertDontSee('Lembar 3 dari 3');
        $response->assertDontSee('Foto Bukti Serah Terima Terlampir');
    }

    public function test_rekapitulasi_permintaan_cetak_single_ticket_contains_all_three_sheets(): void
    {
        $response = $this->actingAs($this->picSdk)->get(route('rekapitulasi-permintaan.cetak', ['request_id' => $this->requestItem->id]));

        $response->assertStatus(200);

        // Lembar 1
        $response->assertSee('DAFTAR KEBUTUHAN ALAT TULIS KANTOR DAN SUPPLAIS KOMPUTER');
        // Lembar 2
        $response->assertSee('BUKTI SERAH TERIMA BARANG ATK');
        // Lembar 3
        $response->assertSee('LAMPIRAN RESMI DOKUMENTASI BUKTI SERAH TERIMA ATK');

        // Pastikan elemen-elemen yang diminta dihapus tidak muncul
        $response->assertDontSee('A. Foto Dokumentasi');
        $response->assertDontSee('B. Berkas Dokumen');
        $response->assertDontSee('Berkas Dokumen Serah Terima Bertanda Tangan');
        $response->assertDontSee('Petunjuk Pengunggahan Berkas Tanda Tangan');
        $response->assertDontSee('Catatan Petugas');
        $response->assertDontSee('Catatan Khusus PIC Distribusi');
        $response->assertDontSee('Lembar 2 dari 3');
        $response->assertDontSee('Lembar 3 dari 3');
        $response->assertDontSee('Foto Bukti Serah Terima Terlampir');
    }

    public function test_pic_or_pegawai_can_upload_signed_proof(): void
    {
        $fakePdf = UploadedFile::fake()->create('dokumen_ttd_basah.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->picSdk)->post(
            route('pengajuan.uploadSignedProof', $this->requestItem->id),
            ['signed_proof' => $fakePdf]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->requestItem->refresh();
        $this->assertNotNull($this->requestItem->signed_proof);
        $this->assertNotNull($this->requestItem->signed_at);
        $this->assertEquals($this->picSdk->id, $this->requestItem->signed_by);
        $this->assertTrue($this->requestItem->is_signed_proof_pdf);
        Storage::disk('public')->assertExists($this->requestItem->signed_proof);

        // Pastikan muncul badge status di top bar dokumen bahwa berkas telah terunggah
        $responseView = $this->actingAs($this->picSdk)->get(route('distribusi.bukti', $this->requestItem->id));
        $responseView->assertSee('Berkas Terunggah');
        $responseView->assertDontSee('Petunjuk Pengunggahan Berkas Tanda Tangan');

        // Pastikan di riwayat pengajuan muncul status & tautan berkas
        $responseIndex = $this->actingAs($this->picSdk)->get(route('pengajuan.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Buka Berkas');
        $responseIndex->assertSee('Nomor Pengajuan:');

        // Pastikan di halaman distribusi muncul tombol Download Berkas dan bukan lagi Berkas TTD PIC
        $responseDistribusi = $this->actingAs($this->gudangUser)->get(route('distribusi.index'));
        $responseDistribusi->assertStatus(200);
        $responseDistribusi->assertSee('Download Berkas');
        $responseDistribusi->assertDontSee('Berkas TTD PIC');
    }

    public function test_unauthorized_user_cannot_upload_signed_proof(): void
    {
        $otherUser = User::firstOrCreate(
            ['email' => 'other_user@pkp.go.id'],
            ['name' => 'Other Subdit', 'role' => 'pegawai', 'password' => bcrypt('secret')]
        );

        $fakePdf = UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf');

        $response = $this->actingAs($otherUser)->post(
            route('pengajuan.uploadSignedProof', $this->requestItem->id),
            ['signed_proof' => $fakePdf]
        );

        $response->assertStatus(403);
    }

    public function test_pengajuan_index_displays_cetak_bukti_and_no_usulan_button(): void
    {
        $response = $this->actingAs($this->picSdk)->get(route('pengajuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Cetak Bukti');
        $response->assertSee('Upload Berkas');
        $response->assertDontSee('>Usulan<', false);
        $response->assertDontSee('>TTD<', false);
    }

    public function test_completed_request_with_signed_proof_is_automatically_hidden_from_dashboard_tracker(): void
    {
        // 1. Saat request baru saja diserahterimakan dan BELUM ada berkas tanda tangan, muncul di tracker
        $response = $this->actingAs($this->picSdk)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee($this->requestItem->request_code);
        $response->assertSee('Upload Berkas TTD');

        // 2. Unggah berkas tanda tangan
        $fakePdf = UploadedFile::fake()->create('dokumen_ttd_basah.pdf', 300, 'application/pdf');
        $this->actingAs($this->picSdk)->post(
            route('pengajuan.uploadSignedProof', $this->requestItem->id),
            ['signed_proof' => $fakePdf]
        );

        // 3. Setelah berkas tanda tangan terunggah, pengajuan otomatis hilang dari tracker agar tidak menumpuk
        $responseAfterUpload = $this->actingAs($this->picSdk)->get(route('dashboard'));
        $responseAfterUpload->assertStatus(200);
        $responseAfterUpload->assertDontSee('id="tracked-panel-' . $this->requestItem->id . '"', false);
    }

    public function test_pegawai_does_not_see_cetak_bukti_and_upload_buttons(): void
    {
        $pegawai = User::firstOrCreate(
            ['email' => 'pegawai_test_role@perumahan.go.id'],
            [
                'name' => 'Staf Pegawai Uji',
                'role' => 'pegawai',
                'nip' => '19980101 202101 1 002',
                'password' => bcrypt('password123'),
            ]
        );

        $reqPegawai = RequestItem::create([
            'request_code' => 'REQ-PEG-TEST-99',
            'user_id' => $pegawai->id,
            'type' => 'individu',
            'status' => 'distributed',
            'receiver_name' => 'Staf Pegawai Uji',
            'distributed_at' => now(),
            'distributed_by' => $this->gudangUser->id,
            'distribution_notes' => 'Selesai diserahkan',
        ]);

        $reqPegawai->details()->create([
            'product_id' => $this->product->id,
            'item_name' => 'Baterai AA (isi 2)',
            'item_unit' => 'stock',
            'quantity' => 2,
            'approved_quantity' => 2,
        ]);

        // 1. Pada halaman Pengajuan (/pengajuan), Pegawai hanya melihat Detail dan TIDAK melihat Cetak Bukti & Upload
        $responsePengajuan = $this->actingAs($pegawai)->get(route('pengajuan.index'));
        $responsePengajuan->assertStatus(200);
        $responsePengajuan->assertSee('Detail');
        $responsePengajuan->assertDontSee('Cetak Bukti');
        $responsePengajuan->assertDontSee('Upload &check;', false);
        $responsePengajuan->assertDontSee('Upload Berkas');
        $responsePengajuan->assertDontSee('uploadTtdModal' . $reqPegawai->id);

        // 2. Pada halaman Dashboard (/dashboard), Pegawai TIDAK melihat tombol Cetak Bukti & Upload Berkas TTD
        $responseDashboard = $this->actingAs($pegawai)->get(route('dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertDontSee('Cetak Bukti');
        $responseDashboard->assertDontSee('Upload Berkas TTD');

        // 3. Pegawai dilarang mengunggah signed_proof langsung ke endpoint backend
        $fakePdf = UploadedFile::fake()->create('dokumen_ttd_pegawai.pdf', 200, 'application/pdf');
        $responseUpload = $this->actingAs($pegawai)->post(
            route('pengajuan.uploadSignedProof', $reqPegawai->id),
            ['signed_proof' => $fakePdf]
        );
        $responseUpload->assertStatus(403);
    }

    public function test_pegawai_gudang_distribusi_can_print_and_upload_signed_proof(): void
    {
        // Pastikan akun pegawai gudang distributor melihat tombol Cetak Bukti pada daftar distribusi
        $responseDistribusi = $this->actingAs($this->gudangUser)->get(route('distribusi.index'));
        $responseDistribusi->assertStatus(200);
        $responseDistribusi->assertSee('Cetak Bukti');

        // Pegawai gudang distributor dapat mengunggah dokumen serah terima bertanda tangan
        $fakePdf = UploadedFile::fake()->create('dokumen_gudang_ttd.pdf', 350, 'application/pdf');
        $responseUpload = $this->actingAs($this->gudangUser)->post(
            route('pengajuan.uploadSignedProof', $this->requestItem->id),
            ['signed_proof' => $fakePdf]
        );
        $responseUpload->assertRedirect();
        $responseUpload->assertSessionHas('success');

        $this->requestItem->refresh();
        $this->assertNotNull($this->requestItem->signed_proof);
        $this->assertEquals($this->gudangUser->id, $this->requestItem->signed_by);
    }
}
