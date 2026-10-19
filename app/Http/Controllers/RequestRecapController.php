<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use App\Models\RequestDetail;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestRecapController extends Controller
{
    /**
     * Halaman Rekapitulasi Permintaan ATK (Subbag TU)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['kasubag_tu', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Rekapitulasi Subbag TU.');
        }

        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $search = $request->get('search');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $activeTab = $request->get('tab', 'pemohon'); // 'pemohon' or 'barang'

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        $baseQuery = RequestItem::query()
            ->when($typeFilter, function ($q) use ($typeFilter) {
                $q->where('type', $typeFilter);
            })
            ->when($statusFilter && $statusFilter !== 'all', function ($q) use ($statusFilter) {
                $q->where('status', $statusFilter);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhere('verification_notes', 'like', "%{$search}%")
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
            ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        // Statistik KPI Rekapitulasi
        $totalRequests = (clone $baseQuery)->count();
        $subditCount = (clone $baseQuery)->where('type', 'subdit')->count();
        $individuCount = (clone $baseQuery)->where('type', 'individu')->count();

        // Total Usulan Disetujui TU (berbasis tiket usulan, persis format total usulan masuk)
        $approvedQuery = (clone $baseQuery)->whereIn('status', ['approved', 'distributed']);
        $approvedRequestsCount = (clone $approvedQuery)->count();
        $subditApprovedCount = (clone $approvedQuery)->where('type', 'subdit')->count();
        $individuApprovedCount = (clone $approvedQuery)->where('type', 'individu')->count();

        // Usulan Menunggu Verifikasi
        $pendingQuery = (clone $baseQuery)->where('status', 'pending');
        $pendingCount = (clone $pendingQuery)->count();
        $subditPendingCount = (clone $pendingQuery)->where('type', 'subdit')->count();
        $individuPendingCount = (clone $pendingQuery)->where('type', 'individu')->count();

        $approvedCount = (clone $baseQuery)->where('status', 'approved')->count();
        $distributedCount = (clone $baseQuery)->where('status', 'distributed')->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();

        // Total kuantitas barang & ID permohonan yang cocok
        $matchingRequestIds = (clone $baseQuery)->pluck('id');
        $totalQtyRequested = RequestDetail::whereIn('request_id', $matchingRequestIds)->sum('quantity');
        $totalQtyApproved = RequestDetail::whereIn('request_id', $matchingRequestIds)
            ->whereHas('requestItem', fn($rq) => $rq->whereIn('status', ['approved', 'distributed']))
            ->sum(DB::raw('COALESCE(approved_quantity, quantity)'));

        // Tab 1: Data Pengajuan per Pemohon / Subdit
        $requests = (clone $baseQuery)
            ->with(['user', 'verifier', 'distributor', 'details.product.category'])
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Tab 2: Rekapitulasi Kebutuhan per Barang ATK (mengelompokkan katalog & usulan pengadaan)
        $productRecap = RequestDetail::whereIn('request_id', $matchingRequestIds)
            ->with(['product.category', 'requestItem.user'])
            ->get()
            ->groupBy(function ($item) {
                return $item->product_id
                    ? 'prod_' . $item->product_id
                    : 'custom_' . strtolower(trim($item->custom_product_name ?: $item->item_name ?: 'item_' . $item->id));
            })
            ->map(function ($details) {
                $firstDetail = $details->first();
                $product = $firstDetail->product;

                $totalReq = $details->sum('quantity');
                $totalApp = $details->filter(function ($d) {
                    return in_array($d->requestItem->status ?? '', ['approved', 'distributed']);
                })->sum(function ($d) {
                    return $d->approved_quantity !== null ? $d->approved_quantity : $d->quantity;
                });

                // Pemohon breakdown
                $requesterBreakdown = $details->groupBy(function ($d) {
                    $u = $d->requestItem->user ?? null;
                    $userName = $u ? ($u->name_with_title ?: $u->name) : 'Pegawai';
                    return $userName . ' (' . ($u->position ?? 'Unit') . ')';
                })->map(function ($group) {
                    return $group->sum('quantity');
                });

                $itemName = $firstDetail->item_name ?: ($product->name ?? 'Barang Permintaan');
                $categoryName = $product->category->name ?? 'Usulan Pengadaan';
                $unitName = $firstDetail->item_unit ?: ($product->unit ?? 'Pcs');

                return (object) [
                    'product_id' => $product->id ?? null,
                    'code' => $product->code ?? '-',
                    'name' => $itemName,
                    'category' => $categoryName,
                    'stock' => $product->stock ?? 0,
                    'unit' => $unitName,
                    'total_requested' => $totalReq,
                    'total_approved' => $totalApp,
                    'requester_count' => $details->pluck('requestItem.user_id')->unique()->count(),
                    'requester_breakdown' => $requesterBreakdown,
                ];
            })
            ->sortBy('name')
            ->values();

        $totalItemTypes = $productRecap->count();

        // Periode Label
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        if ($startDate && $endDate) {
            $periodLabel = date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));
        } elseif ($selectedMonth && $selectedYear) {
            $periodLabel = ($monthNames[$selectedMonth] ?? "Bulan $selectedMonth") . " {$selectedYear}";
        } elseif ($selectedMonth && !$selectedYear) {
            $periodLabel = ($monthNames[$selectedMonth] ?? "Bulan $selectedMonth") . " (Semua Tahun)";
        } elseif (!$selectedMonth && $selectedYear) {
            $periodLabel = "Tahun {$selectedYear}";
        } else {
            $periodLabel = "Semua Periode";
        }

        return view('rekapitulasi_permintaan.index', compact(
            'requests',
            'productRecap',
            'statusFilter',
            'typeFilter',
            'search',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'isAllMonths',
            'activeTab',
            'periodLabel',
            'totalRequests',
            'pendingCount',
            'approvedCount',
            'distributedCount',
            'rejectedCount',
            'subditCount',
            'individuCount',
            'approvedRequestsCount',
            'subditApprovedCount',
            'individuApprovedCount',
            'subditPendingCount',
            'individuPendingCount',
            'totalItemTypes',
            'totalQtyRequested',
            'totalQtyApproved'
        ));
    }

    /**
     * Cetak Dokumen Resmi Rekapitulasi Usulan Permintaan ATK
     */
    public function printRecap(Request $request, $id = null)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['kasubag_tu', 'admin', 'pic_distribusi', 'pegawai'])) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman Cetak Dokumen.');
        }

        $requestId = $id ?? $request->get('request_id');

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // 1. JIKA CETAK PER TIKET (DARI KOLOM AKSI):
        if ($requestId) {
            $singleReq = RequestItem::with(['user', 'details.product.category', 'distributor', 'verifier', 'signedBy'])->findOrFail($requestId);
            $requestItem = $singleReq;
            $reqUser = $singleReq->user;

            $signerInfo = $this->resolveSignerInfo($reqUser, $singleReq->type);
            $subditLabel = $signerInfo['subditLabel'];
            $signerName = $signerInfo['signerName'];
            $signerNip = $signerInfo['signerNip'];

            // Bulan
            $createdDate = $singleReq->created_at;
            $monthPeriodLabel = ($monthNames[(int) $createdDate->format('n')] ?? $createdDate->format('F')) . ' ' . $createdDate->format('Y');

            // No. Tiket
            $ticketNumbersLabel = $singleReq->request_code;

            // Team Name untuk NAMA PENGGUNA BARANG (TIM TU, TIM SDK, TIM PJF, TIM KBR)
            $teamName = $this->resolveTeamName($reqUser, $singleReq->type);

            // Rincian Barang Tiket Ini
            $items = collect();
            foreach ($singleReq->details as $det) {
                $qtyToDisplay = ($det->approved_quantity !== null) ? (int) $det->approved_quantity : (int) $det->quantity;
                $items->push((object) [
                    'request_code' => $singleReq->request_code,
                    'item_name' => $det->item_name,
                    'specification' => $det->specification,
                    'quantity' => $qtyToDisplay,
                    'requested_quantity' => $det->quantity,
                    'approved_quantity' => $det->approved_quantity,
                    'unit' => $det->item_unit,
                    'effective_quantity' => $det->effective_quantity,
                    'user_or_spec' => $teamName,
                ]);
            }

            $signingDate = $createdDate->format('j') . ' ' . ($monthNames[(int) $createdDate->format('n')] ?? $createdDate->format('F')) . ' ' . $createdDate->format('Y');

            $query = collect([$singleReq]);
            $productRecap = collect();
            $periodLabel = $monthPeriodLabel;
            $typeFilter = $singleReq->type;
            $statusFilter = $singleReq->status;

            return view('rekapitulasi_permintaan.cetak', compact(
                'requestItem',
                'singleReq',
                'query',
                'productRecap',
                'items',
                'subditLabel',
                'monthPeriodLabel',
                'ticketNumbersLabel',
                'signerName',
                'signerNip',
                'signingDate',
                'periodLabel',
                'typeFilter',
                'statusFilter'
            ));
        }

        // 2. JIKA CETAK MULTI-TIKET (FILTER PERIODE / FALLBACK)
        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        $query = RequestItem::with(['user', 'verifier', 'details.product.category'])
            ->when($typeFilter, fn($q) => $q->where('type', $typeFilter))
            ->when($statusFilter && $statusFilter !== 'all', fn($q) => $q->where('status', $statusFilter))
            ->when($selectedYear, fn($q) => $q->whereYear('created_at', $selectedYear))
            ->when($selectedMonth, fn($q) => $q->whereMonth('created_at', $selectedMonth))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->latest('created_at')
            ->get();

        $matchingRequestIds = $query->pluck('id');

        $productRecap = RequestDetail::whereIn('request_id', $matchingRequestIds)
            ->with(['product.category', 'requestItem.user'])
            ->get()
            ->groupBy(function ($item) {
                return $item->product_id
                    ? 'prod_' . $item->product_id
                    : 'custom_' . strtolower(trim($item->custom_product_name ?: $item->item_name ?: 'item_' . $item->id));
            })
            ->map(function ($details) {
                $firstDetail = $details->first();
                $product = $firstDetail->product;

                $totalReq = $details->sum('quantity');
                $totalApp = $details->filter(fn($d) => in_array($d->requestItem->status ?? '', ['approved', 'distributed']))
                    ->sum(fn($d) => $d->approved_quantity !== null ? $d->approved_quantity : $d->quantity);

                $itemName = $firstDetail->item_name ?: ($product->name ?? 'Barang Permintaan');
                $categoryName = $product->category->name ?? 'Usulan Pengadaan';
                $unitName = $firstDetail->item_unit ?: ($product->unit ?? 'Pcs');

                return (object) [
                    'code' => $product->code ?? '-',
                    'name' => $itemName,
                    'category' => $categoryName,
                    'stock' => $product->stock ?? 0,
                    'unit' => $unitName,
                    'total_requested' => $totalReq,
                    'total_approved' => $totalApp,
                    'requester_count' => $details->pluck('requestItem.user_id')->unique()->count(),
                ];
            })
            ->sortBy('name')
            ->values();

        if ($startDate && $endDate) {
            $periodLabel = date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));
        } elseif ($selectedMonth && $selectedYear) {
            $periodLabel = ($monthNames[$selectedMonth] ?? "Bulan $selectedMonth") . " {$selectedYear}";
        } elseif ($selectedMonth && !$selectedYear) {
            $periodLabel = ($monthNames[$selectedMonth] ?? "Bulan $selectedMonth") . " (Semua Tahun)";
        } elseif (!$selectedMonth && $selectedYear) {
            $periodLabel = "Tahun {$selectedYear}";
        } else {
            $periodLabel = "Semua Periode";
        }

        // Rincian daftar barang usulan sesuai format resmi Image 1
        $items = collect();
        foreach ($query as $req) {
            $reqTeamName = $this->resolveTeamName($req->user, $req->type);
            foreach ($req->details as $det) {
                $qtyToDisplay = ($det->approved_quantity !== null) ? (int) $det->approved_quantity : (int) $det->quantity;
                $items->push((object) [
                    'request_code' => $req->request_code,
                    'request_id' => $req->id,
                    'created_at' => $req->created_at,
                    'user' => $req->user,
                    'item_name' => $det->item_name,
                    'specification' => $det->specification,
                    'quantity' => $qtyToDisplay,
                    'requested_quantity' => $det->quantity,
                    'approved_quantity' => $det->approved_quantity,
                    'unit' => $det->item_unit,
                    'effective_quantity' => $det->effective_quantity,
                    'user_or_spec' => $reqTeamName,
                ]);
            }
        }

        // Subdirektorat/Subbagian label
        if ($query->isNotEmpty()) {
            $uniqueSubdits = $query->map(function ($r) {
                $info = $this->resolveSignerInfo($r->user, $r->type);
                return $info['subditLabel'];
            })->unique()->filter()->values();

            $subditLabel = $uniqueSubdits->count() === 1
                ? $uniqueSubdits->first()
                : ($uniqueSubdits->count() > 1 ? $uniqueSubdits->join(', ') : 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman');
        } else {
            $subditLabel = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
        }

        // Bulan Periode label
        if ($selectedMonth && $selectedYear) {
            $monthPeriodLabel = ($monthNames[$selectedMonth] ?? '') . ' ' . $selectedYear;
        } elseif ($selectedMonth && !$selectedYear) {
            $monthPeriodLabel = ($monthNames[$selectedMonth] ?? '') . ' ' . date('Y');
        } elseif ($query->isNotEmpty()) {
            $firstDate = $query->first()->created_at;
            $monthPeriodLabel = ($monthNames[(int) $firstDate->format('n')] ?? $firstDate->format('F')) . ' ' . $firstDate->format('Y');
        } else {
            $monthPeriodLabel = ($monthNames[(int) date('n')] ?? date('F')) . ' ' . date('Y');
        }

        // No. Tiket label
        $ticketCodes = $query->pluck('request_code')->unique()->values();
        $ticketNumbersLabel = $ticketCodes->count() === 1
            ? $ticketCodes->first()
            : ($ticketCodes->count() > 1 ? $ticketCodes->join(', ') : '-');

        // Yang Mengajukan & NIP
        if ($query->isNotEmpty()) {
            $firstReq = $query->first();
            $signerInfo = $this->resolveSignerInfo($firstReq->user, $firstReq->type);
            $signerName = $signerInfo['signerName'];
            $signerNip = $signerInfo['signerNip'];

            $firstDate = $query->first()->created_at;
            $signingDate = $firstDate->format('j') . ' ' . ($monthNames[(int) $firstDate->format('n')] ?? $firstDate->format('F')) . ' ' . $firstDate->format('Y');
        } else {
            $signerInfo = $this->resolveSignerInfo(auth()->user(), $typeFilter ?? 'subdit');
            $signerName = $signerInfo['signerName'];
            $signerNip = $signerInfo['signerNip'];
            $signingDate = date('j') . ' ' . ($monthNames[(int) date('n')] ?? date('F')) . ' ' . date('Y');
        }

        return view('rekapitulasi_permintaan.cetak', compact(
            'query',
            'productRecap',
            'items',
            'subditLabel',
            'monthPeriodLabel',
            'ticketNumbersLabel',
            'signerName',
            'signerNip',
            'signingDate',
            'periodLabel',
            'typeFilter',
            'statusFilter'
        ));
    }

    /**
     * Check if user / request is from Pegawai Individu
     */
    private function isPegawai($user, $type = 'subdit'): bool
    {
        if ($type === 'individu') {
            return true;
        }
        if (!$user) {
            return false;
        }

        $email = strtolower($user->email ?? '');
        $role = strtolower($user->role ?? '');

        return $role === 'pegawai' || str_contains($email, 'pegawai');
    }

    /**
     * Check if user / request belongs to Subbagian Tata Usaha (Subdit TU)
     */
    private function isTu($user): bool
    {
        if (!$user) {
            return false;
        }

        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $subdit = strtolower($user->subdit_name ?? '');
        $position = strtolower($user->position ?? '');

        if ($email === 'tu@gmail.com' || str_contains($email, 'tu@') || str_starts_with($email, 'tu.')) {
            return true;
        }
        if (str_contains($name, 'devi') || preg_match('/\btu\b/i', $name) || str_contains($name, 'tata usaha')) {
            return true;
        }
        if (preg_match('/\btu\b/i', $subdit) || str_contains($subdit, 'tata usaha')) {
            return true;
        }
        if (preg_match('/\btu\b/i', $position) || str_contains($position, 'tata usaha')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user belongs to Subdit SDK
     */
    private function isSdk($user): bool
    {
        if (!$user) {
            return false;
        }

        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $subdit = strtolower($user->subdit_name ?? '');
        $position = strtolower($user->position ?? '');

        if (str_contains($email, 'sdk') || str_contains($name, 'fairuz') || preg_match('/\bsdk\b/i', $name)) {
            return true;
        }
        if (str_contains($subdit, 'sumber daya konstruksi') || preg_match('/\bsdk\b/i', $subdit)) {
            return true;
        }
        if (str_contains($position, 'sumber daya konstruksi') || preg_match('/\bsdk\b/i', $position)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user belongs to Subdit PJF
     */
    private function isPjf($user): bool
    {
        if (!$user) {
            return false;
        }

        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $subdit = strtolower($user->subdit_name ?? '');
        $position = strtolower($user->position ?? '');

        return str_contains($email, 'pjf') || preg_match('/\bpjf\b/i', $name) || preg_match('/\bpjf\b/i', $subdit) || preg_match('/\bpjf\b/i', $position) || str_contains($subdit, 'jabatan fungsional') || str_contains($position, 'jabatan fungsional');
    }

    /**
     * Check if user belongs to Subdit KBR
     */
    private function isKbr($user): bool
    {
        if (!$user) {
            return false;
        }

        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $subdit = strtolower($user->subdit_name ?? '');
        $position = strtolower($user->position ?? '');

        return str_contains($email, 'kbr') || preg_match('/\bkbr\b/i', $name) || preg_match('/\bkbr\b/i', $subdit) || preg_match('/\bkbr\b/i', $position) || str_contains($subdit, 'kendala bangunan') || str_contains($position, 'kendala bangunan');
    }

    /**
     * Resolve Team Name for "NAMA PENGGUNA BARANG" column (TIM TU, TIM SDK, TIM PJF, TIM KBR)
     */
    private function resolveTeamName($user, $type = 'subdit'): string
    {
        // Jika Pegawai individu -> nama pemohonnya (misal Pegawai)
        if ($this->isPegawai($user, $type)) {
            return ($user && $user->name) ? ($user->name_with_title ?: $user->name) : 'Pegawai';
        }

        // Subdit TU -> TIM TU
        if ($this->isTu($user)) {
            return 'TIM TU';
        }

        // Subdit SDK -> TIM SDK
        if ($this->isSdk($user)) {
            return 'TIM SDK';
        }

        // Subdit PJF -> TIM PJF
        if ($this->isPjf($user)) {
            return 'TIM PJF';
        }

        // Subdit KBR -> TIM KBR
        if ($this->isKbr($user)) {
            return 'TIM KBR';
        }

        return 'TIM ' . strtoupper($user ? ($user->subdit_name ?: ($user->position ?: $user->name)) : 'SUBDIT');
    }

    /**
     * Resolve Signer and Subdit Information secara real-time dari data akun pemohon terdaftar
     */
    private function resolveSignerInfo($user, $type = 'subdit'): array
    {
        if ($user) {
            $name = $user->name_with_title ?: $user->name;
            $nip = ($user->nip && $user->nip !== '-') ? $user->nip : '-';

            if (!empty($user->subdit_name)) {
                $subdit = $user->subdit_name;
            } elseif ($this->isTu($user)) {
                $subdit = 'Subbagian Tata Usaha';
            } elseif ($this->isSdk($user)) {
                $subdit = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif ($this->isPjf($user)) {
                $subdit = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif ($this->isKbr($user)) {
                $subdit = 'Subdirektorat Kendala Bangunan Rumah';
            } else {
                $subdit = $user->position ?: ($type === 'subdit' ? 'Subdirektorat' : 'Pegawai Perorangan');
            }

            return [
                'signerName' => $name,
                'signerNip' => $nip,
                'subditLabel' => $subdit,
            ];
        }

        return [
            'signerName' => 'Pengaju Permintaan',
            'signerNip' => '-',
            'subditLabel' => $type === 'subdit' ? 'Subdirektorat' : 'Pegawai Perorangan',
        ];
    }
}
