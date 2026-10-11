<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestItem;
use App\Models\RequestDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    // Menampilkan daftar riwayat pengajuan milik user yang login
    public function index()
    {
        $requests = RequestItem::with(['details.product'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pengajuan.index', compact('requests'));
    }

    // Menampilkan form untuk membuat pengajuan baru
    public function create(Request $request)
    {
        $products = Product::with('category')->orderBy('name', 'asc')->get();
        $selectedProductId = $request->get('product_id');
        return view('pengajuan.create', compact('products', 'selectedProductId'));
    }

    // Menyimpan data pengajuan baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individu,subdit',
            'notes' => 'nullable|string',
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        // Cek apakah ada barang yang diajukan yang stoknya kosong atau kurang sehingga membutuhkan pengadaan
        $requiresProcurement = false;
        foreach ($request->product_id as $index => $productId) {
            $product = Product::find($productId);
            $qty = isset($request->quantity[$index]) ? (int) $request->quantity[$index] : 1;
            if ($product && ($product->stock <= 0 || $qty > $product->stock)) {
                $requiresProcurement = true;
                break;
            }
        }

        // Buat nomor kode unik pengajuan, misal: REQ-20260923-XXXX atau REQ-BUY-20260923-XXXX
        $prefix = $requiresProcurement ? 'REQ-BUY-' : 'REQ-';
        $requestCode = $prefix . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Simpan ke tabel header (requests)
        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $request->type,
            'is_procurement' => $requiresProcurement,
            'status' => 'pending', // Menunggu persetujuan Kasubag TU
            'notes' => $request->notes,
        ]);

        // Simpan detail barang yang diajukan
        foreach ($request->product_id as $index => $productId) {
            $qty = $request->quantity[$index];
            $product = Product::find($productId);
            if ($qty > 0) {
                RequestDetail::create([
                    'request_id' => $requestItem->id,
                    'product_id' => $productId,
                    'custom_product_name' => $product ? $product->name : null,
                    'custom_unit' => $product ? ($product->unit ?: 'Pcs') : 'Pcs',
                    'specification' => $product ? ('Barang Katalog (' . $product->code . ')') : null,
                    'quantity' => $qty,
                ]);
            }
        }

        $successMsg = $requiresProcurement
            ? 'Pengajuan permintaan ATK (' . $requestCode . ') berhasil dikirim! Karena terdapat barang dengan stok habis di gudang, pengajuan otomatis diarahkan ke alur pengadaan Kasubag TU.'
            : 'Pengajuan permintaan ATK berhasil dikirim dan menunggu persetujuan Kasubag TU.';

        return redirect()->route('pengajuan.index')->with('success', $successMsg);
    }

    /**
     * Memesan langsung barang yang stoknya habis dari katalog (tanpa harus ke form permintaan pengadaan terpisah)
     */
    public function orderFromCatalog(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:individu,subdit',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ], [
            'product_id.required' => 'Barang wajib dipilih.',
            'product_id.exists' => 'Barang tidak ditemukan dalam sistem.',
            'type.required' => 'Jenis permintaan wajib dipilih.',
            'quantity.required' => 'Jumlah pesanan wajib diisi.',
            'quantity.min' => 'Jumlah pesanan minimal 1 unit.',
        ]);

        $product = Product::with('category')->findOrFail($request->product_id);

        $requestCode = 'REQ-BUY-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $notes = $request->filled('notes')
            ? trim($request->notes)
            : 'Pemesanan langsung barang habis dari katalog (' . $product->name . ')';

        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $request->type,
            'is_procurement' => true,
            'status' => 'pending',
            'notes' => $notes,
        ]);

        RequestDetail::create([
            'request_id' => $requestItem->id,
            'product_id' => $product->id,
            'custom_product_name' => $product->name,
            'custom_unit' => $product->unit ?: 'Pcs',
            'specification' => 'Barang Katalog (' . $product->code . ') - Kategori: ' . ($product->category->name ?? 'Umum'),
            'quantity' => (int) $request->quantity,
        ]);

        $unitName = $product->unit ?: 'Stok';
        $successMsg = 'Pesanan barang "' . $product->name . '" sebanyak ' . $request->quantity . ' ' . $unitName . ' berhasil diajukan (' . $requestCode . ')! Permintaan telah otomatis diteruskan ke Kasubag TU untuk diproses tanpa perlu mengisi formulir pengadaan terpisah.';

        return redirect()->back()->with('success', $successMsg);
    }

    // Menampilkan form untuk usulan pengadaan barang baru (beli ke supplier)
    public function createProcurement(Request $request)
    {
        $existingProducts = Product::orderBy('name', 'asc')->get();
        return view('pengajuan.pengadaan', compact('existingProducts'));
    }

    // Menyimpan data usulan pengadaan barang ke database
    public function storeProcurement(Request $request)
    {
        $request->validate([
            'type' => 'required|in:individu,subdit',
            'notes' => 'nullable|string',
            'item_name' => 'required|array|min:1',
            'item_name.*' => 'required|string|max:255',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'custom_unit' => 'nullable|array',
            'specification' => 'nullable|array',
            'product_id' => 'nullable|array',
        ], [
            'item_name.required' => 'Minimal satu barang yang diusulkan untuk dibeli wajib diisi.',
            'item_name.*.required' => 'Nama barang yang diminta wajib diisi.',
            'quantity.*.required' => 'Jumlah barang wajib diisi.',
            'quantity.*.min' => 'Jumlah barang minimal 1 unit.',
        ]);

        $requestCode = 'REQ-BUY-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $request->type,
            'is_procurement' => true,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $itemNames = $request->input('item_name', []);
        $quantities = $request->input('quantity', []);
        $units = $request->input('custom_unit', []);
        $specs = $request->input('specification', []);
        $productIds = $request->input('product_id', []);

        foreach ($itemNames as $index => $name) {
            $qty = isset($quantities[$index]) ? (int) $quantities[$index] : 1;
            if ($qty > 0 && !empty(trim($name))) {
                $pid = !empty($productIds[$index]) ? (int) $productIds[$index] : null;
                $unit = !empty($units[$index]) ? trim($units[$index]) : 'Pcs';
                $spec = !empty($specs[$index]) ? trim($specs[$index]) : null;

                RequestDetail::create([
                    'request_id' => $requestItem->id,
                    'product_id' => $pid,
                    'custom_product_name' => trim($name),
                    'custom_unit' => $unit,
                    'specification' => $spec,
                    'quantity' => $qty,
                ]);
            }
        }

        return redirect()->route('pengajuan.index')
            ->with('success', 'Permintaan pengadaan barang (' . $requestCode . ') berhasil diajukan! Usulan ini akan direkapitulasi oleh Subbag TU untuk dibeli langsung ke supplier.');
    }
}
