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
    // Menampilkan daftar riwayat pengajuan milik user yang login (dengan filter & pagination)
    public function index(Request $request)
    {
        $user = Auth::user();

        // Lingkup data: milik sendiri; PIC Distribusi juga melihat pengajuan divisinya
        $scope = RequestItem::query();
        if ($user && $user->isPicDistribusi()) {
            $scope->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function ($sub) use ($user) {
                      $sub->forPic($user);
                  });
            });
        } else {
            $scope->where('user_id', Auth::id());
        }

        // Ringkasan status (seluruh riwayat, tidak terpengaruh filter di bawah)
        $statusCounts = (clone $scope)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $search = trim((string) $request->get('search', ''));
        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $selectedMonth = in_array($monthInput, [null, '', 'all'], true) ? null : (int) $monthInput;
        $selectedYear = in_array($yearInput, [null, '', 'all'], true) ? null : (int) $yearInput;

        $query = (clone $scope)
            ->with(['details.product', 'signedBy', 'distributor'])
            ->when(in_array($statusFilter, ['pending', 'approved', 'distributed', 'rejected'], true), fn($q) => $q->where('status', $statusFilter))
            ->when(in_array($typeFilter, ['individu', 'subdit'], true), fn($q) => $q->where('type', $typeFilter))
            ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhere('receiver_name', 'like', "%{$search}%")
                        ->orWhere('purchase_invoice', 'like', "%{$search}%")
                        ->orWhereHas('details', function ($dq) use ($search) {
                            $dq->where('custom_product_name', 'like', "%{$search}%")
                               ->orWhere('specification', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details.product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            });

        $requests = $query->latest()->latest('id')->paginate(10)->withQueryString();

        $isPic = (bool) ($user && $user->isPicDistribusi());

        return view('pengajuan.index', compact(
            'requests', 'statusCounts', 'search', 'statusFilter', 'typeFilter', 'selectedMonth', 'selectedYear', 'isPic'
        ));
    }

    // Menampilkan form untuk membuat pengajuan baru
    public function create(Request $request)
    {
        $products = Product::with('category')->orderBy('name', 'asc')->get();
        $categories = \App\Models\Category::orderBy('name', 'asc')->get()->unique('name');
        $selectedProductId = $request->get('product_id');
        $selectedQuantity = $request->get('quantity');
        $fromRequestId = $request->get('from_request');

        return view('pengajuan.create', compact('products', 'categories', 'selectedProductId', 'selectedQuantity'));
    }

    /**
     * Barang bertanda "Khusus Perwakilan Subdit" hanya boleh diminta PIC Distribusi.
     * Mengembalikan pesan error bila dilanggar, atau null bila aman.
     */
    private function khususSubditViolation($products): ?string
    {
        $user = Auth::user();
        if ($user && $user->isPicDistribusi()) {
            return null;
        }
        $names = $products->where('khusus_subdit', true)->pluck('name')->all();
        if (empty($names)) {
            return null;
        }
        return 'Barang berikut khusus diminta oleh Perwakilan Subdit: ' . implode(', ', $names) . '.';
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

        if ($blocked = $this->khususSubditViolation(Product::whereIn('id', $request->product_id)->get())) {
            return back()->withInput()->with('error', $blocked);
        }

        // Cek apakah ada barang yang diajukan yang stoknya kosong atau kurang sehingga membutuhkan pengadaan
        $requiresProcurement = false;
        foreach ($request->product_id as $index => $productId) {
            $product = Product::find($productId);
            $qty = isset($request->quantity[$index]) ? (int) $request->quantity[$index] : 1;
            $qtyBase = $product ? $product->toBaseQuantity($qty, $request->input('custom_unit.' . $index)) : $qty;
            if ($product && ($product->stock <= 0 || $qtyBase > $product->stock)) {
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
            'type' => 'nullable|in:individu,subdit',
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

        if ($blocked = $this->khususSubditViolation(collect([$product]))) {
            return back()->withInput()->with('error', $blocked);
        }

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
        $categories = \App\Models\Category::orderBy('name', 'asc')->get()->unique('name');
        return view('pengajuan.pengadaan', compact('existingProducts', 'categories'));
    }

    // Menyimpan data usulan pengadaan barang ke database
    public function storeProcurement(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:individu,subdit',
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
