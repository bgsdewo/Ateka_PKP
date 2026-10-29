<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubbagTuVerificationAndRecapTest extends TestCase
{
    use DatabaseTransactions;

    protected User $buUtami;
    protected User $adminUser;
    protected User $pegawaiUser;
    protected User $subditUser;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buUtami = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test_recap@gmail.com'],
            ['name' => 'Admin Test', 'position' => 'Administrator', 'password' => bcrypt('password123'), 'role' => 'admin']
        );

        $this->pegawaiUser = User::firstOrCreate(
            ['email' => 'pegawai_test@gmail.com'],
            ['name' => 'Pegawai Uji', 'position' => 'Staf Subdit', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );

        $this->subditUser = User::firstOrCreate(
            ['email' => 'sdk_test_recap@gmail.com'],
            [
                'name' => 'Fairuz Armina Nur Azizah, S.T.',
                'nip' => '200202262025062007',
                'position' => 'Subdirektorat Sumber Daya Konstruksi',
                'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman',
                'password' => bcrypt('password123'),
                'role' => 'subdit'
            ]
        );

        $category = Category::firstOrCreate(['name' => 'Alat Tulis']);

        $this->productA = Product::firstOrCreate(
            ['code' => 'ATK-VERIF-01'],
            [
                'category_id' => $category->id,
                'name' => 'Buku Ekspedisi TU',
                'unit' => 'Buku',
                'stock' => 50,
                'minimum_stock' => 5,
                'purchase_price' => 15000,
            ]
        );

        $this->productB = Product::firstOrCreate(
            ['code' => 'ATK-VERIF-02'],
            [
                'category_id' => $category->id,
                'name' => 'Pulpen Gel Hitam',
                'unit' => 'Pcs',
                'stock' => 100,
                'minimum_stock' => 10,
                'purchase_price' => 5000,
            ]
        );
    }

    public function test_kasubag_tu_cannot_access_rekapitulasi_permintaan_page(): void
    {
        $response = $this->actingAs($this->buUtami)->get(route('rekapitulasi-permintaan.index'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_rekapitulasi_permintaan_filtering_by_type(): void
    {
        $req1 = RequestItem::create([
            'request_code' => 'REQ-REKAP-001',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'notes' => 'Kebutuhan Individu',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req1->id,
            'product_id' => $this->productA->id,
            'quantity' => 5,
        ]);

        $req2 = RequestItem::create([
            'request_code' => 'REQ-REKAP-002',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'notes' => 'Kebutuhan Kolektif Subdit',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req2->id,
            'product_id' => $this->productB->id,
            'quantity' => 20,
            'approved_quantity' => 15,
        ]);

        // Filter type subdit
        $responseSubdit = $this->actingAs($this->adminUser)
            ->get(route('rekapitulasi-permintaan.index', ['type' => 'subdit']));

        $responseSubdit->assertStatus(200);
        $responseSubdit->assertSee('REQ-REKAP-002');
        $responseSubdit->assertDontSee('REQ-REKAP-001');

        // All items shown when unfiltered
        $responseAll = $this->actingAs($this->adminUser)
            ->get(route('rekapitulasi-permintaan.index'));

        $responseAll->assertStatus(200);
        $responseAll->assertSee('Buku Ekspedisi TU');
        $responseAll->assertSee('Pulpen Gel Hitam');
    }

    public function test_rekapitulasi_permintaan_print_view(): void
    {
        $response = $this->actingAs($this->buUtami)->get(route('rekapitulasi-permintaan.cetak'));

        $response->assertStatus(200);
        $response->assertSee('DAFTAR KEBUTUHAN ALAT TULIS KANTOR DAN SUPPLAIS KOMPUTER');
        $response->assertSee('KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN');
        $response->assertSee('DIREKTORAT JENDERAL KAWASAN PERMUKIMAN');
        $response->assertSee('DIREKTORAT BINA TEKNIK PERUMAHAN DAN KAWASAN PERMUKIMAN');
        $response->assertSee('No. Tiket');
        $response->assertSee('NAMA PENGGUNA BARANG');
        $response->assertSee('Yang Mengajukan');
    }

    public function test_cetak_per_tiket_mengambil_identitas_subdit_sdk_dan_nip(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-SDK-001',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productA->id,
            'quantity' => 10,
            'specification' => 'Buffalo',
        ]);

        $response = $this->actingAs($this->buUtami)
            ->get(route('rekapitulasi-permintaan.cetak', ['request_id' => $req->id]));

        $response->assertStatus(200);
        $response->assertSee('REQ-BUY-SDK-001');
        $response->assertSee('Fairuz Armina Nur Azizah, S.T.');
        $response->assertSee('200202262025062007');
        $response->assertSee('TIM SDK');
        $response->assertSee('Subdirektorat Sumber Daya Konstruksi');
        $response->assertSee('Buffalo');
    }

    public function test_cetak_per_tiket_mengambil_identitas_pegawai(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-PEG-001',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productB->id,
            'quantity' => 2,
            'specification' => 'Keperluan Pribadi',
        ]);

        $response = $this->actingAs($this->buUtami)
            ->get(route('rekapitulasi-permintaan.cetak', ['request_id' => $req->id]));

        $response->assertStatus(200);
        $response->assertSee('REQ-BUY-PEG-001');
        $response->assertSee($this->pegawaiUser->name);
        $response->assertDontSee('Devi Ismiyanti');
        $response->assertSee('Keperluan Pribadi');
    }

    public function test_cetak_per_tiket_subdit_tu_menggunakan_nama_pengguna_tim_tu(): void
    {
        $tuUser = User::firstOrCreate(
            ['email' => 'tu@gmail.com'],
            ['name' => 'Devi Ismiyanti', 'position' => 'Subbagian Tata Usaha', 'password' => bcrypt('password123'), 'role' => 'subdit']
        );
        $tuUser->nip = '19900815 201503 2 004';
        $tuUser->name = 'Devi Ismiyanti';
        $tuUser->save();

        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-TU-001',
            'user_id' => $tuUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req->id,
            'item_name' => 'Kertas',
            'quantity' => 5,
            'item_unit' => 'Rim',
            'specification' => 'Buffalo',
        ]);

        $response = $this->actingAs($this->buUtami)
            ->get(route('rekapitulasi-permintaan.cetak', ['request_id' => $req->id]));

        $response->assertStatus(200);
        $response->assertSee('REQ-BUY-TU-001');
        $response->assertSee('TIM TU');
        $response->assertSee('Devi Ismiyanti');
        $response->assertSee('19900815 201503 2 004');
        $response->assertSee('Subbagian Tata Usaha');
        $response->assertSee('Buffalo');
    }

    public function test_verifikasi_permintaan_supports_partial_approved_quantity_and_notes(): void
    {
        $initialStock = $this->productA->stock;

        $req = RequestItem::create([
            'request_code' => 'REQ-VERIF-TEST',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'pending',
            'notes' => 'Permintaan 10 buku ekspedisi',
            'created_at' => now(),
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productA->id,
            'quantity' => 10,
        ]);

        // Subbag TU adjusts approved quantity to 7 with note
        $response = $this->actingAs($this->buUtami)->put(route('persetujuan.update', $req->id), [
            'status' => 'approved',
            'verification_notes' => 'Disetujui 7 pcs sesuai alokasi triwulan.',
            'approved_quantity' => [
                $detail->id => 7,
            ],
            'item_verification_notes' => [
                $detail->id => 'Stok dibagi rata dengan unit lain',
            ],
        ]);

        $response->assertRedirect(route('persetujuan.index'));
        $response->assertSessionHas('success');

        // Check request status
        $req->refresh();
        $this->assertEquals('approved', $req->status);
        $this->assertEquals($this->buUtami->id, $req->verified_by);
        $this->assertNotNull($req->verified_at);
        $this->assertEquals('Disetujui 7 pcs sesuai alokasi triwulan.', $req->verification_notes);

        // Check detail approved_quantity
        $detail->refresh();
        $this->assertEquals(7, $detail->approved_quantity);
        $this->assertEquals(7, $detail->effective_quantity);
        $this->assertEquals('Stok dibagi rata dengan unit lain', $detail->verification_note);

        // Kasubag TU approval cuts stock in real-time as requested by user
        $this->productA->refresh();
        $this->assertEquals($initialStock - 7, $this->productA->stock);
    }

    public function test_verifikasi_permintaan_prevents_approved_qty_exceeding_requested_qty(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-OVER-TEST',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productA->id,
            'quantity' => 5,
        ]);

        // Attempt to approve 8 (greater than 5)
        $response = $this->actingAs($this->buUtami)->put(route('persetujuan.update', $req->id), [
            'status' => 'approved',
            'approved_quantity' => [
                $detail->id => 8,
            ],
        ]);

        $response->assertSessionHas('error');
        $req->refresh();
        $this->assertEquals('pending', $req->status);
    }

    public function test_unauthorized_user_cannot_access_rekapitulasi_permintaan(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->get(route('rekapitulasi-permintaan.index'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_kasubag_tu_can_procure_and_approve_goods_on_demand(): void
    {
        $initialStock = $this->productA->stock;

        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-001',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'notes' => 'Kebutuhan Buku Ekspedisi Mendesak',
            'created_at' => now(),
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productA->id,
            'quantity' => 12,
        ]);

        $response = $this->actingAs($this->buUtami)->post(route('persetujuan.procure', $req->id), [
            'no_faktur' => 'FAK-TU-2026-001',
            'purchase_date' => '2026-10-05',
            'supplier_name' => 'Toko Alat Tulis Gramedia',
            'procure_quantity' => [
                $detail->id => 12,
            ],
            'purchase_price' => [
                $detail->id => 20000,
            ],
            'purchase_notes' => 'Barang dibeli langsung dari toko offline.',
        ]);

        $response->assertRedirect(route('persetujuan.index'));
        $response->assertSessionHas('success');

        // Check request header
        $req->refresh();
        $this->assertEquals('approved', $req->status);
        $this->assertEquals('FAK-TU-2026-001', $req->purchase_invoice);
        $this->assertEquals('2026-10-05', $req->purchase_date->format('Y-m-d'));
        $this->assertEquals(240000, $req->purchase_price);
        $this->assertEquals($this->buUtami->id, $req->verified_by);
        $this->assertNotNull($req->verified_at);

        // Check detail
        $detail->refresh();
        $this->assertEquals(12, $detail->approved_quantity);
        $this->assertEquals(12, $detail->effective_quantity);
        $this->assertStringContainsString('FAK-TU-2026-001', $detail->verification_note);

        // Check stock transactions
        $txMasuk = StockTransaction::where('product_id', $this->productA->id)
            ->where('type', 'masuk')
            ->where('no_faktur', 'FAK-TU-2026-001')
            ->first();
        $this->assertNotNull($txMasuk);
        $this->assertEquals(12, $txMasuk->quantity);
        $this->assertEquals(20000, $txMasuk->harga_perolehan);
        $this->assertStringContainsString('Gramedia', $txMasuk->uraian);

        $txKeluar = StockTransaction::where('product_id', $this->productA->id)
            ->where('type', 'keluar')
            ->where('transaction_date', '2026-10-05')
            ->latest('id')
            ->first();
        $this->assertNotNull($txKeluar);
        $this->assertEquals(12, $txKeluar->quantity);
        $this->assertEquals('individu', $txKeluar->recipient_type);
        $this->assertStringContainsString('FAK-TU-2026-001', $txKeluar->note);

        // Stock was increased by 12 then decreased by 12, so net change is 0
        $this->productA->refresh();
        $this->assertEquals($initialStock, $this->productA->stock);
        $this->assertEquals(20000, $this->productA->purchase_price);
    }

    public function test_procure_and_approve_validation_fails_when_faktur_is_missing(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-002',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->productA->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->buUtami)->post(route('persetujuan.procure', $req->id), [
            'no_faktur' => '',
            'purchase_date' => '2026-10-05',
            'procure_quantity' => [
                $detail->id => 5,
            ],
        ]);

        $response->assertSessionHasErrors('no_faktur');
        $req->refresh();
        $this->assertEquals('pending', $req->status);
    }

    public function test_pegawai_can_access_procurement_request_form(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->get(route('pengajuan.pengadaan.create'));

        $response->assertStatus(200);
        $response->assertSee('Permintaan Pengadaan Barang ATK (Beli Baru)');
        $response->assertSee('Form Permintaan Pembelian Barang ke Subbag TU');
        $response->assertSee('name="item_name[]"', false);
        $response->assertSee('name="custom_unit[]"', false);
        $response->assertSee('name="specification[]"', false);
        $response->assertSee('name="quantity[]"', false);
    }

    public function test_pegawai_can_submit_procurement_request_for_unstocked_goods(): void
    {
        $response = $this->actingAs($this->pegawaiUser)->post(route('pengajuan.pengadaan.store'), [
            'type' => 'individu',
            'notes' => 'Kebutuhan agenda pimpinan belum ada di gudang',
            'item_name' => [
                'Buku Agenda Khusus Pimpinan',
            ],
            'quantity' => [
                5,
            ],
            'custom_unit' => [
                'Buku',
            ],
            'specification' => [
                'Cover Kulit Hitam Edisi 2026',
            ],
        ]);

        $response->assertRedirect(route('pengajuan.index'));
        $response->assertSessionHas('success');

        $req = RequestItem::where('user_id', $this->pegawaiUser->id)
            ->where('is_procurement', true)
            ->latest('id')
            ->first();

        $this->assertNotNull($req);
        $this->assertEquals('pending', $req->status);
        $this->assertEquals('individu', $req->type);
        $this->assertTrue($req->is_procurement);

        $detail = $req->details()->first();
        $this->assertNotNull($detail);
        $this->assertEquals('Buku Agenda Khusus Pimpinan', $detail->custom_product_name);
        $this->assertEquals('Buku Agenda Khusus Pimpinan', $detail->item_name);
        $this->assertEquals('Buku', $detail->custom_unit);
        $this->assertEquals('Buku', $detail->item_unit);
        $this->assertEquals('Cover Kulit Hitam Edisi 2026', $detail->specification);
        $this->assertEquals(5, $detail->quantity);
    }

    public function test_kasubag_tu_can_procure_and_auto_register_unlisted_product(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-NEW-01',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'is_procurement' => true,
            'status' => 'pending',
            'notes' => 'Kebutuhan ATK baru belum ada di gudang',
            'created_at' => now(),
        ]);

        $detail = RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => null,
            'custom_product_name' => 'Flashdisk Sandisk 64GB',
            'custom_unit' => 'Unit',
            'specification' => 'USB 3.0 Kecepatan Tinggi',
            'quantity' => 4,
        ]);

        // Kasubag TU buys the items from supplier
        $response = $this->actingAs($this->buUtami)->post(route('persetujuan.procure', $req->id), [
            'no_faktur' => 'INV-SUPPLIER-999',
            'purchase_date' => '2026-10-05',
            'supplier_name' => 'IT Store Mangga Dua',
            'procure_quantity' => [
                $detail->id => 4,
            ],
            'purchase_price' => [
                $detail->id => 85000,
            ],
            'purchase_notes' => 'Dibeli sesuai permintaan subdit',
        ]);

        $response->assertRedirect(route('persetujuan.index'));
        $response->assertSessionHas('success');

        // Check request status
        $req->refresh();
        $this->assertEquals('approved', $req->status);
        $this->assertEquals('INV-SUPPLIER-999', $req->purchase_invoice);
        $this->assertEquals(340000, $req->purchase_price);

        // Check detail & auto-created product
        $detail->refresh();
        $this->assertNotNull($detail->product_id);

        $newProduct = Product::find($detail->product_id);
        $this->assertNotNull($newProduct);
        $this->assertEquals('Flashdisk Sandisk 64GB', $newProduct->name);
        $this->assertEquals('Unit', $newProduct->unit);
        $this->assertEquals(85000, $newProduct->purchase_price);

        // Check StockTransaction 'masuk'
        $txMasuk = StockTransaction::where('product_id', $newProduct->id)
            ->where('type', 'masuk')
            ->where('no_faktur', 'INV-SUPPLIER-999')
            ->first();
        $this->assertNotNull($txMasuk);
        $this->assertEquals(4, $txMasuk->quantity);
        $this->assertEquals(85000, $txMasuk->harga_perolehan);

        // In the updated flow, stock deduction is deferred until distribution by Petugas Gudang Distribusi
        $this->assertEquals('ready_to_distribute', $req->status);

        $distributor = User::firstOrCreate(
            ['email' => 'distributor.test@perumahan.go.id'],
            [
                'name' => 'Distributor Gudang Test',
                'role' => 'pegawai_gudang',
                'is_distributor' => true,
                'password' => bcrypt('password123'),
            ]
        );
        $this->actingAs($distributor)
            ->post(route('distribusi.serahkan', $req->id));

        // Check StockTransaction 'keluar' after handover
        $txKeluar = StockTransaction::where('product_id', $newProduct->id)
            ->where('type', 'keluar')
            ->first();
        $this->assertNotNull($txKeluar);
        $this->assertEquals(4, $txKeluar->quantity);
    }

    public function test_persetujuan_page_has_no_dokumen_print_button(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-NODOC-01',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'is_procurement' => true,
            'status' => 'approved',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->buUtami)->get(route('persetujuan.index'));
        $response->assertStatus(200);
        $response->assertSee('Disetujui TU');
        $response->assertDontSee('>Dokumen<', false);
    }

    public function test_approved_procurement_request_does_not_appear_in_pic_distribusi(): void
    {
        $picDistribusi = User::firstOrCreate(
            ['email' => 'fairuz@gmail.com'],
            ['name' => 'Fairuz', 'position' => 'PIC Distribusi', 'password' => bcrypt('password123'), 'role' => 'pic_distribusi']
        );

        $req = RequestItem::create([
            'request_code' => 'REQ-BUY-EXCLUDED-01',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'is_procurement' => true,
            'status' => 'approved',
            'notes' => 'Permintaan pengadaan khusus',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($picDistribusi)->get(route('distribusi.index'));
        $response->assertStatus(200);
        $response->assertDontSee('REQ-BUY-EXCLUDED-01');
    }

    public function test_rekapitulasi_kpi_cards_display_item_types_and_approved_requests(): void
    {
        $req1 = RequestItem::create([
            'request_code' => 'REQ-REKAP-KPI-1',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $req1->id,
            'product_id' => $this->productA->id,
            'quantity' => 50,
            'approved_quantity' => 50,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('rekapitulasi-permintaan.index'));
        $response->assertStatus(200);
        $response->assertSee('Total Barang Diminta');
        $response->assertSee('Total Disetujui TU');
        $response->assertSee('Ragam jenis barang diajukan');
    }

    public function test_kasubag_tu_verifikasi_permintaan_is_separated_between_pengadaan_and_persediaan(): void
    {
        $procurementReq = RequestItem::create([
            'request_code' => 'REQ-BUY-SEP-001',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => true,
            'notes' => 'Minta beli papan tulis baru',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $procurementReq->id,
            'custom_product_name' => 'Whiteboard Test',
            'custom_unit' => 'Unit',
            'quantity' => 2,
        ]);

        $stockReq = RequestItem::create([
            'request_code' => 'REQ-STK-SEP-002',
            'user_id' => $this->pegawaiUser->id,
            'type' => 'individu',
            'status' => 'pending',
            'is_procurement' => false,
            'notes' => 'Minta buku ekspedisi stok gudang',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $stockReq->id,
            'product_id' => $this->productA->id,
            'quantity' => 3,
        ]);

        // 1. Visit category = pengadaan
        $responsePengadaan = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['category' => 'pengadaan']));

        $responsePengadaan->assertStatus(200);
        $responsePengadaan->assertSee('REQ-BUY-SEP-001');
        $responsePengadaan->assertDontSee('REQ-STK-SEP-002');
        $responsePengadaan->assertSee('Daftar Antrean Usulan Pengadaan (Beli Baru)');
        $responsePengadaan->assertSee('Verifikasi Pengadaan');
        $responsePengadaan->assertSee('Verifikasi Permintaan');

        // 2. Visit category = persediaan
        $responsePersediaan = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['category' => 'persediaan']));

        $responsePersediaan->assertStatus(200);
        $responsePersediaan->assertSee('REQ-STK-SEP-002');
        $responsePersediaan->assertDontSee('REQ-BUY-SEP-001');
        $responsePersediaan->assertSee('Daftar Antrean Permintaan Barang Stok (Persediaan)');
    }

    public function test_kasubag_tu_can_view_procurement_history_and_anti_stocking_indicators_for_requester(): void
    {
        // 1. Pengadaan terdahulu oleh subdit
        $pastReq = RequestItem::create([
            'request_code' => 'REQ-BUY-PAST-001',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'is_procurement' => true,
            'purchase_invoice' => 'INV/PAST/2026/01',
            'notes' => 'Pengadaan kertas terdahulu',
            'created_at' => now()->subDays(5),
        ]);
        RequestDetail::create([
            'request_id' => $pastReq->id,
            'item_name' => 'Kertas HVS F4',
            'item_unit' => 'Rim',
            'quantity' => 10,
            'approved_quantity' => 10,
        ]);

        // 2. Usulan pengadaan baru yang meminta barang yang sama lagi
        $newReq = RequestItem::create([
            'request_code' => 'REQ-BUY-NEW-002',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'pending',
            'is_procurement' => true,
            'notes' => 'Minta beli kertas lagi',
            'created_at' => now(),
        ]);
        RequestDetail::create([
            'request_id' => $newReq->id,
            'item_name' => 'Kertas HVS F4',
            'item_unit' => 'Rim',
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['category' => 'pengadaan']));

        $response->assertStatus(200);

        // Kasubag TU melihat tombol Riwayat Akun Pemohon dan indikator barang pernah diminta
        $response->assertSee('Riwayat Akun Pemohon');
        $response->assertSee('Pernah Minta Serupa');
        $response->assertSee('userHistoryModal' . $newReq->id);
        $response->assertSee('INV/PAST/2026/01');
        $response->assertSee('Akumulasi Barang yang Pernah Diajukan');
        $response->assertSee('Waspada Penimbunan Stok');
    }
}


