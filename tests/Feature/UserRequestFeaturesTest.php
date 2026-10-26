<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\RequestDetail;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserRequestFeaturesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $buUtami;
    protected User $buFairus;
    protected User $subditUser;
    protected User $arsiparisUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buUtami = User::firstOrCreate(
            ['email' => 'utami@gmail.com'],
            ['name' => 'Utami M', 'position' => 'Kasubbag TU', 'password' => bcrypt('password123'), 'role' => 'kasubag_tu']
        );

        $this->buFairus = User::firstOrCreate(
            ['email' => 'fairuz@gmail.com'],
            ['name' => 'Fairuz', 'position' => 'PIC Distribusi Subdirektorat SDK', 'subdit_name' => 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman', 'password' => bcrypt('password123'), 'role' => 'pic_distribusi']
        );

        $this->subditUser = User::firstOrCreate(
            ['email' => 'sdk@gmail.com'],
            ['name' => 'Subdit SDK', 'position' => 'Subdirektorat', 'password' => bcrypt('password123'), 'role' => 'subdit']
        );

        $this->arsiparisUser = User::firstOrCreate(
            ['email' => 'devi@gmail.com'],
            ['name' => 'Devi Ismiyanti', 'position' => 'Arsiparis', 'password' => bcrypt('password123'), 'role' => 'arsiparis']
        );

        $category = Category::firstOrCreate(['name' => 'Kertas']);
        $this->product = Product::firstOrCreate(
            ['code' => 'TEST-001'],
            [
                'category_id' => $category->id,
                'name' => 'Kertas HVS A4 Test',
                'unit' => 'Dos',
                'stock' => 20,
                'minimum_stock' => 5,
                'purchase_price' => 50000,
            ]
        );
    }

    public function test_persetujuan_page_has_filter_and_pagination()
    {
        $response = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['status' => 'pending', 'month' => '9', 'year' => '2026', 'type' => 'subdit']));

        $response->assertStatus(200);
        $response->assertSee('name="type"', false);
        $response->assertSee('name="month"', false);
        $response->assertSee('name="year"', false);
        $response->assertSee('name="search"', false);
    }

    public function test_persetujuan_page_has_live_search_elements_and_scripts()
    {
        $response = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index'));

        $response->assertStatus(200);
        $response->assertSee('id="persetujuan-search"', false);
        $response->assertSee('id="btn-clear-search"', false);
        $response->assertSee('id="table-container"', false);
        $response->assertSee('id="persetujuan-table-body"', false);
        $response->assertSee('filterRowsLocally', false);
        $response->assertSee('performServerSearch', false);
    }

    public function test_persetujuan_search_by_applicant_name_and_code()
    {
        $bagus = User::firstOrCreate(
            ['email' => 'bagus_test@gmail.com'],
            ['name' => 'Bagus Sadewo', 'position' => 'Staff TU', 'password' => bcrypt('password123'), 'role' => 'pegawai']
        );

        $reqItem = RequestItem::create([
            'request_code' => 'REQ-TEST-LIVE-01',
            'user_id' => $bagus->id,
            'type' => 'individu',
            'status' => 'pending',
            'created_at' => now(),
        ]);

        // Search with initial "B"
        $responseB = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['search' => 'B']));

        $responseB->assertStatus(200);
        $responseB->assertSee('Bagus Sadewo');
        $responseB->assertSee('REQ-TEST-LIVE-01');

        // Search with request code
        $responseCode = $this->actingAs($this->buUtami)
            ->get(route('persetujuan.index', ['search' => 'LIVE-01']));

        $responseCode->assertStatus(200);
        $responseCode->assertSee('REQ-TEST-LIVE-01');
        $responseCode->assertSee('Bagus Sadewo');
    }

    public function test_distribusi_page_has_live_search_elements_and_scripts()
    {
        $response = $this->actingAs($this->buFairus)
            ->get(route('distribusi.index'));

        $response->assertStatus(200);
        $response->assertSee('id="distribusi-search"', false);
        $response->assertSee('id="btn-clear-search"', false);
        $response->assertSee('id="table-container"', false);
        $response->assertSee('id="distribusi-table-body"', false);
        $response->assertSee('filterRowsLocally', false);
        $response->assertSee('performServerSearch', false);
    }

    public function test_distribusi_search_by_applicant_name_and_code()
    {
        $bagus = User::firstOrCreate(
            ['email' => 'bagus_dist_test@gmail.com'],
            ['name' => 'Bagus Sadewo', 'position' => 'Subdirektorat SDK', 'password' => bcrypt('password123'), 'role' => 'subdit']
        );

        $reqItem = RequestItem::create([
            'request_code' => 'REQ-DIST-LIVE-01',
            'user_id' => $bagus->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => now(),
        ]);

        // Search with initial "B"
        $responseB = $this->actingAs($this->buFairus)
            ->get(route('distribusi.index', ['search' => 'B']));

        $responseB->assertStatus(200);
        $responseB->assertSee('Bagus Sadewo');
        $responseB->assertSee('REQ-DIST-LIVE-01');

        // Search with request code
        $responseCode = $this->actingAs($this->buFairus)
            ->get(route('distribusi.index', ['search' => 'DIST-LIVE-01']));

        $responseCode->assertStatus(200);
        $responseCode->assertSee('REQ-DIST-LIVE-01');
        $responseCode->assertSee('Bagus Sadewo');
    }

    public function test_katalog_displays_stok_instead_of_unit()
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('katalog-atk'));

        $response->assertStatus(200);
        $response->assertSee('Stok');
        // Ensure "Sisa Stok Fisik" row has "Stok"
        $response->assertSee('Sisa Stok Fisik:', false);
    }

    public function test_katalog_page_has_live_search_elements_and_scripts()
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('katalog-atk'));

        $response->assertStatus(200);
        $response->assertSee('id="katalog-search"', false);
        $response->assertSee('id="btn-clear-search"', false);
        $response->assertSee('id="katalog-grid-container"', false);
        $response->assertSee('filterCardsLocally', false);
        $response->assertSee('performServerSearch', false);
    }

    public function test_katalog_search_by_name_and_code()
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('katalog-atk', ['search' => 'HVS']));

        $response->assertStatus(200);
        $response->assertSee('Kertas HVS A4 Test');
    }

    public function test_pengajuan_create_displays_stok_instead_of_dos()
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('data-unit="Stok"', false);
        $response->assertSee('unit-addon">Stok</span>', false);
    }

    public function test_dashboard_period_filtering_for_subdit()
    {
        // Create request in September 2026
        $reqSept = RequestItem::create([
            'request_code' => 'REQ-TEST-SEP',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'pending',
            'created_at' => '2026-09-15 10:00:00',
        ]);

        // When requesting July 2026, September request should NOT be displayed
        $response = $this->actingAs($this->subditUser)
            ->get(route('dashboard', ['month' => '7', 'year' => '2026']));

        $response->assertStatus(200);
        $response->assertDontSee('REQ-TEST-SEP');

        // When requesting September 2026, it should be displayed
        $response2 = $this->actingAs($this->subditUser)
            ->get(route('dashboard', ['month' => '9', 'year' => '2026']));

        $response2->assertStatus(200);
        $response2->assertSee('REQ-TEST-SEP');
    }

    public function test_distribusi_retains_items_after_handover_and_has_period_filters()
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-TEST-DIST',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'approved',
            'created_at' => '2026-09-20 10:00:00',
        ]);

        RequestDetail::create([
            'request_id' => $req->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Confirm handover by Bu Fairus
        $responseHandover = $this->actingAs($this->buFairus)
            ->post(route('distribusi.handover', $req->id), [
                'receiver_name' => 'Budi Staf SDK',
                'distribution_notes' => 'Diserahkan lengkap',
            ]);

        $responseHandover->assertSessionHas('print_id', $req->id);

        // Access index page: default filter is 'all', so REQ-TEST-DIST is still visible!
        $responseIndex = $this->actingAs($this->buFairus)
            ->get(route('distribusi.index'));

        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('REQ-TEST-DIST');
        $responseIndex->assertSee(route('distribusi.bukti', $req->id));
        $responseIndex->assertSee('name="month"', false);
        $responseIndex->assertSee('name="year"', false);
    }

    public function test_print_reports_use_indonesian_day_names()
    {
        $req = RequestItem::create([
            'request_code' => 'REQ-TEST-PRINT',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'distributed',
            'distributed_at' => '2026-09-28 10:00:00', // Monday -> Senin
        ]);

        // Test bukti serah terima
        $responseBukti = $this->actingAs($this->buFairus)
            ->get(route('distribusi.bukti', $req->id));

        $responseBukti->assertStatus(200);
        $responseBukti->assertSee('Senin, 28 September 2026');
        $responseBukti->assertDontSee('Monday');

        // Test berita acara
        $responseBA = $this->actingAs($this->arsiparisUser)
            ->get(route('reports.berita-acara', ['year' => '2026', 'month' => '9']));

        $responseBA->assertStatus(200);
        $responseBA->assertDontSee('Monday');
    }

    public function test_pengajuan_create_has_working_remove_button(): void
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('pengajuan.create'));

        $response->assertStatus(200);
        // Ensure remove button is not disabled
        $response->assertSee('class="btn btn-outline-danger btn-sm remove-row w-100"', false);
        $response->assertDontSee('<button type="button" class="btn btn-outline-danger btn-sm remove-row w-100" disabled', false);
    }

    public function test_dashboard_year_filter_and_auto_reload(): void
    {
        $response = $this->actingAs($this->subditUser)
            ->get(route('dashboard', ['year' => '2026', 'month' => '7']));

        $response->assertStatus(200);
        // Verify onchange="this.form.submit()" is present for auto-reload
        $response->assertSee('name="year" class="form-select form-select-sm" onchange="this.form.submit()"', false);
        $response->assertSee('name="month" class="form-select form-select-sm" onchange="this.form.submit()"', false);
        // Verify 2026 option is present and selected
        $response->assertSee('<option value="2026" selected>', false);
    }

    public function test_bukti_status_verifikasi_has_no_background_color_badge(): void
    {
        $product = Product::first();
        $req = RequestItem::create([
            'request_code' => 'REQ-TEST-BUKTI-COLOR',
            'user_id' => $this->subditUser->id,
            'type' => 'subdit',
            'status' => 'distributed',
            'distributed_at' => now(),
            'notes' => 'Test notes',
        ]);
        $req->details()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->buFairus)
            ->get(route('distribusi.bukti', $req->id));

        $response->assertStatus(200);
        // Verify no bg-success or bg-info badge used for status
        $response->assertDontSee('badge bg-success', false);
        $response->assertDontSee('badge bg-info', false);
        // Verify text-dark fw-bold is used instead
        $response->assertSee('Disetujui Kasubbag TU &amp; Telah Didistribusikan', false);
    }

    public function test_navbar_profile_has_no_position_bullet_text(): void
    {
        // 1. Kasubbag TU (Utami): ensure "• Kasubbag TU" is NOT rendered in the navbar
        $responseUtami = $this->actingAs($this->buUtami)->get(route('dashboard'));
        $responseUtami->assertStatus(200);
        $responseUtami->assertDontSee('&bull; Kasubbag TU', false);
        $responseUtami->assertDontSee('&bull; ' . $this->buUtami->position, false);
        // Ensure name and role badge are rendered
        $responseUtami->assertSee('Utami M');
        $responseUtami->assertSee('Kasubbag TU');

        // 2. Subdit / Pegawai: ensure "• [position]" is NOT rendered in the navbar
        $responseSubdit = $this->actingAs($this->subditUser)->get(route('dashboard'));
        $responseSubdit->assertStatus(200);
        $responseSubdit->assertDontSee('&bull; ' . $this->subditUser->position, false);
        $responseSubdit->assertSee($this->subditUser->name);

        // 3. On other pages (e.g. pengajuan for PIC), ensure no &bull; appears in navbar
        $responseDist = $this->actingAs($this->buFairus)->get(route('pengajuan.index'));
        $responseDist->assertStatus(200);
        $responseDist->assertDontSee('&bull; ' . $this->buFairus->position, false);

        // 4. Kasubbag TU can access dashboard smoothly
        $responseUtamiDist = $this->actingAs($this->buUtami)->get(route('dashboard'));
        $responseUtamiDist->assertStatus(200);
    }

    public function test_sidebar_mini_hides_kementerian_pkp_ri_subtext(): void
    {
        $response = $this->actingAs($this->buUtami)->get(route('dashboard'));
        $response->assertStatus(200);
        // Verify sidebar-subtext class is present on Kementerian PKP RI
        $response->assertSee('sidebar-subtext', false);
        // Verify CSS rule hiding it on sidebar-mini is in the head
        $response->assertSee('.sidebar.sidebar-mini .sidebar-subtext', false);
        $response->assertSee('display: none !important;', false);
    }

    public function test_pengajuan_index_banner_buttons_removed(): void
    {
        $response = $this->actingAs($this->subditUser)->get(route('pengajuan.index'));
        $response->assertStatus(200);
        // Verify Cek Katalog ATK is removed from the top banner
        $response->assertDontSee('Cek Katalog ATK', false);
        // Verify orange button (btn-warning) for Buat Pengajuan Baru is removed from banner
        $response->assertDontSee('btn-warning text-dark fw-bold btn-sm shadow-sm', false);
        // Table header blue button still exists
        $response->assertSee('btn-primary btn-sm', false);
    }

    public function test_pengajuan_create_has_no_kembali_ke_riwayat_button(): void
    {
        $response = $this->actingAs($this->subditUser)->get(route('pengajuan.create'));
        $response->assertStatus(200);
        $response->assertDontSee('Kembali ke Riwayat', false);
    }

    public function test_katalog_index_has_no_buat_pengajuan_atk_button(): void
    {
        $response = $this->actingAs($this->subditUser)->get(route('katalog-atk'));
        $response->assertStatus(200);
        $response->assertDontSee('+ Buat Pengajuan ATK', false);
    }

    public function test_sidebar_mini_elements_and_letters_alignment(): void
    {
        $response = $this->actingAs($this->subditUser)->get(route('pengajuan.create'));
        $response->assertStatus(200);

        // 1. Verify user profile card classes for mini-sidebar styling
        $response->assertSee('sidebar-user-item', false);
        $response->assertSee('sidebar-user-card', false);
        $response->assertSee('sidebar-user-avatar', false);

        // 2. Verify sub-nav mini letters styling (R, +, K, etc.) is included
        $response->assertSee('.sidebar.sidebar-mini .sub-nav .nav-link', false);
        $response->assertSee('.sidebar.sidebar-mini .sub-nav .nav-link i.sidenav-mini-icon', false);
        $response->assertSee('.sidebar.sidebar-mini .sidebar-user-card', false);
        $response->assertSee('.sidebar.sidebar-mini .nav-item.static-item', false);
        $response->assertSee('transform: none !important;', false);
    }

    public function test_sidebar_mini_logo_and_icons_visibility(): void
    {
        $response = $this->actingAs($this->subditUser)->get(route('dashboard'));
        $response->assertStatus(200);

        // 1. Verify mini header & logo styles
        $response->assertSee('.sidebar.sidebar-mini .sidebar-header', false);
        $response->assertSee('.sidebar.sidebar-mini .navbar-brand .logo-normal img', false);
        $response->assertSee('.sidebar.sidebar-mini .navbar-brand .ms-2', false);
        $response->assertSee('sidebar-brand-text', false);

        // 2. Verify main menu icons styling in mini sidebar
        $response->assertSee('.sidebar.sidebar-mini .iq-main-menu > .nav-item > .nav-link .icon svg', false);
        $response->assertSee('.sidebar.sidebar-mini .item-name', false);
        $response->assertSee('.sidebar.sidebar-mini .sub-nav:not(.show)', false);

        // 3. Verify title attributes exist on nav links for tooltips
        $response->assertSee('title="Dashboard"', false);
        $response->assertSee('title="Permintaan ATK"', false);
    }

    public function test_persetujuan_and_rekapitulasi_permintaan_paginated_to_10()
    {
        // Check persetujuan pagination
        $resApproval = $this->actingAs($this->buUtami)->get(route('persetujuan.index'));
        $resApproval->assertStatus(200);
        /** @var \Illuminate\Pagination\LengthAwarePaginator $requestsApproval */
        $requestsApproval = $resApproval->viewData('requests');
        $this->assertEquals(10, $requestsApproval->perPage());
        $resApproval->assertSee('10 per halaman');

        // Check rekapitulasi-permintaan is disabled for Kasubbag TU and redirects to dashboard
        $resRecap = $this->actingAs($this->buUtami)->get(route('rekapitulasi-permintaan.index'));
        $resRecap->assertRedirect(route('dashboard'));

        // Check Kasubbag TU dashboard and sidebar do not show Rekapitulasi Permintaan
        $resDash = $this->actingAs($this->buUtami)->get(route('dashboard'));
        $resDash->assertStatus(200);
        $resDash->assertDontSee('Rekapitulasi Permintaan');
        $resDash->assertSee('Verifikasi & Persetujuan');
    }
}

