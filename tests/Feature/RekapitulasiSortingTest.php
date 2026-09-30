<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RekapitulasiSortingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );
    }

    public function test_rekapitulasi_page_sorts_categories_by_masuk_keluar_stok_and_saldo(): void
    {
        // Category 1: Kategori ZZZ (alphabetically last), but has biggest total_in
        $catZ = Category::create(['name' => 'ZZZ Kategori']);
        $prodZ = Product::create([
            'category_id' => $catZ->id,
            'code' => 'TEST-CATZ-01',
            'name' => 'Item Z',
            'unit' => 'Pcs',
            'stock' => 50,
            'minimum_stock' => 5,
            'purchase_price' => 1000,
        ]);
        StockTransaction::create([
            'product_id' => $prodZ->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 100,
            'stock_after' => 100,
            'harga_perolehan' => 1000,
            'transaction_date' => '2026-05-10',
        ]);

        // Category 2: Kategori AAA (alphabetically first), but 0 mutasi and 0 stock
        $catA = Category::create(['name' => 'AAA Kategori Kosong']);

        $response = $this->actingAs($this->user)->get(route('reports.rekapitulasi', ['year' => 2026]));

        $response->assertStatus(200);

        // ZZZ Kategori should appear before AAA Kategori Kosong
        $content = $response->getContent();
        $posZ = strpos($content, 'ZZZ Kategori');
        $posA = strpos($content, 'AAA Kategori Kosong');

        $this->assertNotFalse($posZ);
        $this->assertNotFalse($posA);
        $this->assertLessThan($posA, $posZ, 'Kategori yang memiliki barang masuk harus tampil lebih dulu dibanding kategori kosong meskipun abjadnya lebih akhir.');
    }
}
