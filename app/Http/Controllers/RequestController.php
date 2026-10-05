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

        // Buat nomor kode unik pengajuan, misal: REQ-20260923-XXXX
        $requestCode = 'REQ-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        // Simpan ke tabel header (requests)
        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $request->type,
            'status' => 'pending', // Menunggu persetujuan Kasubag TU
            'notes' => $request->notes,
        ]);

        // Simpan detail barang yang diajukan
        foreach ($request->product_id as $index => $productId) {
            $qty = $request->quantity[$index];
            if ($qty > 0) {
                RequestDetail::create([
                    'request_id' => $requestItem->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                ]);
            }
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan permintaan ATK berhasil dikirim dan menunggu persetujuan Kasubag TU.');
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
