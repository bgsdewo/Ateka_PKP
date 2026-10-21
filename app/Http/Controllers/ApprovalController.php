<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequestItem;
use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->isKasubagTU()) {
            return redirect()->route('dashboard')->with('error', 'Halaman Verifikasi & Persetujuan hanya dapat diakses oleh Kasubag TU.');
        }

        $category = $request->get('category'); // 'pengadaan' or 'persediaan' or null
        $statusFilter = $request->get('status');
        $typeFilter = $request->get('type');
        $search = $request->get('search');
        $monthInput = $request->get('month');
        $yearInput = $request->get('year');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $isAllMonths = ($monthInput === 'all');
        $selectedMonth = ($isAllMonths || $monthInput === null || $monthInput === '') ? null : (int) $monthInput;
        $selectedYear = ($yearInput === 'all' || $yearInput === null || $yearInput === '') ? null : (int) $yearInput;

        // Hitung pending tiket masing-masing kategori untuk tab switcher
        $pendingProcurementCount = RequestItem::where('status', 'pending')->where('is_procurement', true)->count();
        $pendingStockCount = RequestItem::where('status', 'pending')->where(function ($q) {
            $q->where('is_procurement', false)->orWhereNull('is_procurement');
        })->count();

        $baseQuery = RequestItem::query()
            ->when($category === 'pengadaan', function ($q) {
                $q->where('is_procurement', true);
            })
            ->when($category === 'persediaan', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('is_procurement', false)
                        ->orWhereNull('is_procurement');
                });
            })
            ->when($typeFilter, function ($q) use ($typeFilter) {
                $q->where('type', $typeFilter);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_code', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
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

        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (clone $baseQuery)->whereIn('status', ['approved', 'distributed'])->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'rejected')->count();
        $totalCount = (clone $baseQuery)->count();

        $query = (clone $baseQuery)->with([
            'user.requests' => function ($rq) {
                $rq->with(['details.product'])->latest('created_at');
            },
            'details.product'
        ])->latest('created_at')->latest('id');

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'approved') {
                $query->whereIn('status', ['approved', 'distributed']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('persetujuan.index', compact(
            'requests',
            'category',
            'statusFilter',
            'typeFilter',
            'search',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'isAllMonths',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'pendingProcurementCount',
            'pendingStockCount'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $item = RequestItem::with('details.product', 'user')->findOrFail($id);

        if ($item->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah pernah diproses verifikasi sebelumnya.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|max:255',
            'verification_notes' => 'nullable|string|max:1000',
            'approved_quantity' => 'nullable|array',
            'approved_quantity.*' => 'nullable|integer|min:0',
            'item_verification_notes' => 'nullable|array',
            'item_verification_notes.*' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            if ($request->status == 'approved') {
                $approvedQuantities = $request->input('approved_quantity', []);
                $itemNotes = $request->input('item_verification_notes', []);

                // 1. Validasi kuantitas disetujui
                $hasApprovedItems = false;
                foreach ($item->details as $detail) {
                    $approvedQty = isset($approvedQuantities[$detail->id])
                        ? (int) $approvedQuantities[$detail->id]
                        : (int) $detail->quantity;

                    if ($approvedQty > 0) {
                        $hasApprovedItems = true;
                    }

                    if ($approvedQty > $detail->quantity) {
                        $itemName = $detail->product ? $detail->product->name : ($detail->custom_product_name ?? 'Barang');
                        return redirect()->back()->with('error', 'Kuantitas disetujui untuk "' . $itemName . '" (' . $approvedQty . ') tidak boleh melebihi jumlah yang diajukan (' . $detail->quantity . ').');
                    }

                    // Jika permintaan persediaan (dari stok gudang), kuantitas disetujui tidak boleh melebihi sisa stok yang ada
                    if (!$item->is_procurement && $detail->product_id) {
                        $product = Product::find($detail->product_id);
                        if ($product && $approvedQty > $product->stock) {
                            return redirect()->back()->with('error', 'Kuantitas disetujui untuk "' . $product->name . '" (' . $approvedQty . ' Stock) melebihi sisa stok gudang (' . $product->stock . ' Stock).');
                        }
                    }
                }

                if (!$hasApprovedItems && count($approvedQuantities) > 0) {
                    return redirect()->back()->with('error', 'Jumlah barang yang disetujui tidak boleh 0 untuk semua barang. Jika ingin menolak permohonan, pilih opsi Tolak Permintaan.');
                }

                // 2. Simpan kuantitas yang disetujui & lakukan pemotongan stok jika barang persediaan
                foreach ($item->details as $detail) {
                    $approvedQty = isset($approvedQuantities[$detail->id])
                        ? (int) $approvedQuantities[$detail->id]
                        : (int) $detail->quantity;
                    $itemNote = $itemNotes[$detail->id] ?? null;

                    $detail->update([
                        'approved_quantity' => $approvedQty,
                        'verification_note' => $itemNote,
                    ]);

                    // Jika permintaan barang persediaan dari stok gudang & disetujui > 0
                    if (!$item->is_procurement && $detail->product_id && $approvedQty > 0) {
                        $product = Product::lockForUpdate()->find($detail->product_id);
                        if ($product) {
                            $stockAfter = max(0, $product->stock - $approvedQty);
                            $product->stock = $stockAfter;
                            $product->save();

                            $pemohonName = $item->user->name ?? 'Pegawai';
                            $pemohonUnit = $item->user->position ? ' (' . $item->user->position . ')' : '';
                            $uraianPenerima = $pemohonName . $pemohonUnit;

                            $noteText = 'Pengeluaran ' . $product->name . ' Untuk ' . $pemohonName;
                            if ($itemNote) {
                                $noteText .= ' - ' . $itemNote;
                            } elseif ($request->filled('verification_notes')) {
                                $noteText .= ' - ' . $request->verification_notes;
                            }

                            // Catat transaksi Barang Keluar secara real-time
                            StockTransaction::create([
                                'product_id'       => $product->id,
                                'user_id'          => Auth::id(),
                                'type'             => 'keluar',
                                'recipient_type'   => $item->type ?? 'individu',
                                'quantity'         => $approvedQty,
                                'stock_after'      => $stockAfter,
                                'transaction_date' => now()->toDateString(),
                                'no_faktur'        => $item->request_code,
                                'uraian'           => $uraianPenerima,
                                'kondisi'          => 'Baik',
                                'harga_perolehan'  => $product->purchase_price ?? 0,
                                'note'             => $noteText,
                            ]);
                        }
                    }

                    // Jika pengadaan barang: jangan tambahkan stok otomatis.
                    // Stok baru akan diisi oleh Kasubag TU atau Pegawai Gudang melalui menu Daftar Barang (Master Data) atau Barang Masuk setelah barang fisik dibeli.
                    if ($item->is_procurement) {
                        if (!$detail->product_id) {
                            $itemName = $detail->custom_product_name ?: $detail->item_name;
                            $existingProduct = Product::where('name', $itemName)->first();
                            if ($existingProduct) {
                                $detail->update(['product_id' => $existingProduct->id]);
                            }
                        }
                    }
                }

                // Update status tiket pengajuan
                $item->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'verification_notes' => $request->verification_notes,
                    'rejection_reason' => null,
                ]);

                if ($item->is_procurement) {
                    // Kirim notifikasi ke pemohon bahwa usulan pengadaan telah disetujui (dalam proses pembelian)
                    if ($item->user) {
                        foreach ($item->details as $d) {
                            $itemName = $d->product ? $d->product->name : ($d->custom_product_name ?: 'Barang');
                            $itemUnit = $d->product ? ($d->product->unit ?: 'Pcs') : ($d->custom_unit ?: 'Pcs');
                            if ($d->approved_quantity > 0) {
                                UserNotification::create([
                                    'user_id'    => $item->user_id,
                                    'title'      => 'Usulan Pengadaan Disetujui',
                                    'message'    => "Usulan pengadaan \"{$itemName}\" telah disetujui Kasubag TU sebanyak {$d->approved_quantity} {$itemUnit}. Barang sedang dalam proses pembelian oleh Subbag TU & Gudang.",
                                    'type'       => 'procurement_approved',
                                    'icon'       => 'shopping-bag',
                                    'link'       => route('pengajuan.index'),
                                    'request_id' => $item->id,
                                    'product_id' => $d->product_id,
                                    'is_read'    => false,
                                    'data'       => [
                                        'product_name' => $itemName,
                                        'request_code' => $item->request_code,
                                        'approved_quantity' => $d->approved_quantity,
                                    ],
                                ]);
                            }
                        }
                    }
                    $pesan = 'Usulan pengadaan barang (' . $item->request_code . ') berhasil disetujui! Status masuk tahap proses pembelian sebelum dicatat ke daftar barang oleh Kasubag TU / Pegawai Gudang.';
                } else {
                    $pesan = 'Permintaan barang persediaan (' . $item->request_code . ') berhasil disetujui! Stok telah dipotong dan tercatat di Barang Keluar secara real-time.';
                }
            } else {
                // Status rejected
                foreach ($item->details as $detail) {
                    $detail->update([
                        'approved_quantity' => 0,
                        'verification_note' => $request->rejection_reason,
                    ]);
                }

                $item->update([
                    'status' => 'rejected',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                    'verification_notes' => $request->verification_notes ?? $request->rejection_reason,
                    'rejection_reason' => $request->rejection_reason,
                ]);

                $pesan = 'Permintaan (' . $item->request_code . ') berhasil ditolak oleh Subbag TU.';
            }

            DB::commit();

            $redirectParams = $request->filled('category') ? ['category' => $request->input('category')] : [];
            return redirect()->route('persetujuan.index', $redirectParams)->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Pembelian / Pengadaan Barang Masuk Sesuai Permintaan Pegawai/Subdit (Kasubag TU)
     */
    public function procureAndApprove(Request $request, $id)
    {
        $item = RequestItem::with('details.product', 'user')->findOrFail($id);

        $request->validate([
            'no_faktur' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'procure_quantity' => 'required|array',
            'procure_quantity.*' => 'required|integer|min:1',
            'purchase_price' => 'nullable|array',
            'purchase_price.*' => 'nullable|numeric|min:0',
            'purchase_notes' => 'nullable|string|max:1000',
        ], [
            'no_faktur.required' => 'Nomor faktur / nota pembelian wajib diisi.',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'procure_quantity.required' => 'Jumlah barang yang dibeli wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = 0;
            $procureQuantities = $request->input('procure_quantity', []);
            $prices = $request->input('purchase_price', []);

            foreach ($item->details as $detail) {
                $procureQty = isset($procureQuantities[$detail->id]) ? (int) $procureQuantities[$detail->id] : (int) $detail->quantity;
                $product = $detail->product;

                $price = isset($prices[$detail->id]) && (float) $prices[$detail->id] > 0
                    ? (float) $prices[$detail->id]
                    : (float) ($product->purchase_price ?? 0);

                // Jika produk belum terdaftar di gudang (item baru usulan pengadaan), daftarkan ke master produk
                if (!$product) {
                    $itemName = $detail->custom_product_name ?: 'Barang Pengadaan';
                    $product = Product::where('name', $itemName)->first();
                    if (!$product) {
                        $category = \App\Models\Category::firstOrCreate(['name' => 'Alat Tulis']);
                        $productCode = 'ATK-' . strtoupper(Str::random(5));
                        $product = Product::create([
                            'category_id' => $category->id,
                            'code' => $productCode,
                            'name' => $itemName,
                            'unit' => $detail->custom_unit ?: 'Pcs',
                            'stock' => 0,
                            'minimum_stock' => 5,
                            'purchase_price' => $price,
                        ]);
                    }
                    $detail->update(['product_id' => $product->id]);
                }

                $totalCost += ($price * $procureQty);

                // 1. Catat Transaksi Masuk (Barang Masuk Hasil Pembelian Kasubag TU)
                $stockAfterIn = $product->stock + $procureQty;
                StockTransaction::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::id(),
                    'type'             => 'masuk',
                    'quantity'         => $procureQty,
                    'stock_after'      => $stockAfterIn,
                    'no_faktur'        => $request->no_faktur,
                    'uraian'           => $request->supplier_name ?: 'Pengadaan ATK untuk ' . ($item->user->name ?? 'Pemohon') . ' (' . $item->request_code . ')',
                    'kondisi'          => 'Baik',
                    'harga_perolehan'  => $price,
                    'transaction_date' => $request->purchase_date,
                    'note'             => 'Pembelian ' . $product->name
                ]);

                // Update master produk harga & stok sementara masuk
                $product->stock = $stockAfterIn;
                if ($price > 0) {
                    $product->purchase_price = $price;
                }
                $product->save();

                // 2. Alokasikan barang untuk pemohon (Disetujui)
                $approvedQty = min($procureQty, $detail->quantity);
                $detail->update([
                    'approved_quantity' => $approvedQty,
                    'verification_note' => 'Dibeli melalui Faktur: ' . $request->no_faktur . ($request->supplier_name ? ' (' . $request->supplier_name . ')' : ''),
                ]);

                // 3. Catat Transaksi Keluar (Alokasi Persediaan untuk Pemohon)
                $stockAfterOut = $product->stock - $approvedQty;
                $product->stock = $stockAfterOut;
                $product->save();

                StockTransaction::create([
                    'product_id'       => $product->id,
                    'user_id'          => Auth::id(),
                    'type'             => 'keluar',
                    'recipient_type'   => $item->type ?? 'individu',
                    'quantity'         => $approvedQty,
                    'stock_after'      => $stockAfterOut,
                    'uraian'           => $item->user->name ?? 'Pegawai',
                    'kondisi'          => 'Baik',
                    'harga_perolehan'  => $price,
                    'transaction_date' => $request->purchase_date,
                    'note'             => 'Pengeluaran ' . $product->name . ' Untuk ' . ($item->user->name ?? 'Pegawai') . ' via Faktur ' . $request->no_faktur
                ]);
            }

            // 4. Update Header Request
            $item->update([
                'status'              => 'approved',
                'verified_at'         => now(),
                'verified_by'         => Auth::id(),
                'purchase_invoice'    => $request->no_faktur,
                'purchase_date'       => $request->purchase_date,
                'purchase_price'      => $totalCost,
                'purchase_notes'      => $request->purchase_notes,
                'verification_notes'  => 'Barang telah dibeli oleh Kasubag TU (Faktur: ' . $request->no_faktur . ') dan siap diserahterimakan.',
                'rejection_reason'    => null,
            ]);

            // Kirim notifikasi konsolidasian ke pemohon bahwa barang pengadaan telah dibeli dan tersedia di daftar barang
            if ($item->user) {
                $purchasedProducts = $item->details->map(fn($det) => $det->product)->filter()->unique('id');
                if ($purchasedProducts->isNotEmpty()) {
                    UserNotification::sendConsolidatedProcurementNotification($item, $purchasedProducts, $request->no_faktur);
                }
            }

            DB::commit();

            return redirect()->route('persetujuan.index')
                ->with('success', 'Barang berhasil dibeli dan dicatat di Barang Masuk! Notifikasi telah dikirimkan ke pemohon usulan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses pembelian: ' . $e->getMessage());
        }
    }
}
