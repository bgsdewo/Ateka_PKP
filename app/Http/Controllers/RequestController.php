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
}
