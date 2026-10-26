<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KasubagTuNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagTu;
    protected User $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->pegawai = User::firstOrCreate(
            ['email' => 'pegawai_notif_test@gmail.com'],
            ['name' => 'Pegawai Uji Notif', 'position' => 'Staf Subdit', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );
    }

    public function test_kasubag_tu_sees_pending_verifications_on_notification_page(): void
    {
        $category = Category::firstOrCreate(['name' => 'Alat Tulis']);
        $product = Product::firstOrCreate(
            ['code' => 'NOTIF-PROD-01'],
            [
                'category_id' => $category->id,
                'name' => 'Kertas HVS A4 Test',
                'unit' => 'Rim',
                'stock' => 50,
                'minimum_stock' => 5,
                'purchase_price' => 50000,
            ]
        );

        // Buat permintaan persediaan pending
        $reqStock = RequestItem::create([
            'request_code' => 'REQ-STOCK-NOTIF-01',
            'user_id' => $this->pegawai->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => false,
            'notes' => 'Permintaan persediaan untuk tes notifikasi',
        ]);
        RequestDetail::create([
            'request_id' => $reqStock->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Buat usulan pengadaan pending
        $reqProcure = RequestItem::create([
            'request_code' => 'REQ-BUY-NOTIF-01',
            'user_id' => $this->pegawai->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => true,
            'notes' => 'Pengadaan barang baru untuk tes notifikasi',
        ]);
        RequestDetail::create([
            'request_id' => $reqProcure->id,
            'custom_product_name' => 'Proyektor Mini 4K',
            'quantity' => 1,
            'custom_unit' => 'Unit',
        ]);

        $response = $this->actingAs($this->kasubagTu)->get(route('notifications.index'));
        $response->assertRedirect(route('dashboard'));

        $dashResponse = $this->actingAs($this->kasubagTu)->get(route('dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Menu Verifikasi');
        $dashResponse->assertDontSee('Semua Riwayat');
    }

    public function test_kasubag_tu_json_notifications_feed(): void
    {
        $response = $this->actingAs($this->kasubagTu)->getJson(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unread_count',
            'notifications',
        ]);
    }

    public function test_mark_as_read_with_string_id_does_not_crash(): void
    {
        $response = $this->actingAs($this->kasubagTu)->post(route('notifications.markAsRead', 'req-99999'));

        $response->assertStatus(302);
    }
}
