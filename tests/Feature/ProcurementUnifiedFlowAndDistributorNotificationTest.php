<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\StockTransaction;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcurementUnifiedFlowAndDistributorNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagTu;
    protected User $pemohon;
    protected User $examinerGudang;
    protected User $distributorGudang;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'utami_proc_flow@gmail.com'],
            ['name' => 'Utami Kasubbag TU', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->pemohon = User::firstOrCreate(
            ['email' => 'pemohon_unified@gmail.com'],
            ['name' => 'Bagus Sadewo, S.Kom', 'position' => 'Staf Subdit', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );

        $this->examinerGudang = User::updateOrCreate(
            ['email' => 'examiner_proc_flow@gmail.com'],
            [
                'name' => 'Irvan Examiner',
                'position' => 'Pegawai Gudang',
                'password' => bcrypt('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => false,
                'is_report_examiner' => true,
            ]
        );

        $this->distributorGudang = User::updateOrCreate(
            ['email' => 'distributor_proc_flow@gmail.com'],
            [
                'name' => 'Devi Distributor',
                'position' => 'Pegawai Gudang',
                'password' => bcrypt('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => true,
                'is_report_examiner' => false,
            ]
        );

        $this->category = Category::firstOrCreate(['name' => 'Alat Tulis']);
    }

    public function test_procurement_restock_notifies_distributor_and_defers_stock_deduction_until_handover(): void
    {
        // 1. Pemohon membuat pengajuan pengadaan barang
        $req = RequestItem::create([
            'request_code' => 'REQ-UNIFY-' . time(),
            'user_id' => $this->pemohon->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => true,
            'notes' => 'Pengadaan Tinta Printer Canon Hitam',
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'custom_product_name' => 'Tinta Printer Canon Hitam',
            'custom_unit' => 'Botol',
            'quantity' => 5,
            'is_fulfilled' => false,
        ]);

        // 2. Kasubbag TU menyetujui pengadaan
        $this->actingAs($this->kasubagTu)->put(route('persetujuan.update', $req->id), [
            'status' => 'approved',
            'verification_notes' => 'Disetujui untuk dibeli',
            'items' => [
                $detail->id => [
                    'approved_quantity' => 5,
                    'note' => 'Acc 5 botol',
                ]
            ],
        ]);

        $req->refresh();
        $this->assertEquals('approved', $req->status);

        // Notifikasi ke pemeriksa gudang telah terkirim
        $notifExaminer = UserNotification::where('user_id', $this->examinerGudang->id)
            ->where('request_id', $req->id)
            ->where('type', 'procurement_approved_examiner')
            ->first();
        $this->assertNotNull($notifExaminer);

        // 3. Pegawai Gudang Pemeriksa mengisi stok barang pengadaan
        $product = Product::create([
            'code' => 'BRG-TINTA-' . time(),
            'name' => 'Tinta Printer Canon Hitam',
            'category_id' => $this->category->id,
            'unit' => 'Botol',
            'stock' => 10,
            'purchase_price' => 75000,
        ]);

        $productController = new \App\Http\Controllers\ProductController();
        $notified = $productController->fulfillMatchingProcurements($product, 'FAK-TINTA-001', now()->toDateString(), 'Pengadaan Tinta');


        $req->refresh();
        $detail->refresh();
        $product->refresh();

        $this->assertTrue($detail->is_fulfilled);
        $this->assertEquals($product->id, $detail->product_id);
        $this->assertEquals(5, $detail->approved_quantity);

        // PENTING: Stok fisik di gudang TIDAK terpotong pada tahap pengisian oleh pemeriksa
        // Stok tetap utuh 10 botol sampai Petugas Gudang Distribusi melakukan serah terima
        $this->assertEquals(10, $product->stock, 'Stok di gudang tidak boleh langsung terpotong saat diisi pemeriksa.');

        // Transaksi mutasi barang KELUAR belum dicatat
        $txKeluarCount = StockTransaction::where('type', 'keluar')
            ->where('product_id', $product->id)
            ->where('no_faktur', $req->request_code)
            ->count();
        $this->assertEquals(0, $txKeluarCount, 'Mutasi barang keluar tidak boleh dibuat sebelum serah terima.');

        // Notifikasi terkirim ke PETUGAS GUDANG DISTRIBUSI (Bukan lagi ke Pemohon)
        $notifDistributor = UserNotification::where('user_id', $this->distributorGudang->id)
            ->where('request_id', $req->id)
            ->where('type', 'procurement_ready_distribution')
            ->first();
        $this->assertNotNull($notifDistributor, 'Petugas Gudang Distribusi harus menerima notifikasi bahwa barang siap didistribusikan.');
        $this->assertStringContainsString('Siap Didistribusikan', $notifDistributor->title);
        $this->assertStringContainsString('Tinta Printer Canon Hitam', $notifDistributor->message);
        $this->assertStringContainsString('Bagus Sadewo', $notifDistributor->message);
        $this->assertEquals(route('distribusi.index'), $notifDistributor->link);

        // Pastikan Pemohon TIDAK mendapatkan notifikasi 'procurement_available'
        $notifPemohonAvailable = UserNotification::where('user_id', $this->pemohon->id)
            ->where('request_id', $req->id)
            ->where('type', 'procurement_available')
            ->first();
        $this->assertNull($notifPemohonAvailable, 'Pemohon tidak boleh lagi menerima notifikasi lama procurement_available.');

        // 4. Petugas Gudang Distribusi melakukan serah terima barang kepada Pemohon
        $handoverResponse = $this->actingAs($this->distributorGudang)->post(route('distribusi.handover', $req->id), [
            'receiver_name' => 'Bagus Sadewo',
            'distribution_notes' => 'Diterima dalam kondisi baik dan lengkap',
        ]);

        $handoverResponse->assertRedirect();

        $req->refresh();
        $product->refresh();

        // Status tiket berubah menjadi distributed
        $this->assertEquals('distributed', $req->status);
        $this->assertEquals($this->distributorGudang->id, $req->distributed_by);

        // Pada tahap serah terima inilah stok fisik baru terpotong 5 botol (10 - 5 = 5)
        $this->assertEquals(5, $product->stock, 'Stok gudang harus terpotong 5 botol setelah serah terima.');

        // Mutasi Barang Keluar tercatat resmi atas nama Petugas Gudang Distribusi
        $txKeluar = StockTransaction::where('type', 'keluar')
            ->where('product_id', $product->id)
            ->where('no_faktur', $req->request_code)
            ->first();
        $this->assertNotNull($txKeluar, 'Transaksi mutasi barang keluar harus tercatat.');
        $this->assertEquals(5, $txKeluar->quantity);
        $this->assertEquals(5, $txKeluar->stock_after);
        $this->assertEquals($this->distributorGudang->id, $txKeluar->user_id);

        // Pemohon menerima notifikasi serah terima fisik selesai
        $notifHandoverPemohon = UserNotification::where('user_id', $this->pemohon->id)
            ->where('request_id', $req->id)
            ->where('type', 'distribution_completed')
            ->first();
        $this->assertNotNull($notifHandoverPemohon, 'Pemohon menerima notifikasi saat barang fisik telah diserahkan.');
    }
}
