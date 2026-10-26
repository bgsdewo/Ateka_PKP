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

class ReportExaminerNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kasubagTu;
    protected User $pegawai;
    protected User $examinerGudang;
    protected User $regularGudang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasubagTu = User::firstOrCreate(
            ['email' => 'utami_examiner_test@gmail.com'],
            ['name' => 'Utami Kasubbag TU', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->pegawai = User::firstOrCreate(
            ['email' => 'pemohon_proc_test@gmail.com'],
            ['name' => 'Bagus Sadewo, S.Kom', 'position' => 'Staf Subdit', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );

        $this->examinerGudang = User::updateOrCreate(
            ['email' => 'irvan_examiner_test@gmail.com'],
            [
                'name' => 'Irvan, S.K.',
                'position' => 'Pegawai Gudang',
                'password' => bcrypt('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => false,
                'is_report_examiner' => true,
            ]
        );

        $this->regularGudang = User::updateOrCreate(
            ['email' => 'gudang_regular_test@gmail.com'],
            [
                'name' => 'Budi Gudang',
                'position' => 'Pegawai Gudang',
                'password' => bcrypt('password123'),
                'role' => 'pegawai_gudang',
                'is_distributor' => true,
                'is_report_examiner' => false,
            ]
        );
    }

    public function test_examiner_receives_notification_when_kasubag_tu_approves_procurement(): void
    {
        // 1. Buat usulan pengadaan dengan beberapa item
        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-EXAM-001',
            'user_id' => $this->pegawai->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => true,
            'notes' => 'Kebutuhan ATK baru belum ada di gudang',
            'created_at' => now(),
        ]);

        $detail1 = RequestDetail::create([
            'request_id' => $req->id,
            'custom_product_name' => 'Bak Stempel Besar',
            'custom_unit' => 'buah',
            'quantity' => 1,
            'is_fulfilled' => false,
        ]);

        $detail2 = RequestDetail::create([
            'request_id' => $req->id,
            'custom_product_name' => 'Baterai AA (isi 4)',
            'custom_unit' => 'Set',
            'quantity' => 1,
            'is_fulfilled' => false,
        ]);

        // 2. Kasubbag TU menyetujui usulan pengadaan
        $response = $this->actingAs($this->kasubagTu)->put(route('persetujuan.update', $req->id), [
            'status' => 'approved',
            'category' => 'pengadaan',
            'approved_quantity' => [
                $detail1->id => 1,
                $detail2->id => 1,
            ],
            'verification_notes' => 'Disetujui untuk diproses pembelian secara offline oleh Subbag TU.',
        ]);

        if (session('error')) {
            $this->fail('Persetujuan gagal dengan error: ' . session('error'));
        }

        $response->assertRedirect();

        // 3. Verifikasi notifikasi terkirim ke Petugas Pemeriksa Laporan Persediaan
        $notifExaminer = UserNotification::where('user_id', $this->examinerGudang->id)
            ->where('request_id', $req->id)
            ->where('type', 'procurement_approved_examiner')
            ->first();

        $this->assertNotNull($notifExaminer, 'Notifikasi untuk petugas pemeriksa laporan persediaan harus terbuat.');
        $this->assertStringContainsString('Usulan Pengadaan Disetujui', $notifExaminer->title);
        $this->assertStringContainsString('REQ-BUY-EXAM-001', $notifExaminer->message);
        $this->assertStringContainsString('Bagus Sadewo', $notifExaminer->message);
        $this->assertStringContainsString('Bak Stempel Besar', $notifExaminer->message);
        $this->assertStringContainsString('#pengadaan-section', $notifExaminer->link);

        // 4. Pastikan Pegawai Gudang yang BUKAN pemeriksa tidak mendapat notifikasi examiner
        $notifRegular = UserNotification::where('user_id', $this->regularGudang->id)
            ->where('request_id', $req->id)
            ->where('type', 'procurement_approved_examiner')
            ->first();
        $this->assertNull($notifRegular, 'Pegawai gudang yang bukan pemeriksa tidak boleh menerima notifikasi pemeriksa jika ada pemeriksa yang ditugaskan.');

        // 5. Verifikasi tampilan navbar dan notifikasi feed untuk pemeriksa laporan
        $feedResponse = $this->actingAs($this->examinerGudang)->getJson(route('notifications.index'));
        $feedResponse->assertStatus(200);
        $feedData = $feedResponse->json();
        $this->assertGreaterThanOrEqual(1, $feedData['unread_count']);

        // 6. Verifikasi halaman produk / master data dapat diakses oleh pemeriksa
        $prodResponse = $this->actingAs($this->examinerGudang)->get(route('products.index'));
        $prodResponse->assertStatus(200);
        $prodResponse->assertSee('Permintaan Pengadaan Menunggu Penambahan Barang');
        $prodResponse->assertSee('Bak Stempel Besar');
        $prodResponse->assertSee('Baterai AA (isi 4)');
    }
}
