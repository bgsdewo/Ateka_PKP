<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportExportFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagUser;
    protected User $picDistribusiUser;
    protected User $pegawaiUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagUser = User::firstOrCreate(
            ['email' => 'kasubag_export_test@gmail.com'],
            ['name' => 'Utami M.', 'position' => 'Kasubag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu', 'nip' => '199812132025062010']
        );

        $this->picDistribusiUser = User::firstOrCreate(
            ['email' => 'pic_export_test@gmail.com'],
            ['name' => 'Fairuz', 'position' => 'PIC Distribusi', 'password' => bcrypt('password123'), 'role' => 'pic_distribusi']
        );

        $this->pegawaiUser = User::firstOrCreate(
            ['email' => 'pegawai_export_test@gmail.com'],
            ['name' => 'Budi Pegawai', 'position' => 'Pegawai', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );

        $category = Category::firstOrCreate(['name' => 'Alat Tulis Kantor']);
        $this->product = Product::firstOrCreate(
            ['code' => 'ATK-EXP-001'],
            [
                'category_id' => $category->id,
                'name' => 'Buku Ekspedisi Test',
                'unit' => 'Buku',
                'stock' => 15,
                'minimum_stock' => 3,
                'purchase_price' => 25000,
            ]
        );

        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->kasubagUser->id,
            'type' => 'keluar',
            'quantity' => 5,
            'stock_before' => 20,
            'stock_after' => 15,
            'harga_satuan' => 25000,
            'transaction_date' => now()->toDateString(),
            'recipient_type' => 'subdit',
            'uraian' => 'Subdit Standardisasi',
            'note' => 'Pengadaan rutin kantor Subdit',
        ]);
    }

    public function test_unauthorized_roles_cannot_access_reports_page(): void
    {
        foreach ([$this->pegawaiUser, $this->picDistribusiUser] as $user) {
            $response = $this->actingAs($user)->get(route('reports.index'));
            $response->assertRedirect(route('dashboard'));
        }
    }

    public function test_authorized_roles_can_access_reports_page(): void
    {
        foreach ([$this->kasubagUser] as $user) {
            $response = $this->actingAs($user)->get(route('reports.index'));
            $response->assertStatus(200);
            $response->assertSee('Laporan Persediaan Barang');
            $response->assertSee('Export Excel');
            $response->assertSee('Export PDF');
        }
    }

    public function test_reports_page_has_table_and_footer_summary(): void
    {
        $response = $this->actingAs($this->kasubagUser)->get(route('reports.index'));
        $response->assertStatus(200);

        // Verify Table structure and new column headers
        $response->assertSee('id="laporanTable"', false);
        $response->assertSee('Tanggal Faktur', false);
        $response->assertSee('Kode Barang', false);
        $response->assertSee('Uraian', false);
        $response->assertSee('<tfoot>', false);
        $response->assertSee('TOTAL REKAPITULASI', false);
        $response->assertSee('id="footerTotalStokAwal"', false);
        $response->assertSee('id="footerTotalStokDipinjam"', false);
        $response->assertSee('id="footerTotalStokSisa"', false);
        $response->assertSee('id="footerTotalNilaiSaldo"', false);
        $response->assertSee('Nama Penerima', false);
        $response->assertSee('Subdit Standardisasi', false);

        // Verify Catatan format for barang keluar
        $response->assertSee('Pengeluaran Buku Ekspedisi Test Untuk Subdit Standardisasi', false);
    }

    public function test_transaction_note_formatting_for_masuk_and_keluar(): void
    {
        // 1. Test Barang Masuk
        $txMasuk = StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->kasubagUser->id,
            'type' => 'masuk',
            'quantity' => 10,
            'stock_after' => 25,
            'harga_perolehan' => 25000,
            'transaction_date' => now()->toDateString(),
            'no_faktur' => 'INV-TEST-001',
            'uraian' => 'Toko Buku Test',
            'note' => 'Pembelian Buku Ekspedisi Test',
        ]);

        $this->assertEquals('Pembelian Buku Ekspedisi Test', $txMasuk->formatted_note);

        // 2. Test Barang Keluar
        $txKeluar = StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->kasubagUser->id,
            'type' => 'keluar',
            'quantity' => 2,
            'stock_after' => 23,
            'transaction_date' => now()->toDateString(),
            'recipient_type' => 'individu',
            'uraian' => 'Bagus Sadewo',
            'note' => 'Pengeluaran persediaan ATK untuk Bagus Sadewo [REQ-001]',
        ]);

        $this->assertEquals('Pengeluaran Buku Ekspedisi Test Untuk Bagus Sadewo', $txKeluar->formatted_note);

        $response = $this->actingAs($this->kasubagUser)->get(route('reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Pembelian Buku Ekspedisi Test', false);
        $response->assertSee('Pengeluaran Buku Ekspedisi Test Untuk Bagus Sadewo', false);
        $response->assertSee('Toko Buku Test', false);
    }

    public function test_excel_export_script_contains_formulas_and_custom_styles(): void
    {
        $response = $this->actingAs($this->kasubagUser)->get(route('reports.index'));
        $response->assertStatus(200);

        // 1. Verify Excel configuration contains row calculation formula: Nilai Saldo = Stok Sisa * Harga Satuan (=J*K)
        $response->assertSee("cL.append('<f>J' + rNum + '*K' + rNum + '</f>');", false);

        // 2. Verify Excel summary row formulas (=SUM(...))
        $response->assertSee("'<f>SUM(H' + startDataRow + ':H' + endDataRow + ')</f>", false);
        $response->assertSee("'<f>SUM(I' + startDataRow + ':I' + endDataRow + ')</f>", false);
        $response->assertSee("'<f>SUM(J' + startDataRow + ':J' + endDataRow + ')</f>", false);
        $response->assertSee("'<f>SUM(L' + startDataRow + ':L' + endDataRow + ')</f>", false);

        // 3. Verify Custom Rupiah number format and Kemen PKP header colors
        $response->assertSee('formatCode', false);
        $response->assertSee('FF144B59', false); // Kemen PKP Navy
        $response->assertSee('showGridLines', false);
    }

    public function test_pdf_export_script_contains_ministry_branding_and_signatures(): void
    {
        $response = $this->actingAs($this->kasubagUser)->get(route('reports.index'));
        $response->assertStatus(200);

        // 1. Verify Ministry header
        $response->assertSee('KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN REPUBLIK INDONESIA', false);
        $response->assertSee('SEKRETARIAT JENDERAL - PENGELOLAAN ARSIP & PERSEDIAAN BARANG (ATK)', false);

        // 2. Verify PDF landscape orientation and layout table widths
        $response->assertSee("orientation: 'landscape'", false);
        $response->assertSee("pageSize: 'A4'", false);

        // 3. Verify Single Signature Block (Kasubag TU only)
        $response->assertDontSee('Arsiparis (Pelaporan & Administrasi)', false);
        $response->assertSee('Kasubag TU (Approver / Pimpinan)', false);
    }
}
