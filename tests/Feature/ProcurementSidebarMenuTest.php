<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\RequestItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementSidebarMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_and_pic_see_usulan_pengadaan_menu_and_can_submit_unstocked_goods(): void
    {
        $category = Category::create([
            'name' => 'Kertas',
            'kd_rincian' => '20.01.1',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kertas A4 70gr',
            'code' => '20.01.1',
            'unit' => 'Rim',
            'initial_stock' => 10,
            'stock' => 10,
            'purchase_price' => 50000,
        ]);

        $pegawai = User::factory()->create([
            'role' => 'pegawai',
            'position' => 'Staf Pelaksana',
        ]);

        $pic = User::factory()->create([
            'role' => 'pic_distribusi',
            'position' => 'PIC Distribusi SDK',
        ]);

        // 1. Pegawai sees "Usulan Pengadaan" in sidebar
        $responsePegawai = $this->actingAs($pegawai)->get(route('pengajuan.create'));
        $responsePegawai->assertStatus(200);
        $responsePegawai->assertSee('Usulan Pengadaan');
        $responsePegawai->assertSee(route('pengajuan.pengadaan.create'));

        // 2. Pegawai visits pengajuan pengadaan page
        $resFormPegawai = $this->actingAs($pegawai)->get(route('pengajuan.pengadaan.create'));
        $resFormPegawai->assertStatus(200);
        $resFormPegawai->assertSee('Usulan Pengadaan Barang ATK');
        $resFormPegawai->assertSee('Barang Belum Tersedia di Katalog');

        // 3. Pegawai submits unstocked goods
        $resSubmitPegawai = $this->actingAs($pegawai)->post(route('pengajuan.pengadaan.store'), [
            'type' => 'individu',
            'notes' => 'Permintaan barang baru khusus',
            'item_name' => ['Proyektor Mini Portable'],
            'quantity' => [2],
            'custom_unit' => ['Unit'],
            'specification' => ['Konektor HDMI & Type-C'],
        ]);
        $resSubmitPegawai->assertRedirect(route('pengajuan.index'));
        $this->assertDatabaseHas('requests', [
            'user_id' => $pegawai->id,
            'type' => 'individu',
            'is_procurement' => 1,
            'notes' => 'Permintaan barang baru khusus',
        ]);

        // 4. PIC sees "Usulan Pengadaan" in sidebar
        $responsePic = $this->actingAs($pic)->get(route('pengajuan.create'));
        $responsePic->assertStatus(200);
        $responsePic->assertSee('Usulan Pengadaan');
        $responsePic->assertSee(route('pengajuan.pengadaan.create'));

        // 5. PIC visits form and submits unstocked goods for subdit
        $resFormPic = $this->actingAs($pic)->get(route('pengajuan.pengadaan.create'));
        $resFormPic->assertStatus(200);
        $resFormPic->assertSee('Subdirektorat (Kolektif Unit Kerja)');

        $resSubmitPic = $this->actingAs($pic)->post(route('pengajuan.pengadaan.store'), [
            'type' => 'subdit',
            'notes' => 'Pengadaan kolektif SDK',
            'item_name' => ['Whiteboard Magnetik 120x80'],
            'quantity' => [3],
            'custom_unit' => ['Buah'],
            'specification' => ['Merk Sakura'],
        ]);
        $resSubmitPic->assertRedirect(route('pengajuan.index'));
        $this->assertDatabaseHas('requests', [
            'user_id' => $pic->id,
            'type' => 'subdit',
            'is_procurement' => 1,
            'notes' => 'Pengadaan kolektif SDK',
        ]);
    }
}
