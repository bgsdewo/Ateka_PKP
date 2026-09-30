<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BarangMasukConversionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_barang_masuk_with_packaging_unit_conversion(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $category = Category::firstOrCreate(['name' => 'Buku & Tulis']);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'BRG-CONV-01',
            'name' => 'Buku Besar Folio',
            'unit' => 'Buku',
            'stock' => 10,
            'minimum_stock' => 5,
            'purchase_price' => 20000,
        ]);

        // Simulasikan beli 2 Lusin @ 12 Buku = 24 Buku dengan harga Rp 25.000 / Buku
        $response = $this->actingAs($user)->post(route('inventory.transaction.store'), [
            'product_id' => $product->id,
            'type' => 'masuk',
            'satuan_beli' => 'Lusin',
            'jumlah_beli' => 2,
            'isi_per_kemasan' => 12,
            'quantity' => 24,
            'tanggal_faktur' => now()->format('Y-m-d'),
            'no_faktur' => 'FAK-LUSIN-01',
            'uraian' => 'CV Toko Buku Makmur',
            'kondisi' => 'Baru',
            'harga_perolehan' => 25000,
            'keterangan' => 'Pembelian 2 lusin buku besar',
        ]);

        $response->assertRedirect(route('barang-masuk'));
        $response->assertSessionHas('success');

        // Pastikan transaksi tercatat dengan konversi yang tepat
        $transaction = StockTransaction::where('no_faktur', 'FAK-LUSIN-01')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(24, $transaction->quantity);
        $this->assertEquals('Lusin', $transaction->satuan_beli);
        $this->assertEquals(2, $transaction->jumlah_beli);
        $this->assertEquals(12, $transaction->isi_per_kemasan);
        $this->assertEquals(34, $transaction->stock_after); // 10 + 24
        $this->assertEquals(25000, (float) $transaction->harga_perolehan);

        // Pastikan stok fisik produk bertambah menjadi 34 dan purchase_price diperbarui
        $product->refresh();
        $this->assertEquals(34, $product->stock);
        $this->assertEquals(25000, (float) $product->purchase_price);

        // Pastikan halaman barang-masuk menampilkan konversi lusin & unit produk
        $indexResponse = $this->actingAs($user)->get(route('barang-masuk'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('24', false);
        $indexResponse->assertSee('2 Lusin', false);
        $indexResponse->assertSee('@ 12 Buku', false);
    }
}
