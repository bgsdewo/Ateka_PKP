<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DaftarBarangPeriodAndInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->category = Category::firstOrCreate(['name' => 'Alat Tulis']);
    }

    public function test_daftar_barang_page_has_period_filter_and_invoice_columns(): void
    {
        $response = $this->actingAs($this->user)->get(route('master-data.daftar-barang.index'));

        $response->assertStatus(200);
        $response->assertSee('Filter Periode Daftar Barang');
        $response->assertSee('Periode Bulan');
        $response->assertSee('Periode Tahun');
        $response->assertSee('Dari Tanggal');
        $response->assertSee('Sampai Tanggal');
        $response->assertSee('Terapkan');
        $response->assertSee('No. Faktur');
        $response->assertSee('Tgl Faktur');
        $response->assertSee('Uraian / Supplier');
    }

    public function test_daftar_barang_filters_by_period(): void
    {
        $prodAug = Product::create([
            'category_id' => $this->category->id,
            'code' => 'TEST-AUG-01',
            'name' => 'Barang Bulan Agustus',
            'unit' => 'Pcs',
            'stock' => 10,
            'minimum_stock' => 2,
            'purchase_price' => 5000,
        ]);
        StockTransaction::create([
            'product_id' => $prodAug->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 10,
            'stock_after' => 10,
            'harga_perolehan' => 5000,
            'no_faktur' => 'FAK-AUG-11',
            'uraian' => 'CV. Agustus Mandiri',
            'transaction_date' => '2026-08-15',
        ]);

        $prodSep = Product::create([
            'category_id' => $this->category->id,
            'code' => 'TEST-SEP-01',
            'name' => 'Barang Bulan September',
            'unit' => 'Pcs',
            'stock' => 15,
            'minimum_stock' => 3,
            'purchase_price' => 7000,
        ]);
        StockTransaction::create([
            'product_id' => $prodSep->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 15,
            'stock_after' => 15,
            'harga_perolehan' => 7000,
            'no_faktur' => 'FAK-SEP-22',
            'uraian' => 'CV. September Sejahtera',
            'transaction_date' => '2026-09-20',
        ]);

        // Filter bulan September 2026
        $resSep = $this->actingAs($this->user)->get(route('master-data.daftar-barang.index', [
            'month' => '09',
            'year' => '2026',
        ]));
        $resSep->assertStatus(200);
        $resSep->assertSee('Barang Bulan September');
        $resSep->assertSee('FAK-SEP-22');
        $resSep->assertSee('20/09/2026');
        $resSep->assertSee('CV. September Sejahtera');
        $resSep->assertDontSee('Barang Bulan Agustus');

        // Filter rentang tanggal Agustus 2026
        $resAug = $this->actingAs($this->user)->get(route('master-data.daftar-barang.index', [
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-31',
        ]));
        $resAug->assertStatus(200);
        $resAug->assertSee('Barang Bulan Agustus');
        $resAug->assertSee('FAK-AUG-11');
        $resAug->assertSee('15/08/2026');
        $resAug->assertSee('CV. Agustus Mandiri');
        $resAug->assertDontSee('Barang Bulan September');
    }

    public function test_daftar_barang_store_and_update_with_transaction_date(): void
    {
        // 1. Store dengan transaction_date
        $resStore = $this->actingAs($this->user)->post(route('master-data.daftar-barang.store'), [
            'category_id' => $this->category->id,
            'code' => 'TEST-STORE-TGL',
            'name' => 'Barang Tanggal Custom',
            'unit' => 'Pcs',
            'stock' => 25,
            'minimum_stock' => 5,
            'purchase_price' => 12000,
            'no_faktur' => 'FAK-CUST-99',
            'transaction_date' => '2026-07-10',
            'uraian' => 'CV. Prima Custom',
        ]);

        $resStore->assertRedirect(route('master-data.daftar-barang.index'));

        $product = Product::where('code', 'TEST-STORE-TGL')->first();
        $this->assertNotNull($product);

        $tx = StockTransaction::where('product_id', $product->id)->where('type', 'masuk')->first();
        $this->assertNotNull($tx);
        $this->assertEquals('2026-07-10', \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d'));
        $this->assertEquals('FAK-CUST-99', $tx->no_faktur);
        $this->assertEquals('CV. Prima Custom', $tx->uraian);

        // 2. Update dengan transaction_date baru
        $resUpdate = $this->actingAs($this->user)->put(route('master-data.daftar-barang.update', $product->id), [
            'category_id' => $this->category->id,
            'code' => 'TEST-STORE-TGL',
            'name' => 'Barang Tanggal Custom (Edited)',
            'unit' => 'Pcs',
            'stock' => 30,
            'minimum_stock' => 5,
            'purchase_price' => 12500,
            'no_faktur' => 'FAK-CUST-EDITED',
            'transaction_date' => '2026-07-15',
            'uraian' => 'CV. Prima Custom Baru',
        ]);

        $resUpdate->assertRedirect(route('master-data.daftar-barang.index'));

        $tx->refresh();
        $this->assertEquals('2026-07-15', \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d'));
        $this->assertEquals('FAK-CUST-EDITED', $tx->no_faktur);
        $this->assertEquals('CV. Prima Custom Baru', $tx->uraian);
    }
}
