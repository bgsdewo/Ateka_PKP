<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HandoverHistoryRedirectionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $distributorUser;
    protected User $applicantUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->distributorUser = User::firstOrCreate(
            ['email' => 'devi_distributor_test@gmail.com'],
            [
                'name' => 'Devi Ismiyantati',
                'role' => 'pegawai_gudang',
                'position' => 'Petugas Distribusi Gudang ATK',
                'password' => bcrypt('password123'),
                'is_distributor' => 1,
            ]
        );

        $this->applicantUser = User::firstOrCreate(
            ['email' => 'bagus_applicant_test@gmail.com'],
            [
                'name' => 'Bagus Sadewo',
                'role' => 'pegawai',
                'position' => 'Asisten Penyusunan Data & Pelaporan',
                'password' => bcrypt('password123'),
            ]
        );

        $category = Category::firstOrCreate(['name' => 'Alat Tulis Kantor']);
        $this->product = Product::firstOrCreate(
            ['code' => 'ATK-STEMPEL-01'],
            [
                'category_id' => $category->id,
                'name' => 'Bak Stempel Besar',
                'stock' => 50,
                'unit' => 'Pcs',
                'purchase_price' => 15000,
            ]
        );
    }

    public function test_confirm_handover_redirects_to_distribusi_index_and_shows_history_without_refresh(): void
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-20261026-QT3A',
            'user_id' => $this->applicantUser->id,
            'type' => 'individu',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
            'approved_quantity' => 10,
        ]);

        // Simulasikan user mengakses dari URL ?status=ready (seperti pada Gambar 1)
        $readyUrl = route('distribusi.index', ['status' => 'ready']);
        $responseReady = $this->actingAs($this->distributorUser)->get($readyUrl);
        $responseReady->assertStatus(200);
        $responseReady->assertSee('REQ-20261026-QT3A');

        // Lakukan submit Konfirmasi Penyerahan ATK
        $responseHandover = $this->actingAs($this->distributorUser)
            ->from($readyUrl)
            ->post(route('distribusi.handover', $req->id), [
                'receiver_name' => 'Bagus Sadewo',
                'distribution_notes' => 'Diserahkan lengkap',
            ]);

        // Harus diarahkan ke route distribusi.index (bukan kembali ke ?status=ready yang menyembunyikan riwayat)
        $responseHandover->assertRedirect(route('distribusi.index'));
        $responseHandover->assertSessionHas('print_id', $req->id);
        $responseHandover->assertSessionHas('success');

        // Buka halaman hasil redirect (seperti pada Gambar 2)
        $responseAfter = $this->actingAs($this->distributorUser)
            ->get(route('distribusi.index'));

        $responseAfter->assertStatus(200);
        // Pastikan riwayat langsung muncul dengan status Telah Diserahterimakan
        $responseAfter->assertSee('REQ-20261026-QT3A');
        $responseAfter->assertSee('Telah Diserahterimakan');
        $responseAfter->assertSee('Bagus Sadewo');
        $responseAfter->assertSee('Cetak Bukti');
        // Pastikan dropdown status default adalah Semua Status (bukan Siap Distribusi)
        $responseAfter->assertSee('<option value="all" selected>Semua Status</option>', false);
    }
}
