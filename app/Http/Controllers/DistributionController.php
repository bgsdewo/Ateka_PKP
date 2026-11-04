<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DistributionController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan dan koordinasi distribusi barang (PIC Distribusi)
     */
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU() && !auth()->user()->isPegawaiGudang()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Distribusi Barang hanya dapat diakses oleh Kasubbag TU dan Pegawai Gudang.');
        }

        $filterType = $request->get('type'); // individu, subdit, or null
        $filterStatus = $request->get('status', 'all'); // default to 'all' so handed over items remain visible

        // Jika user baru saja menyelesaikan serah terima barang (session print_id aktif),
        // pastikan status otomatis kembali ke 'all' (Semua Status) agar riwayat barang yang baru diserahkan langsung muncul
        // di tabel riwayat tanpa harus user me-refresh atau mengganti filter secara manual.
        if (session('print_id') && $filterStatus === 'ready') {
            $filterStatus = 'all';
        }
        $search = $request->get('search');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        $baseQuery = RequestItem::with(['user', 'details.product', 'distributor'])
            ->whereIn('status', ['approved', 'distributed'])
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('type', $filterType);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('receiver_name', 'like', "%{$search}%")
                        ->orWhere('distribution_notes', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                               ->orWhere('position', 'like', "%{$search}%");
                        })
                        ->orWhereHas('details.product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                        })
                        // Barang pengadaan yang belum ada di Master Barang (nama diketik pemohon)
                        ->orWhereHas('details', function ($dq) use ($search) {
                            $dq->where('custom_product_name', 'like', "%{$search}%")
                               ->orWhere('specification', 'like', "%{$search}%");
                        });
                });
            })
            ->when($selectedYear, function ($q) use ($selectedYear) {
                $q->where(function ($sub) use ($selectedYear) {
                    $sub->whereYear('distributed_at', $selectedYear)
                        ->orWhere(function ($s2) use ($selectedYear) {
                            $s2->whereNull('distributed_at')
                               ->whereYear('created_at', $selectedYear);
                        });
                });
            })
            ->when($selectedMonth, function ($q) use ($selectedMonth) {
                $q->where(function ($sub) use ($selectedMonth) {
                    $sub->whereMonth('distributed_at', $selectedMonth)
                        ->orWhere(function ($s2) use ($selectedMonth) {
                            $s2->whereNull('distributed_at')
                               ->whereMonth('created_at', $selectedMonth);
                        });
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->where(function ($sub) use ($startDate) {
                    $sub->whereDate('distributed_at', '>=', $startDate)
                        ->orWhere(function ($s2) use ($startDate) {
                            $s2->whereNull('distributed_at')
                               ->whereDate('created_at', '>=', $startDate);
                        });
                });
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->whereDate('distributed_at', '<=', $endDate)
                        ->orWhere(function ($s2) use ($endDate) {
                            $s2->whereNull('distributed_at')
                               ->whereDate('created_at', '<=', $endDate);
                        });
                });
            });

        // Rekap statistik untuk Distribusi Barang via 1 single aggregated query
        $stats = (clone $baseQuery)
            ->selectRaw("
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as total_ready,
                COUNT(CASE WHEN status = 'distributed' THEN 1 END) as total_distributed,
                COUNT(CASE WHEN status = 'approved' AND type = 'subdit' THEN 1 END) as total_subdit_ready,
                COUNT(CASE WHEN status = 'approved' AND (type = 'individu' OR type IS NULL) THEN 1 END) as total_individu_ready
            ")
            ->first();

        $totalReady = (int) ($stats->total_ready ?? 0);
        $totalDistributed = (int) ($stats->total_distributed ?? 0);
        $totalSubditReady = (int) ($stats->total_subdit_ready ?? 0);
        $totalIndividuReady = (int) ($stats->total_individu_ready ?? 0);

        // Rekapitulasi kolektif per Subdirektorat (hanya dijalankan jika ada item subdit yang siap)
        $subditGroups = $totalSubditReady > 0
            ? (clone $baseQuery)
                ->where('status', 'approved')
                ->where('type', 'subdit')
                ->get()
                ->groupBy(function ($item) {
                    return $item->user->position ?? ($item->user->name ?? 'Subdit Umum');
                })
            : collect();

        $query = (clone $baseQuery)->latest('updated_at')->latest('id');

        if ($filterStatus === 'ready') {
            $query->where('status', 'approved');
        } elseif ($filterStatus === 'distributed') {
            $query->where('status', 'distributed');
        }

        $requests = $query->paginate(15)->withQueryString();

        // Akun Pegawai Gudang yang ditunjuk Kasubbag TU sebagai Petugas Distribusi resmi
        $defaultDistributor = \Illuminate\Support\Facades\Cache::remember('distribusi_default_distributor', 300, function () {
            return \App\Models\User::where('role', 'pegawai_gudang')
                ->where('is_distributor', true)
                ->where('name', '!=', 'tes')
                ->latest('id')
                ->first()
                ?? \App\Models\User::where('role', 'pegawai_gudang')
                ->where('name', '!=', 'tes')
                ->latest('id')
                ->first();
        }) ?? auth()->user();

        $gudangUsers = \Illuminate\Support\Facades\Cache::remember('distribusi_gudang_users', 300, function () {
            return \App\Models\User::where('role', 'pegawai_gudang')
                ->orderByDesc('is_distributor')
                ->orderBy('name')
                ->get();
        });

        return view('distribusi.index', compact(
            'requests',
            'totalReady',
            'totalDistributed',
            'totalSubditReady',
            'totalIndividuReady',
            'subditGroups',
            'gudangUsers',
            'defaultDistributor',
            'filterType',
            'filterStatus',
            'search',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'isAllMonths'
        ));
    }

    /**
     * Konfirmasi penyerahan barang / selesaikan distribusi
     */
    public function confirmHandover(Request $request, $id)
    {
        $authUser = auth()->user();
        $isAuthorizedDistributor = $authUser && ($authUser->isKasubagTU() || ($authUser->isPegawaiGudang() && $authUser->isDistributor()));

        if (!$isAuthorizedDistributor) {
            return redirect()->route('dashboard')->with('error', 'Aksi penyerahan barang hanya dapat dilakukan oleh Petugas Distribusi yang ditugaskan resmi atau Kasubbag TU.');
        }

        $requestItem = RequestItem::with(['user', 'details.product'])
            ->findOrFail($id);

        $request->validate([
            'distributed_by' => 'nullable|exists:users,id',
            'receiver_name' => 'required|string|max:255',
            'distribution_notes' => 'nullable|string',
            'distribution_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'receiver_name.required' => 'Nama penerima barang fisik wajib diisi.',
            'distribution_proof.image' => 'File bukti penyerahan harus berupa foto/gambar.',
            'distribution_proof.mimes' => 'Format foto harus berupa JPEG, PNG, JPG, atau WEBP.',
            'distribution_proof.max' => 'Ukuran foto bukti tidak boleh melebihi 5 MB.',
        ]);

        DB::beginTransaction();
        try {
            $proofPath = $requestItem->distribution_proof;
            if ($request->hasFile('distribution_proof')) {
                if ($proofPath && Storage::disk('public')->exists($proofPath)) {
                    Storage::disk('public')->delete($proofPath);
                }
                $file = $request->file('distribution_proof');
                $ext = strtolower($file->getClientOriginalExtension());
                $ext = in_array($ext, ['jpeg', 'png', 'jpg', 'webp']) ? $ext : 'jpg';
                $filename = 'proof_' . $requestItem->request_code . '_' . \Illuminate\Support\Str::random(16) . '.' . $ext;
                $proofPath = $file->storeAs('distribution_proofs', $filename, 'public');
            }

            $distributedById = $request->input('distributed_by');
            if (!$distributedById) {
                $defaultDist = \App\Models\User::where('role', 'pegawai_gudang')
                    ->where('is_distributor', true)
                    ->where('name', '!=', 'tes')
                    ->latest('id')
                    ->first();
                $distributedById = $defaultDist ? $defaultDist->id : Auth::id();
            }

            $distributorUser = \App\Models\User::find($distributedById);
            $distributorName = $distributorUser ? ($distributorUser->name_with_title ?: $distributorUser->name) : 'Petugas Distribusi Gudang';

            // ========================================================
            // MUTASI BARANG KELUAR & PEMOTONGAN STOK GUDANG RIIL
            // Dilakukan saat Petugas Gudang Distribusi menyerahkan fisik barang
            // ========================================================
            $pemohonName = $requestItem->user ? ($requestItem->user->name_with_title ?: $requestItem->user->name) : 'Pemohon';
            $pemohonUnit = ($requestItem->user && $requestItem->user->position) ? ' (' . $requestItem->user->position . ')' : '';
            $penerimaUraian = $request->receiver_name . ($request->receiver_name !== $pemohonName ? ' (untuk ' . $pemohonName . $pemohonUnit . ')' : $pemohonUnit);

            foreach ($requestItem->details as $detail) {
                $qtyToDeduct = $detail->effective_quantity ?? ($detail->approved_quantity !== null ? (int) $detail->approved_quantity : (int) $detail->quantity);
                if ($qtyToDeduct <= 0) {
                    continue;
                }

                if ($detail->product_id) {
                    $product = Product::lockForUpdate()->find($detail->product_id);
                    if ($product) {
                        // Cek apakah mutasi keluar untuk tiket & produk ini sudah pernah tercatat sebelumnya (idempotency)
                        $existingTx = StockTransaction::where('type', 'keluar')
                            ->where('product_id', $product->id)
                            ->where('no_faktur', $requestItem->request_code)
                            ->first();

                        $noteText = 'Penyerahan ATK kepada ' . $request->receiver_name . ' (' . $requestItem->request_code . ')';
                        if ($request->filled('distribution_notes')) {
                            $noteText .= ' - ' . $request->distribution_notes;
                        }

                        if ($existingTx) {
                            // Update data transaksi yang ada agar mencerminkan serah terima riil
                            $existingTx->update([
                                'user_id'          => $distributedById,
                                'quantity'         => $qtyToDeduct,
                                'transaction_date' => now()->toDateString(),
                                'uraian'           => $penerimaUraian,
                                'note'             => $noteText,
                            ]);
                        } else {
                            // Potong stok gudang saat fisik barang diserahkan
                            $stockAfterOut = max(0, $product->stock - $qtyToDeduct);
                            $product->stock = $stockAfterOut;
                            $product->save();

                            // Catat mutasi Barang Keluar
                            StockTransaction::create([
                                'product_id'       => $product->id,
                                'user_id'          => $distributedById, // Petugas Gudang Distribusi yang menyerahkan
                                'type'             => 'keluar',
                                'recipient_type'   => $requestItem->type ?? 'individu',
                                'quantity'         => $qtyToDeduct,
                                'stock_after'      => $stockAfterOut,
                                'transaction_date' => now()->toDateString(),
                                'no_faktur'        => $requestItem->request_code,
                                'uraian'           => $penerimaUraian,
                                'kondisi'          => 'Baik',
                                'harga_perolehan'  => $product->purchase_price ?? 0,
                                'note'             => $noteText,
                            ]);
                        }
                    }
                }
            }

            $requestItem->update([
                'status'             => 'distributed',
                'distributed_at'     => now(),
                'distributed_by'     => $distributedById,
                'receiver_name'      => $request->receiver_name,
                'distribution_notes' => $request->distribution_notes,
                'distribution_proof' => $proofPath,
            ]);

            // Kirim notifikasi ke pemohon bahwa barang fisik telah diserahterimakan
            if ($requestItem->user) {
                UserNotification::create([
                    'user_id'    => $requestItem->user_id,
                    'title'      => 'Barang ATK Telah Diserahterimakan',
                    'message'    => "Permintaan ATK #{$requestItem->request_code} telah diserahkan oleh {$distributorName} dan diterima oleh {$request->receiver_name}.",
                    'type'       => 'distribution_completed',
                    'icon'       => 'check-circle',
                    'link'       => route('pengajuan.index'),
                    'request_id' => $requestItem->id,
                    'is_read'    => false,
                    'data'       => [
                        'request_code'  => $requestItem->request_code,
                        'receiver_name' => $request->receiver_name,
                        'distributor'   => $distributorName,
                    ],
                ]);
            }

            DB::commit();

            return redirect()->route('distribusi.index')
                ->with('success', 'Barang berhasil diserahterimakan ke ' . $request->receiver_name . ' (' . $requestItem->request_code . '). Mutasi barang keluar telah dicatat oleh Petugas Distribusi.' . ($proofPath ? ' Foto bukti serah terima berhasil diunggah.' : ''))
                ->with('print_id', $requestItem->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses serah terima barang: ' . $e->getMessage());
        }
    }

    /**
     * Upload / perbarui foto bukti penyerahan barang setelah didistribusikan
     */
    public function uploadProof(Request $request, $id)
    {
        $authUser = auth()->user();
        $isAuthorizedDistributor = $authUser && ($authUser->isKasubagTU() || ($authUser->isPegawaiGudang() && $authUser->isDistributor()));

        if (!$isAuthorizedDistributor) {
            return redirect()->route('dashboard')->with('error', 'Aksi upload foto bukti penyerahan hanya dapat dilakukan oleh Petugas Distribusi yang ditugaskan resmi atau Kasubbag TU.');
        }

        $requestItem = RequestItem::findOrFail($id);

        $request->validate([
            'distribution_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'distribution_proof.required' => 'Silakan pilih file foto bukti penyerahan terlebih dahulu.',
            'distribution_proof.image' => 'File bukti penyerahan harus berupa foto/gambar.',
            'distribution_proof.mimes' => 'Format foto harus berupa JPEG, PNG, JPG, atau WEBP.',
            'distribution_proof.max' => 'Ukuran foto bukti tidak boleh melebihi 5 MB.',
        ]);

        if ($requestItem->distribution_proof && Storage::disk('public')->exists($requestItem->distribution_proof)) {
            Storage::disk('public')->delete($requestItem->distribution_proof);
        }

        $file = $request->file('distribution_proof');
        $ext = strtolower($file->getClientOriginalExtension());
        $ext = in_array($ext, ['jpeg', 'png', 'jpg', 'webp']) ? $ext : 'jpg';
        $filename = 'proof_' . $requestItem->request_code . '_' . \Illuminate\Support\Str::random(16) . '.' . $ext;
        $proofPath = $file->storeAs('distribution_proofs', $filename, 'public');

        $requestItem->update([
            'distribution_proof' => $proofPath,
        ]);

        return redirect()->route('distribusi.index')
            ->with('success', 'Foto bukti serah terima barang untuk ' . $requestItem->request_code . ' berhasil disimpan.');
    }

    /**
     * Cetak Bukti Serah Terima Barang / Surat Jalan Distribusi ATK
     */
    public function printBukti($id)
    {
        $currentUser = auth()->user();

        $requestItem = RequestItem::with(['user', 'details.product', 'distributor'])
            ->findOrFail($id);

        // Validasi hak akses menyeluruh (Kasubbag TU, Petugas Gudang Distributor, Pemohon bersangkutan, atau PIC penanggung jawab)
        $canAccess = $currentUser && (
            $currentUser->isKasubagTU()
            || ($currentUser->isPegawaiGudang() && $currentUser->isDistributor())
            || ($requestItem->user_id === $currentUser->id)
            || ($currentUser->isPicDistribusi() && $currentUser->canHandleRequest($requestItem))
        );

        if (!$canAccess) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk melihat bukti serah terima permohonan ini.');
        }

        // Jika parameter ?view=foto, tampilkan foto dokumentasi
        if (request('view') === 'foto') {
            return view('distribusi.bukti_foto', compact('requestItem'));
        }

        // Tampilkan Dokumen Resmi Bukti Serah Terima Barang ATK (Gambar 3)
        return view('distribusi.bukti', compact('requestItem'));
    }
}
