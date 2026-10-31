<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuPersediaanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_kartu_persediaan_accessible_by_kasubag_and_pegawai_gudang(): void
    {
        $kasubag = User::factory()->create([
            'role' => 'kasubag_tu',
            'name' => 'RA. Utami Mindasari',
            'title' => 'S.T., M.Arch.',
            'nip' => '19850918 201502 2 002',
        ]);

        $examiner = User::factory()->create([
            'role' => 'pegawai_gudang',
            'name' => 'Irvan Fauzan Rahmat',
            'title' => 'S.E',
            'nip' => '19841121 202421 1 005',
            'is_report_examiner' => true,
        ]);

        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'name' => 'Staf Pegawai',
        ]);

        $category = Category::create([
            'name' => 'Baterai & Elektronik',
            'kd_rincian' => '1010306010',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => '1010306010',
            'bmn_code' => '1010306010',
            'name' => 'Baterai ABC Alkaline AA',
            'unit' => 'pak',
            'stock' => 5,
            'purchase_price' => 20000,
        ]);

        // Transaksi Masuk
        StockTransaction::create([
            'product_id' => $product->id,
            'user_id' => $examiner->id,
            'type' => 'masuk',
            'quantity' => 2,
            'stock_after' => 2,
            'transaction_date' => '2026-07-27',
            'no_faktur' => '128/F/CV-KPG/VII/2026',
            'uraian' => 'CV. KARYA PUTRA GROUP',
            'kondisi' => 'Baru',
            'harga_perolehan' => 20000,
        ]);

        // Transaksi Keluar dengan no_faktur berisi kode tiket REQ (harus diabaikan di kolom NO. FAKTUR)
        StockTransaction::create([
            'product_id' => $product->id,
            'user_id' => $examiner->id,
            'type' => 'keluar',
            'quantity' => 1,
            'stock_after' => 1,
            'transaction_date' => '2026-07-28',
            'no_faktur' => 'REQ-BUY-20260728-001',
            'recipient_type' => 'individu',
            'uraian' => 'Rian Teknika, S.T., M.T.',
            'note' => 'Pengeluaran untuk Rian Teknika',
        ]);

        // 1. Pegawai biasa tidak boleh mengakses
        $response = $this->actingAs($pegawai)->get(route('reports.kartu-persediaan'));
        $response->assertRedirect(route('dashboard'));

        // 2. Kasubbag TU bisa mengakses dan melihat data
        $responseKasubag = $this->actingAs($kasubag)->get(route('reports.kartu-persediaan', [
            'product_id' => $product->id,
            'month' => 7,
            'year' => 2026,
        ]));
        $responseKasubag->assertStatus(200);
        $responseKasubag->assertSee('KARTU PERSEDIAAN BARANG');
        $responseKasubag->assertSee('Satker Direktorat Jenderal Kawasan Permukiman');
        $responseKasubag->assertSee('146.03.01.994.694308.000');
        $responseKasubag->assertSee('Baterai ABC Alkaline AA');
        $responseKasubag->assertSee('128/F/CV-KPG/VII/2026');
        $responseKasubag->assertSee('CV. KARYA PUTRA GROUP');
        $responseKasubag->assertSee('Rian Teknika, S.T., M.T.');
        // Blok tanda tangan bawah tidak ditampilkan di halaman preview index
        $responseKasubag->assertDontSee('Kepala Subbagian Tata Usaha,');
        // Baris Saldo Awal TIDAK boleh ditampilkan di tabel
        $responseKasubag->assertDontSee('Saldo Awal Bulan Juli');
        // Keterangan keluar harus berformat "Penyerahan {barang} kepada {penerima}"
        $responseKasubag->assertSee('Penyerahan Baterai ABC Alkaline AA kepada Rian Teknika, S.T., M.T.');

        // 3. Pegawai Gudang bisa mengakses halaman cetak resmi
        $responseCetak = $this->actingAs($examiner)->get(route('reports.kartu-persediaan.cetak', [
            'product_id' => $product->id,
            'month' => 7,
            'year' => 2026,
        ]));
        $responseCetak->assertStatus(200);
        $responseCetak->assertSee('KARTU PERSEDIAAN BARANG');
        $responseCetak->assertSee('TAHUN ANGGARAN 2026');
        $responseCetak->assertSee('146.03.01.994.694308.000');
        $responseCetak->assertSee('1010306010');
        $responseCetak->assertSee('Baterai ABC Alkaline AA');
        $responseCetak->assertSee('pak');
        $responseCetak->assertSee('Mengetahui,');
        $responseCetak->assertSee('Kepala Subbagian Tata Usaha,');
        $responseCetak->assertSee('Pembuat Daftar,');
        $responseCetak->assertSee('RA. Utami Mindasari, S.T., M.Arch.');
        $responseCetak->assertSee('19850918 201502 2 002');
        $responseCetak->assertSee('Irvan Fauzan Rahmat, S.E');
        $responseCetak->assertSee('19841121 202421 1 005');
        // Rumus kolom 9 dan 10
        $responseCetak->assertSee('9 = (7-8)');
        $responseCetak->assertSee('10 = (6x9)');
        // Baris Saldo Awal TIDAK boleh ditampilkan di lembar cetak
        $responseCetak->assertDontSee('Saldo Awal Bulan Juli');
        // Kode tiket REQ-BUY tidak boleh tampil di kolom NO. FAKTUR
        $responseCetak->assertDontSee('REQ-BUY-20260728-001');
        // Keterangan keluar
        $responseCetak->assertSee('Penyerahan Baterai ABC Alkaline AA kepada Rian Teknika, S.T., M.T.');

        // 4. Batch cetak semua barang bermutasi (product_id=all)
        $responseBatch = $this->actingAs($examiner)->get(route('reports.kartu-persediaan.cetak', [
            'product_id' => 'all',
            'month' => 7,
            'year' => 2026,
        ]));
        $responseBatch->assertStatus(200);
        $responseBatch->assertSee('Baterai ABC Alkaline AA');
    }

    public function test_dynamic_signature_updates_when_profile_changes(): void
    {
        $kasubag = User::factory()->create([
            'role' => 'kasubag_tu',
            'name' => 'Pejabat Kasubag Baru',
            'title' => 'S.Sos., M.Si.',
            'nip' => '19900101 201501 1 001',
        ]);

        $examiner = User::factory()->create([
            'role' => 'pegawai_gudang',
            'name' => 'Petugas Gudang Baru',
            'title' => 'A.Md.',
            'nip' => '19950202 202002 1 002',
            'is_report_examiner' => true,
        ]);

        $category = Category::create([
            'name' => 'Kertas',
            'kd_rincian' => '20.01.1',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'KRT-01',
            'name' => 'Kertas A4 80gr',
            'unit' => 'rim',
            'stock' => 10,
        ]);

        $response = $this->actingAs($kasubag)->get(route('reports.kartu-persediaan.cetak', [
            'product_id' => $product->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Pejabat Kasubag Baru, S.Sos., M.Si.');
        $response->assertSee('19900101 201501 1 001');
        $response->assertSee('Petugas Gudang Baru, A.Md.');
        $response->assertSee('19950202 202002 1 002');
    }
}
