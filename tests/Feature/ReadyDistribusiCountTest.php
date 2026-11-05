<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReadyDistribusiCountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pengadaan_yang_sudah_diisi_stoknya_dihitung_siap_diserahkan(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );
        $cat = Category::firstOrCreate(['name' => 'Alat Tulis']);
        $prod = Product::create(['category_id' => $cat->id, 'code' => 'RDY-T', 'name' => 'Barang Siap Uji', 'unit' => 'Buah', 'stock' => 10, 'minimum_stock' => 0, 'purchase_price' => 1000]);

        RequestItem::flushRequestCaches();
        $awal = RequestItem::getReadyDistribusiCount();

        $mk = function (string $kode, bool $fulfilled) use ($user, $prod) {
            $t = RequestItem::create(['request_code' => $kode, 'user_id' => $user->id, 'type' => 'individu', 'status' => 'approved', 'is_procurement' => true]);
            RequestDetail::create(['request_id' => $t->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit' => 'Buah', 'is_fulfilled' => $fulfilled]);
            return $t;
        };
        $mk('REQ-BUY-RDY-A', true);   // sudah diisi -> siap diserahkan
        $mk('REQ-BUY-RDY-B', false);  // belum diisi -> belum siap

        RequestItem::flushRequestCaches();
        $this->assertSame(1, RequestItem::getReadyDistribusiCount() - $awal);
    }

    public function test_tiket_pengadaan_sebagian_menunggu_stok_dan_tidak_bisa_diserahkan(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );
        $cat = Category::firstOrCreate(['name' => 'Alat Tulis']);
        $prod = Product::create(['category_id' => $cat->id, 'code' => 'RDY-T2', 'name' => 'Barang Sebagian Uji', 'unit' => 'Buah', 'stock' => 10, 'minimum_stock' => 0, 'purchase_price' => 1000]);

        $t = RequestItem::create(['request_code' => 'REQ-BUY-PART', 'user_id' => $user->id, 'type' => 'individu', 'status' => 'approved', 'is_procurement' => true]);
        $d1 = RequestDetail::create(['request_id' => $t->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit' => 'Buah', 'is_fulfilled' => true]);
        $d2 = RequestDetail::create(['request_id' => $t->id, 'product_id' => $prod->id, 'quantity' => 1, 'unit' => 'Buah', 'is_fulfilled' => false]);

        $this->assertTrue($t->fresh()->is_waiting_stock);
        RequestItem::flushRequestCaches();

        $this->actingAs($user)->post('/distribusi/' . $t->id . '/handover', ['receiver_name' => 'Penerima Uji'])
            ->assertSessionHas('error');
        $this->assertSame('approved', $t->fresh()->status);

        $d2->update(['is_fulfilled' => true]);
        $this->assertFalse($t->fresh()->is_waiting_stock);
    }
}
