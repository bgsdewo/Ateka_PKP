<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductRealtimeSyncTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->category = Category::firstOrCreate(['name' => 'Alat Tulis']);
    }

    public function test_product_store_creates_stock_transaction_in_barang_masuk_realtime(): void
    {
        $response = $this->actingAs($this->user)->post(route('master-data.daftar-barang.store'), [
            'category_id' => $this->category->id,
            'code' => 'TEST-SYNC-01',
            'name' => 'Pulpen Pilot G2',
            'unit' => 'Dos',
            'stock' => 30,
            'minimum_stock' => 5,
            'purchase_price' => 15000,
            'no_faktur' => 'FAK-SYNC-01',
            'uraian' => 'CV. Sinar Abadi',
            'description' => 'Pulpen tinta gel',
        ]);

        $response->assertRedirect(route('master-data.daftar-barang.index'));

        // Cek data di products
        $product = Product::where('code', 'TEST-SYNC-01')->first();
        $this->assertNotNull($product);
        $this->assertEquals(30, $product->stock);

        // Cek riwayat otomatis di stock_transactions
        $transaction = StockTransaction::where('product_id', $product->id)->where('type', 'masuk')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(30, $transaction->quantity);
        $this->assertEquals(30, $transaction->stock_after);
        $this->assertEquals('FAK-SYNC-01', $transaction->no_faktur);
        $this->assertEquals('CV. Sinar Abadi', $transaction->uraian);

        // Cek di halaman barang-masuk
        $respBarangMasuk = $this->actingAs($this->user)->get(route('barang-masuk'));
        $respBarangMasuk->assertSee('Pulpen Pilot G2', false);
        $respBarangMasuk->assertSee('FAK-SYNC-01', false);
        $respBarangMasuk->assertSee('30', false);
    }

    public function test_product_update_syncs_stock_transaction_and_barang_masuk_realtime(): void
    {
        // 1. Buat barang awal dengan stok 20
        $product = Product::create([
            'category_id' => $this->category->id,
            'code' => 'TEST-SYNC-02',
            'name' => 'Spidol Whiteboard Awal',
            'unit' => 'Dos',
            'stock' => 20,
            'minimum_stock' => 5,
            'purchase_price' => 25000,
            'description' => 'Spidol hitam',
        ]);

        StockTransaction::create([
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 20,
            'stock_after' => 20,
            'harga_perolehan' => 25000,
            'no_faktur' => 'FAK-AWAL-02',
            'uraian' => 'Toko Alat Tulis',
            'transaction_date' => now(),
            'note' => 'Stok awal',
        ]);

        // 2. Lakukan update: ubah nama, stok dari 20 ke 35, harga perolehan, no faktur, dan uraian
        $response = $this->actingAs($this->user)->put(route('master-data.daftar-barang.update', $product->id), [
            'category_id' => $this->category->id,
            'code' => 'TEST-SYNC-02',
            'name' => 'Spidol Whiteboard Diperbarui',
            'unit' => 'Dos',
            'stock' => 35,
            'minimum_stock' => 10,
            'purchase_price' => 30000,
            'no_faktur' => 'FAK-BARU-99',
            'uraian' => 'PT. ATK Modern',
            'description' => 'Spidol hitam tebal',
        ]);

        $response->assertRedirect(route('master-data.daftar-barang.index'));

        // Cek update di database products
        $product->refresh();
        $this->assertEquals('Spidol Whiteboard Diperbarui', $product->name);
        $this->assertEquals(35, $product->stock);
        $this->assertEquals(30000, (float) $product->purchase_price);

        // Cek update real-time di stock_transactions
        $transaction = StockTransaction::where('product_id', $product->id)->where('type', 'masuk')->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(35, $transaction->quantity);
        $this->assertEquals(35, $transaction->stock_after);
        $this->assertEquals(30000, (float) $transaction->harga_perolehan);
        $this->assertEquals('FAK-BARU-99', $transaction->no_faktur);
        $this->assertEquals('PT. ATK Modern', $transaction->uraian);

        // Cek di halaman barang-masuk
        $respBarangMasuk = $this->actingAs($this->user)->get(route('barang-masuk'));
        $respBarangMasuk->assertSee('Spidol Whiteboard Diperbarui', false);
        $respBarangMasuk->assertSee('FAK-BARU-99', false);
        $respBarangMasuk->assertSee('PT. ATK Modern', false);
        $respBarangMasuk->assertSee('35', false);
        $respBarangMasuk->assertDontSee('FAK-AWAL-02', false);
        $respBarangMasuk->assertDontSee('Spidol Whiteboard Awal', false);
        $respBarangMasuk->assertDontSee('Toko Alat Tulis', false);
    }

    public function test_product_destroy_removes_from_stock_transactions_and_barang_masuk_realtime(): void
    {
        // 1. Buat barang
        $product = Product::create([
            'category_id' => $this->category->id,
            'code' => 'TEST-DELETE-01',
            'name' => 'Barang Untuk Dihapus',
            'unit' => 'Pcs',
            'stock' => 50,
            'minimum_stock' => 5,
            'purchase_price' => 10000,
        ]);

        StockTransaction::create([
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 50,
            'stock_after' => 50,
            'harga_perolehan' => 10000,
            'no_faktur' => 'FAK-HAPUS-01',
            'uraian' => 'Toko Lama',
            'transaction_date' => now(),
        ]);

        // Pastikan awalnya terlihat di Barang Masuk
        $respBefore = $this->actingAs($this->user)->get(route('barang-masuk'));
        $respBefore->assertSee('Barang Untuk Dihapus', false);

        // 2. Hapus barang via Master Data
        $response = $this->actingAs($this->user)->delete(route('master-data.daftar-barang.destroy', $product->id));
        $response->assertRedirect(route('master-data.daftar-barang.index'));

        // Cek bahwa produk sudah terhapus
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Cek bahwa transaksi stoknya juga terhapus secara real-time
        $this->assertDatabaseMissing('stock_transactions', ['product_id' => $product->id]);

        // Cek di halaman barang-masuk bahwa barang sudah tidak tampil
        $respAfter = $this->actingAs($this->user)->get(route('barang-masuk'));
        $respAfter->assertDontSee('Barang Untuk Dihapus', false);
    }
}
