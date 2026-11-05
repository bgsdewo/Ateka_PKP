<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PembelianPerKemasanTest extends TestCase
{
    use DatabaseTransactions;

    private function payload(Category $cat, array $over = []): array
    {
        return array_merge([
            'category_id' => $cat->id,
            'code' => 'RTN-KACA-T',
            'name' => 'Rautan Kaca Uji Kemasan',
            'unit' => 'Buah',
            'stock' => 1,
            'purchase_price' => 120000,
            'pack_size' => 12,
        ], $over);
    }

    public function test_beli_per_dus_dikonversi_ke_satuan_terkecil_dan_sisa_tersimpan(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );
        $cat = Category::firstOrCreate(['name' => 'Alat Tulis']);

        $this->actingAs($user)->post(route('master-data.daftar-barang.store'), $this->payload($cat))->assertSessionHasNoErrors();

        $p = Product::where('name', 'Rautan Kaca Uji Kemasan')->firstOrFail();
        $this->assertSame(12, (int) $p->stock);
        $this->assertEquals(10000.0, (float) $p->purchase_price);
    }

    public function test_harga_rata_rata_dan_catatan_perubahan_harga(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );
        $cat = Category::firstOrCreate(['name' => 'Alat Tulis']);

        // Pembelian 1: 10 buah @ 20.000 (tanpa kemasan)
        $this->actingAs($user)->post(route('master-data.daftar-barang.store'),
            $this->payload($cat, ['stock' => 10, 'purchase_price' => 20000, 'pack_size' => 1]));
        // Pembelian 2: 10 buah @ 18.000 -> rata-rata 19.000
        $this->actingAs($user)->post(route('master-data.daftar-barang.store'),
            $this->payload($cat, ['stock' => 10, 'purchase_price' => 18000, 'pack_size' => 1]));

        $p = Product::where('name', 'Rautan Kaca Uji Kemasan')->firstOrFail();
        $this->assertSame(20, (int) $p->stock);
        $this->assertEquals(19000.0, (float) $p->purchase_price);

        $tx = StockTransaction::where('product_id', $p->id)->where('type', 'masuk')->latest('id')->first();
        $this->assertEquals(18000.0, (float) $tx->harga_perolehan);
        $this->assertStringContainsString('Harga berubah', (string) $tx->note);
    }
}
