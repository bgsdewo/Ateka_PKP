<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use App\Models\Product;
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
        if (!auth()->user()->isPicDistribusi()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Distribusi Barang hanya dapat diakses oleh PIC Distribusi.');
        }

        $picUser = auth()->user();
        $filterType = $request->get('type'); // individu, subdit, or null
        $filterStatus = $request->get('status', 'all'); // default to 'all' so handed over items remain visible
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
            ->where(function ($q) {
                $q->where('is_procurement', false)
                  ->orWhereNull('is_procurement');
            })
            ->forPic($picUser)
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

        // Rekap statistik untuk PIC Distribusi
        $totalReady = (clone $baseQuery)->where('status', 'approved')->count();
        $totalDistributed = (clone $baseQuery)->where('status', 'distributed')->count();
        $totalSubditReady = (clone $baseQuery)->where('status', 'approved')->where('type', 'subdit')->count();
        $totalIndividuReady = (clone $baseQuery)->where('status', 'approved')->where('type', 'individu')->count();

        // Rekapitulasi kolektif per Subdirektorat
        $subditGroups = (clone $baseQuery)
            ->where('status', 'approved')
            ->where('type', 'subdit')
            ->get()
            ->groupBy(function ($item) {
                return $item->user->position ?? ($item->user->name ?? 'Subdit Umum');
            });

        $query = (clone $baseQuery)->latest('updated_at')->latest('id');

        if ($filterStatus === 'ready') {
            $query->where('status', 'approved');
        } elseif ($filterStatus === 'distributed') {
            $query->where('status', 'distributed');
        }

        $requests = $query->paginate(15)->withQueryString();

        return view('distribusi.index', compact(
            'requests',
            'totalReady',
            'totalDistributed',
            'totalSubditReady',
            'totalIndividuReady',
            'subditGroups',
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
        if (!auth()->user()->isPicDistribusi()) {
            return redirect()->route('dashboard')->with('error', 'Aksi penyerahan barang hanya dapat dilakukan oleh PIC Distribusi.');
        }

        $picUser = auth()->user();

        $requestItem = RequestItem::with(['user', 'details.product'])
            ->where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            })
            ->forPic($picUser)
            ->findOrFail($id);

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'distribution_notes' => 'nullable|string',
            'distribution_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'receiver_name.required' => 'Nama penerima barang fisik wajib diisi.',
            'distribution_proof.image' => 'File bukti penyerahan harus berupa foto/gambar.',
            'distribution_proof.mimes' => 'Format foto harus berupa JPEG, PNG, JPG, atau WEBP.',
            'distribution_proof.max' => 'Ukuran foto bukti tidak boleh melebihi 5 MB.',
        ]);

        $proofPath = $requestItem->distribution_proof;
        if ($request->hasFile('distribution_proof')) {
            if ($proofPath && Storage::disk('public')->exists($proofPath)) {
                Storage::disk('public')->delete($proofPath);
            }
            $file = $request->file('distribution_proof');
            $filename = 'proof_' . $requestItem->request_code . '_' . time() . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('distribution_proofs', $filename, 'public');
        }

        $requestItem->update([
            'status' => 'distributed',
            'distributed_at' => now(),
            'distributed_by' => Auth::id(),
            'receiver_name' => $request->receiver_name,
            'distribution_notes' => $request->distribution_notes,
            'distribution_proof' => $proofPath,
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil diserahterimakan ke ' . $request->receiver_name . ' (' . $requestItem->request_code . ').' . ($proofPath ? ' Foto bukti serah terima berhasil diunggah.' : ''))
            ->with('print_id', $requestItem->id);
    }

    /**
     * Upload / perbarui foto bukti penyerahan barang setelah didistribusikan
     */
    public function uploadProof(Request $request, $id)
    {
        if (!auth()->user()->isPicDistribusi()) {
            return redirect()->route('dashboard')->with('error', 'Aksi ini hanya dapat dilakukan oleh PIC Distribusi.');
        }

        $picUser = auth()->user();

        $requestItem = RequestItem::where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            })
            ->forPic($picUser)
            ->findOrFail($id);

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
        $filename = 'proof_' . $requestItem->request_code . '_' . time() . '.' . $file->getClientOriginalExtension();
        $proofPath = $file->storeAs('distribution_proofs', $filename, 'public');

        $requestItem->update([
            'distribution_proof' => $proofPath,
        ]);

        return redirect()->back()
            ->with('success', 'Foto bukti serah terima barang untuk ' . $requestItem->request_code . ' berhasil disimpan.');
    }

    /**
     * Cetak Bukti Serah Terima Barang / Surat Jalan Distribusi ATK
     */
    public function printBukti($id)
    {
        $currentUser = auth()->user();

        $requestItem = RequestItem::with(['user', 'details.product', 'distributor'])
            ->where(function ($q) {
                $q->where('is_procurement', false)->orWhereNull('is_procurement');
            })
            ->findOrFail($id);

        // Jika PIC Distribusi yang mengakses, pastikan hanya bukti dari divisi yang ditanganinya
        if ($currentUser && $currentUser->isPicDistribusi() && !$currentUser->canHandleRequest($requestItem)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak bukti serah terima barang dari divisi ini.');
        }

        // Jika Pegawai atau Subdit: hanya tampilkan bukti upload foto yang diunggah oleh PIC
        if ($currentUser && ($currentUser->isPegawai() || $currentUser->isSubdit())) {
            if ($requestItem->user_id !== $currentUser->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti serah terima permintaan ini.');
            }
            return view('distribusi.bukti_foto', compact('requestItem'));
        }

        return view('distribusi.bukti', compact('requestItem'));
    }
}
