<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestItem;
use App\Models\RequestDetail;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    // Menampilkan daftar riwayat pengajuan milik user yang login
    public function index()
    {
        $user = Auth::user();
        $query = RequestItem::with(['details.product', 'signedBy', 'distributor']);

        if ($user && $user->isPicDistribusi()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function ($sub) use ($user) {
                      $sub->forPic($user);
                  });
            });
        } else {
            $query->where('user_id', Auth::id());
        }

        $requests = $query->latest()->get();

        return view('pengajuan.index', compact('requests'));
    }

    // Menampilkan form untuk membuat pengajuan baru
    public function create(Request $request)
    {
        $products = Product::with('category')->orderBy('name', 'asc')->get();
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        $selectedProductId = $request->get('product_id');
        $selectedQuantity = $request->get('quantity');
        $fromRequestId = $request->get('from_request');

        return view('pengajuan.create', compact('products', 'categories', 'selectedProductId', 'selectedQuantity'));
    }

    // Menyimpan data pengajuan baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:individu,subdit',
            'notes' => 'nullable|string',
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'custom_unit' => 'nullable|array',
            'custom_unit.*' => 'nullable|string|max:50',
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

        $currentUser = Auth::user();
        $requestType = ($currentUser && $currentUser->isPicDistribusi()) ? 'subdit' : ($request->input('type', 'individu') ?: 'individu');

        // Simpan ke tabel header (requests)
        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $requestType,
            'is_procurement' => $requiresProcurement,
            'status' => 'pending', // Menunggu persetujuan Kasubbag TU
            'notes' => $request->notes,
        ]);

        // Simpan detail barang yang diajukan
        $units = $request->input('custom_unit', []);
        foreach ($request->product_id as $index => $productId) {
            $qty = $request->quantity[$index];
            $product = Product::find($productId);
            $selectedUnit = !empty($units[$index]) ? trim($units[$index]) : ($product ? ($product->unit ?: 'Pcs') : 'Pcs');
            if ($qty > 0) {
                RequestDetail::create([
                    'request_id' => $requestItem->id,
                    'product_id' => $productId,
                    'custom_product_name' => $product ? $product->name : null,
                    'custom_unit' => $selectedUnit,
                    'specification' => $product ? ('Barang Katalog (' . $product->code . ')') : null,
                    'quantity' => $qty,
                ]);
            }
        }

        $successMsg = $requiresProcurement
            ? 'Pengajuan permintaan ATK (' . $requestCode . ') berhasil dikirim! Barang akan diproses pengadaan oleh Subbag TU dan otomatis dialokasikan untuk Anda setelah tiba tanpa perlu mengajukan ulang.'
            : 'Pengajuan permintaan ATK berhasil dikirim dan menunggu persetujuan Kasubbag TU.';

        return redirect()->route('pengajuan.index')->with('success', $successMsg);
    }

    /**
     * Memesan langsung barang yang stoknya habis dari katalog (tanpa harus ke form permintaan pengadaan terpisah)
     */
    public function orderFromCatalog(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'nullable|in:individu',
            'quantity' => 'required|integer|min:1',
            'custom_unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'product_id.required' => 'Barang wajib dipilih.',
            'product_id.exists' => 'Barang tidak ditemukan dalam sistem.',
            'quantity.required' => 'Jumlah pesanan wajib diisi.',
            'quantity.min' => 'Jumlah pesanan minimal 1 unit.',
        ]);

        $product = Product::with('category')->findOrFail($request->product_id);

        $requestCode = 'REQ-BUY-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $notes = $request->filled('notes')
            ? trim($request->notes)
            : 'Pemesanan langsung barang habis dari katalog (' . $product->name . ')';

        $selectedUnit = $request->filled('custom_unit') ? trim($request->custom_unit) : ($product->unit ?: 'Pcs');

        $currentUser = Auth::user();
        $requestType = ($currentUser && $currentUser->isPicDistribusi()) ? 'subdit' : ($request->input('type', 'individu') ?: 'individu');

        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $requestType,
            'is_procurement' => true,
            'status' => 'pending',
            'notes' => $notes,
        ]);

        RequestDetail::create([
            'request_id' => $requestItem->id,
            'product_id' => $product->id,
            'custom_product_name' => $product->name,
            'custom_unit' => $selectedUnit,
            'specification' => 'Barang Katalog (' . $product->code . ') - Kategori: ' . ($product->category->name ?? 'Umum'),
            'quantity' => (int) $request->quantity,
        ]);

        $unitName = $selectedUnit ?: 'Stok';
        $successMsg = 'Pesanan barang "' . $product->name . '" sebanyak ' . $request->quantity . ' ' . $unitName . ' berhasil diajukan (' . $requestCode . ')! Permintaan telah otomatis diteruskan ke Kasubbag TU untuk diproses tanpa perlu mengisi formulir pengadaan terpisah.';

        return redirect()->back()->with('success', $successMsg);
    }

    // Menampilkan form untuk usulan pengadaan barang baru (beli ke supplier)
    public function createProcurement(Request $request)
    {
        $existingProducts = Product::with('category')->orderBy('name', 'asc')->get();
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        return view('pengajuan.pengadaan', compact('existingProducts', 'categories'));
    }

    // Menyimpan data usulan pengadaan barang ke database
    public function storeProcurement(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:individu',
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

        $currentUser = Auth::user();
        $requestType = ($currentUser && $currentUser->isPicDistribusi()) ? 'subdit' : ($request->input('type', 'individu') ?: 'individu');

        $requestItem = RequestItem::create([
            'user_id' => Auth::id(),
            'request_code' => $requestCode,
            'type' => $requestType,
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

    /**
     * Upload berkas bukti serah terima bertanda tangan oleh PIC / Pegawai
     */
    public function uploadSignedProof(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $requestItem = RequestItem::findOrFail($id);

        $canUpload = ($user->isKasubagTU()
            || $user->isPegawaiGudang()
            || $user->isAdmin()
            || ($user->isPicDistribusi() && $user->canHandleRequest($requestItem)))
            && !$user->isPegawai();

        if (!$canUpload) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunggah bukti tanda tangan pada pengajuan ini.');
        }

        $request->validate([
            'signed_proof' => 'required|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
        ], [
            'signed_proof.required' => 'Silakan pilih file dokumen tanda tangan (PDF / Gambar) terlebih dahulu.',
            'signed_proof.file' => 'File yang diunggah tidak valid.',
            'signed_proof.mimes' => 'Format file harus berupa PDF, JPEG, PNG, JPG, atau WEBP.',
            'signed_proof.max' => 'Ukuran file tidak boleh melebihi 5 MB.',
        ]);

        if ($requestItem->signed_proof && Storage::disk('public')->exists($requestItem->signed_proof)) {
            Storage::disk('public')->delete($requestItem->signed_proof);
        }

        $file = $request->file('signed_proof');
        $ext = strtolower($file->getClientOriginalExtension());
        $ext = in_array($ext, ['pdf', 'jpeg', 'png', 'jpg', 'webp']) ? $ext : 'pdf';
        $filename = 'signed_' . $requestItem->request_code . '_' . Str::random(16) . '.' . $ext;
        $path = $file->storeAs('signed_proofs', $filename, 'public');

        $requestItem->update([
            'signed_proof' => $path,
            'signed_at' => now(),
            'signed_by' => $user->id,
        ]);

        return redirect()->back()
            ->with('success', 'Dokumen bukti serah terima bertanda tangan untuk pengajuan ' . $requestItem->request_code . ' berhasil diunggah.');
    }
}
