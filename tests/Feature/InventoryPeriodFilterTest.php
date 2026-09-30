<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryPeriodFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $category = Category::firstOrCreate(['name' => 'Alat Tulis']);
        $this->product = Product::firstOrCreate(
            ['code' => 'BRG-FILT-01'],
            [
                'category_id' => $category->id,
                'name' => 'Kertas A4 Filter Test',
                'unit' => 'Rim',
                'stock' => 50,
                'minimum_stock' => 10,
                'purchase_price' => 45000,
            ]
        );
    }

    public function test_barang_masuk_page_has_period_filter_and_no_modal_form(): void
    {
        // Buat transaksi di tahun 2025 dan 2026
        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 15,
            'stock_after' => 30,
            'transaction_date' => '2025-05-10',
            'no_faktur' => 'FAK-2025-01',
            'uraian' => 'Toko 2025',
        ]);

        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 20,
            'stock_after' => 50,
            'transaction_date' => '2026-09-15',
            'no_faktur' => 'FAK-2026-01',
            'uraian' => 'Toko 2026',
        ]);

        // 1. Akses tanpa filter
        $response = $this->actingAs($this->user)->get(route('barang-masuk'));
        $response->assertStatus(200);
        $response->assertSee('Filter Periode Barang Masuk', false);
        $response->assertSee('name="month"', false);
        $response->assertSee('name="year"', false);
        $response->assertSee('name="start_date"', false);
        $response->assertSee('name="end_date"', false);
        // Pastikan tombol dan modal tambah barang sudah tidak ada
        $response->assertDontSee('data-bs-target="#modalBarangMasuk"', false);
        $response->assertDontSee('id="modalBarangMasuk"', false);

        // 2. Akses dengan filter tahun 2026
        $response2026 = $this->actingAs($this->user)->get(route('barang-masuk', ['year' => '2026']));
        $response2026->assertStatus(200);
        $response2026->assertSee('FAK-2026-01', false);
        $response2026->assertDontSee('FAK-2025-01', false);
    }

    public function test_barang_keluar_page_has_period_filter_and_no_modal_form(): void
    {
        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'keluar',
            'quantity' => 5,
            'stock_after' => 45,
            'transaction_date' => '2026-08-20',
            'uraian' => 'Distribusi Bagian Umum',
        ]);

        $response = $this->actingAs($this->user)->get(route('barang-keluar'));
        $response->assertStatus(200);
        $response->assertSee('Filter Periode Barang Keluar', false);
        $response->assertSee('name="month"', false);
        $response->assertSee('name="year"', false);
        // Pastikan tombol dan modal tambah barang keluar sudah tidak ada
        $response->assertDontSee('data-bs-target="#modalBarangKeluar"', false);
        $response->assertDontSee('id="modalBarangKeluar"', false);
        $response->assertSee('Distribusi Bagian Umum', false);
    }

    public function test_single_date_filter_strictly_shows_only_that_exact_day(): void
    {
        // Buat data transaksi tanggal 27, 28, dan 29 September 2026
        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 10,
            'stock_after' => 10,
            'transaction_date' => '2026-09-27',
            'no_faktur' => 'FAK-27-SEP',
            'uraian' => 'Toko 27 Sep',
        ]);

        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 20,
            'stock_after' => 30,
            'transaction_date' => '2026-09-28',
            'no_faktur' => 'FAK-28-SEP',
            'uraian' => 'Toko 28 Sep',
        ]);

        StockTransaction::create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 30,
            'stock_after' => 60,
            'transaction_date' => '2026-09-29',
            'no_faktur' => 'FAK-29-SEP',
            'uraian' => 'Toko 29 Sep',
        ]);

        // Filter hanya tanggal 27 (start_date=2026-09-27, end_date kosong)
        $resp27 = $this->actingAs($this->user)->get(route('barang-masuk', [
            'start_date' => '2026-09-27',
            'end_date' => '',
        ]));

        $resp27->assertStatus(200);
        $resp27->assertSee('FAK-27-SEP', false);
        $resp27->assertDontSee('FAK-28-SEP', false);
        $resp27->assertDontSee('FAK-29-SEP', false);

        // Filter rentang tanggal 27 s/d 28
        $respRange = $this->actingAs($this->user)->get(route('barang-masuk', [
            'start_date' => '2026-09-27',
            'end_date' => '2026-09-28',
        ]));

        $respRange->assertStatus(200);
        $respRange->assertSee('FAK-27-SEP', false);
        $respRange->assertSee('FAK-28-SEP', false);
        $respRange->assertDontSee('FAK-29-SEP', false);
    }

    public function test_empty_filter_results_do_not_render_colspan_in_tbody(): void
    {
        // Akses filter dengan tanggal yang dipastikan tidak memiliki data sama sekali (misal 2010-01-01)
        $respMasuk = $this->actingAs($this->user)->get(route('barang-masuk', [
            'start_date' => '2010-01-01',
            'end_date' => '2010-01-01',
        ]));
        $respMasuk->assertStatus(200);
        // Pastikan tidak ada <td colspan inside tbody yang menyebabkan DataTables TN/18
        $respMasuk->assertDontSee('colspan="9"', false);

        $respKeluar = $this->actingAs($this->user)->get(route('barang-keluar', [
            'start_date' => '2010-01-01',
            'end_date' => '2010-01-01',
        ]));
        $respKeluar->assertStatus(200);
        // Pastikan tidak ada <td colspan inside tbody
        $respKeluar->assertDontSee('colspan="8"', false);
    }
}
