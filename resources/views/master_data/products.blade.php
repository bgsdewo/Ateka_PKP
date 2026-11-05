@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Daftar Barang ATK</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .pengadaan-pagination {
        width: 100% !important;
    }
    .pengadaan-pagination nav {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .pengadaan-pagination nav > div:last-child {
        width: 100% !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 1rem !important;
    }
    .pengadaan-pagination p {
        margin-bottom: 0 !important;
        font-size: 0.82rem !important;
        color: #64748b !important;
    }
    .pengadaan-pagination .pagination {
        margin-bottom: 0 !important;
        margin-left: auto !important;
        gap: 3px !important;
        display: flex !important;
        align-items: center !important;
    }
    .pengadaan-pagination .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 28px !important;
        height: 28px !important;
        padding: 0 0.4rem !important;
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        border-radius: 5px !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        text-decoration: none !important;
        background: #ffffff !important;
        transition: all 0.15s ease !important;
    }
    .pengadaan-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25) !important;
    }
    .pengadaan-pagination .page-link:hover:not(.active) {
        background: #f1f5f9 !important;
        color: #0284c7 !important;
        border-color: #cbd5e1 !important;
    }
    .pengadaan-pagination .page-item.disabled .page-link {
        opacity: 0.45 !important;
        background: #f8fafc !important;
        color: #94a3b8 !important;
    }

    /* Styling Pagination DataTables Master Data Barang ATK */
    #datatable_paginate,
    .dataTables_paginate {
        margin-left: auto !important;
        margin-right: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
    }
    #datatable_paginate .pagination,
    .dataTables_paginate .pagination {
        margin-bottom: 0 !important;
        margin-left: auto !important;
        gap: 3px !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }
    #datatable_paginate .page-item,
    .dataTables_paginate .page-item {
        margin: 0 !important;
        padding: 0 !important;
        display: inline-flex !important;
    }
    #datatable_paginate .page-link,
    .dataTables_paginate .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 28px !important;
        height: 28px !important;
        padding: 0 0.45rem !important;
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        border-radius: 5px !important;
        border: 1px solid #e2e8f0 !important;
        color: #475569 !important;
        text-decoration: none !important;
        background: #ffffff !important;
        transition: all 0.15s ease !important;
        line-height: 1 !important;
        box-shadow: none !important;
    }
    #datatable_paginate .page-item.previous .page-link,
    #datatable_paginate .page-item.next .page-link,
    .dataTables_paginate .page-item.previous .page-link,
    .dataTables_paginate .page-item.next .page-link {
        padding: 0 0.65rem !important;
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        min-width: unset !important;
    }
    #datatable_paginate .page-link:hover:not(.active),
    .dataTables_paginate .page-link:hover:not(.active) {
        background: #f1f5f9 !important;
        color: #0284c7 !important;
        border-color: #cbd5e1 !important;
    }
    #datatable_paginate .page-item.active .page-link,
    .dataTables_paginate .page-item.active .page-link {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        border-color: #0284c7 !important;
        box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25) !important;
    }
    #datatable_paginate .page-item.disabled .page-link,
    .dataTables_paginate .page-item.disabled .page-link {
        opacity: 0.45 !important;
        background: #f8fafc !important;
        color: #94a3b8 !important;
        border-color: #e2e8f0 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }

    /* Modal Barang Compact & Modern (Tambah, Edit, Isi Stok) */
    #modalBarang .modal-dialog {
        max-width: 620px !important;
        margin: 1.5rem auto !important;
    }
    #modalBarang .modal-content {
        border-radius: 14px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 10px 10px -5px rgba(15, 23, 42, 0.04) !important;
        overflow: hidden;
    }
    #modalBarang .modal-header {
        padding: 0.75rem 1.15rem !important;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    #modalBarang .modal-title {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    #modalBarang .modal-body {
        padding: 0.9rem 1.15rem !important;
        max-height: calc(85vh - 105px) !important;
        overflow-y: auto !important;
    }
    #modalBarang .modal-footer {
        padding: 0.6rem 1.15rem !important;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
    #modalBarang .form-label {
        font-size: 0.76rem !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 0.2rem !important;
    }
    #modalBarang .form-control,
    #modalBarang .form-select {
        font-size: 0.8125rem !important;
        padding: 0.32rem 0.65rem !important;
        border-radius: 6px !important;
        min-height: 33px !important;
    }
    #modalBarang .input-group-text {
        font-size: 0.78rem !important;
        padding: 0.32rem 0.6rem !important;
        background-color: #f1f5f9;
        color: #475569;
    }
    #modalBarang .ateka-scroll-select-btn {
        min-height: 33px !important;
        padding: 0.32rem 0.65rem !important;
        font-size: 0.8125rem !important;
        border-radius: 6px !important;
    }
    .product-img-thumbnail-box {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .product-img-thumbnail-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Card Filter Periode Transaksi / Pengadaan --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-3">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                </svg>
                            </span>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">Filter Data Barang ATK</h6>
                            </div>
                        </div>
                        @if ($month || $year || $startDate || $endDate || !empty($categoryId) || !empty($stockStatus))
                            <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-outline-danger btn-sm px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-3 p-md-3.5">
                        <form method="GET" action="{{ route('master-data.daftar-barang.index') }}" id="filterForm">
                            <div class="row g-2.5 mb-2.5">
                                {{-- 1. Kategori ATK --}}
                                <div class="col-12 col-md-7 col-lg-7">
                                    <label class="form-label text-dark small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                        Kategori Barang ATK
                                    </label>
                                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">Semua Kategori ({{ $categories->count() }})</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- 2. Status Stok --}}
                                <div class="col-12 col-md-5 col-lg-5">
                                    <label class="form-label text-dark small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                        Status Ketersediaan Stok
                                    </label>
                                    <select name="stock_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">Semua Status Stok</option>
                                        <option value="available" {{ ($stockStatus ?? '') === 'available' ? 'selected' : '' }}>Tersedia (Stok &gt; 0)</option>
                                        <option value="empty" {{ ($stockStatus ?? '') === 'empty' ? 'selected' : '' }}>Habis / Belum Tersedia (0)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-2.5">
                                {{-- 3. Periode Bulan & Tahun --}}
                                <div class="col-12 col-md-5 col-lg-5">
                                    <label class="form-label text-dark small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                        Periode Bulan &amp; Tahun
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <select name="month" class="form-select">
                                            <option value="">Semua Bulan (Tahunan)</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                        <select name="year" class="form-select" style="max-width: 115px;">
                                            <option value="">Semua Tahun</option>
                                            @foreach ($availableYears as $y)
                                                <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- 4. Rentang Tanggal Faktur --}}
                                <div class="col-12 col-md-7 col-lg-7">
                                    <label class="form-label text-dark small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.03em;">
                                        Rentang Tanggal Faktur
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="input-group input-group-sm w-100">
                                                <span class="input-group-text px-2 bg-light text-muted" style="font-size: 0.72rem;">Dari</span>
                                                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group input-group-sm w-100">
                                                <span class="input-group-text px-2 bg-light text-muted" style="font-size: 0.72rem;">Sampai</span>
                                                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Baris Bawah: Tombol Aksi --}}
                            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 pt-2.5 mt-2.5 border-top">
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    @if ($month || $year || $startDate || $endDate || !empty($categoryId) || !empty($stockStatus))
                                        <a href="{{ route('master-data.daftar-barang.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                            Reset Filter
                                        </a>
                                    @endif
                                    <button type="submit" class="btn btn-primary btn-sm px-3.5 d-inline-flex align-items-center gap-1.5 fw-semibold shadow-xs" style="font-size: 0.82rem;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                                        </svg>
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($unfulfilledProcurements) && $unfulfilledProcurements->isNotEmpty())
            <div class="row mb-4" id="pengadaan-section">
                <div class="col-sm-12">
                    <div class="card border-0 shadow-sm" style="border: 1px solid rgba(245, 158, 11, 0.25) !important; border-top: 3.5px solid #f59e0b !important;">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <span class="badge bg-warning-subtle text-warning p-2 rounded-3 d-flex align-items-center justify-content-center">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Permintaan Pengadaan Menunggu Penambahan Barang</h6>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold shadow-xs" style="font-size: 0.75rem;">
                                {{ method_exists($unfulfilledProcurements, 'total') ? $unfulfilledProcurements->total() : $unfulfilledProcurements->count() }} Item Menunggu
                            </span>
                        </div>
                        <div class="card-body p-3.5 bg-light-subtle">
                            <div class="row g-3">
                                @foreach($unfulfilledProcurements as $upDetail)
                                    @php
                                        $upReq = $upDetail->requestItem;
                                        $upProd = $upDetail->product;
                                        $upQty = $upDetail->approved_quantity ?: $upDetail->quantity;
                                        $upUnit = $upDetail->custom_unit ?: ($upProd->unit ?? 'Buah');
                                        $upName = $upProd->name ?? ($upDetail->custom_product_name ?: 'Barang Pengadaan');

                                        // Auto-resolve matched product jika belum terhubung
                                        if (!$upProd && !empty($upName)) {
                                            $upProd = \App\Models\Product::where('name', $upName)->first()
                                                ?: \App\Models\Product::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($upName))])->first();
                                        }

                                        $upCode = $upProd->code ?? '';
                                        $upCatId = $upProd->category_id ?? null;

                                        // Jika kategori belum ketemu, cocokkan nama kategori dengan kata di dalam nama barang
                                        if (!$upCatId && !empty($upName)) {
                                            foreach ($categories as $cat) {
                                                if (mb_stripos($upName, $cat->name) !== false) {
                                                    $upCatId = $cat->id;
                                                    if (!$upCode && $cat->kd_rincian) {
                                                        $upCode = $cat->kd_rincian;
                                                    }
                                                    break;
                                                }
                                            }
                                        }

                                        if (!$upCatId) {
                                            $upCatId = $categories->first()->id ?? '';
                                        }

                                        if (!$upCode && $upCatId) {
                                            $matchedCat = $categories->firstWhere('id', $upCatId);
                                            $upCode = $matchedCat->kd_rincian ?? '10.01.1';
                                        }
                                    @endphp
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="card border border-light-subtle shadow-xs h-100 bg-white rounded-3 p-3 transition-all overflow-hidden">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-2">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace text-truncate px-2 py-0.5" style="font-size: 0.68rem; max-width: 58%;" title="{{ $upReq->request_code ?? '-' }}">
                                                    {{ $upReq->request_code ?? '-' }}
                                                </span>
                                                <span class="badge {{ ($upReq->status ?? '') === 'approved' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2 py-0.5 fw-semibold flex-shrink-0" style="font-size: 0.68rem;">
                                                    {{ ($upReq->status ?? '') === 'approved' ? 'Disetujui TU' : 'Menunggu Review' }}
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $upName }}" style="font-size: 0.88rem;">
                                                {{ $upName }}
                                            </h6>
                                            <div class="text-secondary small mb-3" style="font-size: 0.76rem;">
                                                Pemohon: <span class="text-dark fw-semibold">{{ $upReq->user->name ?? 'Pegawai' }}</span> &bull; 
                                                Kebutuhan: <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">{{ $upQty }} {{ $upUnit }}</span>
                                            </div>
                                            <div class="mt-auto pt-2 border-top d-flex justify-content-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-success text-white fw-bold d-inline-flex align-items-center gap-1.5 btn-fulfill-procurement shadow-sm px-3 py-1.5"
                                                    style="font-size: 0.76rem;"
                                                    data-detail-id="{{ $upDetail->id }}"
                                                    data-product-id="{{ $upProd->id ?? '' }}"
                                                    data-category-id="{{ $upCatId }}"
                                                    data-code="{{ $upCode }}"
                                                    data-name="{{ $upName }}"
                                                    data-unit="{{ $upUnit }}"
                                                    data-qty="{{ $upQty }}"
                                                    data-price="{{ $upProd->purchase_price ?? 0 }}"
                                                    data-image="{{ $upProd ? asset($upProd->image_url) : '' }}">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                                    </svg>
                                                    <span>Isi Stok</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination Pengadaan (3 item per halaman) --}}
                            <div class="pt-3 mt-3 border-top">
                                @if ($unfulfilledProcurements->hasPages())
                                    <div class="pengadaan-pagination w-100">
                                        {{ $unfulfilledProcurements->links() }}
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted small" style="font-size: 0.78rem;">
                                            Showing <span class="fw-semibold">1</span> to <span class="fw-semibold">{{ $unfulfilledProcurements->count() }}</span> of <span class="fw-semibold">{{ method_exists($unfulfilledProcurements, 'total') ? $unfulfilledProcurements->total() : $unfulfilledProcurements->count() }}</span> results
                                        </div>
                                        <div>
                                            <span class="badge bg-white text-secondary border px-2.5 py-1.5" style="font-size: 0.74rem;">Halaman 1 dari 1</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Master Data Barang</h4>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if ($startDate && $endDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($startDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter Tanggal: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($endDate)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter s/d: <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                                </span>
                            @elseif ($month || $year)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    Filter: <strong>{{ $month ? \Carbon\Carbon::createFromDate(2026, (int)$month, 1)->translatedFormat('F') : '' }} {{ $year ?? '' }}</strong>
                                </span>
                            @endif
                            <button type="button" class="btn btn-primary btn-sm btn-create-barang">
                                + Tambah / Isi Stok Barang
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-2 px-md-3 py-3">
                        <table id="datatable" class="table table-striped table-hover align-middle word-wrap w-100 ateka-fit-table"
                            data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th width="3%" class="text-center no-sort" data-orderable="false">No</th>
                                        <th width="7%" class="text-nowrap">No. Faktur</th>
                                        <th width="7%" class="text-nowrap">Tgl Faktur</th>
                                        <th width="7%" class="text-nowrap">Kode Rincian</th>
                                        <th width="17%">Nama Barang</th>
                                        <th width="7%">Kategori</th>
                                        <th width="8%">Uraian / Supplier</th>
                                        <th width="4%" class="text-center">Satuan</th>
                                        <th width="4%" class="text-center">Stok</th>
                                        <th width="8%" class="text-nowrap text-end">Harga Perolehan</th>
                                        <th width="8%" class="text-nowrap text-end">Total Nilai Saldo</th>
                                        <th width="8%" class="text-center text-nowrap">Status</th>
                                        <th width="12%" class="text-center text-nowrap no-sort" data-orderable="false">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        @php
                                            $hargaBeli = $product->purchase_price ?? 0;
                                            $totalNilai = $product->stock * $hargaBeli;
                                            $noFaktur = $product->initial_no_faktur;
                                            $tglFaktur = $product->initial_transaction_date_formatted;
                                            $uraian = $product->initial_uraian;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-nowrap">
                                                <span class="badge bg-secondary">{{ $noFaktur ?: '-' }}</span>
                                            </td>
                                            <td class="text-nowrap">
                                                {{ $tglFaktur }}
                                            </td>
                                            <td class="text-nowrap">
                                                <span class="badge bg-primary">{{ $product->code }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" style="width: 28px; height: 28px; object-fit: contain; background: #f8fafc; border-radius: 6px; padding: 2px; border: 1px solid #e2e8f0; flex-shrink: 0;" loading="lazy">
                                                    <div style="min-width: 0;">
                                                        <strong class="d-block" style="font-size: 0.78rem; line-height: 1.25;">{{ $product->name }}</strong>
                                                        @if ($product->khusus_subdit)
                                                            <span class="badge bg-warning text-dark" style="font-size: 0.62rem;">Khusus Perwakilan Subdit</span>
                                                        @endif
                                                        @if ($product->description)
                                                            <small class="text-muted d-block text-truncate" style="font-size: 0.68rem; max-width: 170px;">{{ $product->description }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->category->name ?? '-' }}</td>
                                            <td>{{ $uraian ?: '-' }}</td>
                                            <td class="text-center">{{ $product->unit }}</td>
                                            <td class="text-center fw-semibold">{{ number_format($product->stock) }}</td>
                                            <td class="text-nowrap text-end" style="font-size: 0.74rem;">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                            <td class="text-nowrap text-end" style="font-size: 0.74rem;"><strong class="text-success">Rp
                                                    {{ number_format($totalNilai, 0, ',', '.') }}</strong></td>
                                            <td class="text-nowrap text-center">
                                                @if ($product->stock <= 0)
                                                    <span class="badge bg-dark" style="font-size: 0.65rem; padding: 0.22rem 0.45rem;">Stok Belum Tersedia</span>
                                                @else
                                                    <span class="badge bg-success" style="font-size: 0.65rem; padding: 0.22rem 0.45rem;">Tersedia</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                                    <button type="button" class="btn btn-info btn-sm btn-detail-barang px-1.5 py-0.5"
                                                        style="font-size: 0.68rem; line-height: 1.2;"
                                                        data-id="{{ $product->id }}"
                                                        data-category-id="{{ $product->category_id }}"
                                                        data-category-name="{{ e($product->category->name ?? '-') }}"
                                                        data-code="{{ e($product->code) }}"
                                                        data-name="{{ e($product->name) }}"
                                                        data-unit="{{ e($product->unit) }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-minimum-stock="{{ $product->minimum_stock }}"
                                                        data-purchase-price="{{ $product->purchase_price }}"
                                                        data-no-faktur="{{ e($noFaktur ?? '-') }}"
                                                        data-transaction-date="{{ $product->initial_transaction_date ?? '' }}"
                                                        data-transaction-date-formatted="{{ $tglFaktur }}"
                                                        data-uraian="{{ e($uraian ?? '-') }}"
                                                        data-description="{{ e($product->description) }}"
                                                        data-transaction-count="{{ $product->stock_transactions_count }}"
                                                        data-created-at="{{ $product->created_at ? $product->created_at->format('d-m-Y H:i') : '-' }}"
                                                        data-updated-at="{{ $product->updated_at ? $product->updated_at->format('d-m-Y H:i') : '-' }}"
                                                        data-image="{{ asset($product->image_url) }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-barang px-1.5 py-0.5"
                                                        style="font-size: 0.68rem; line-height: 1.2;"
                                                        data-id="{{ $product->id }}"
                                                        data-category-id="{{ $product->category_id }}"
                                                        data-code="{{ e($product->code) }}"
                                                        data-name="{{ e($product->name) }}"
                                                        data-unit="{{ e($product->unit) }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-minimum-stock="{{ $product->minimum_stock }}"
                                                        data-purchase-price="{{ $product->purchase_price }}"
                                                        data-no-faktur="{{ e($noFaktur ?? '') }}"
                                                        data-transaction-date="{{ $product->initial_transaction_date ?? '' }}"
                                                        data-uraian="{{ e($uraian ?? '') }}"
                                                        data-description="{{ e($product->description) }}"
                                                        data-khusus-subdit="{{ $product->khusus_subdit ? 1 : 0 }}"
                                                        data-image="{{ asset($product->image_url) }}">
                                                        Edit
                                                    </button>
                                                    @if (!auth()->user()->isPegawaiGudang())
                                                        <form
                                                            action="{{ route('master-data.daftar-barang.destroy', $product->id) }}"
                                                            method="POST"
                                                            class="d-inline form-delete-product">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm px-1.5 py-0.5 btn-delete-product"
                                                                style="font-size: 0.68rem; line-height: 1.2;"
                                                                data-id="{{ $product->id }}"
                                                                data-name="{{ e($product->name) }}"
                                                                data-code="{{ e($product->code ?? '') }}"
                                                                data-stock="{{ $product->stock }}"
                                                                data-unit="{{ $product->unit ?? 'unit' }}">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create / Update / Detail --}}
    <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formBarang" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="" disabled>
                    <input type="hidden" name="request_detail_id" id="request_detail_id" value="">

                    <div class="modal-header">
                        <h6 class="modal-title" id="modalBarangLabel">Form Barang</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
                    </div>

                    <div class="modal-body">
                        <div id="detailBarangInfo" class="alert alert-info d-none mb-2.5 py-2 px-2.5">
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Status Stok</small>
                                    <strong id="detailStatusStok" style="font-size: 0.78rem;">-</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Transaksi</small>
                                    <strong id="detailTransactionCount" style="font-size: 0.78rem;">0</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Dibuat</small>
                                    <strong id="detailCreatedAt" style="font-size: 0.78rem;">-</strong>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">Diupdate</small>
                                    <strong id="detailUpdatedAt" style="font-size: 0.78rem;">-</strong>
                                </div>
                            </div>
                        </div>

                        <div id="quickCatalogRestockBox" class="alert alert-light border border-primary-subtle mb-2.5 p-2 rounded-2">
                            <label class="form-label fw-bold text-primary mb-1 d-block" style="font-size: 0.74rem;">
                                Pilih Cepat dari Katalog Standar LIST ATK (Opsional — Untuk Isi Stok / Input Faktur):
                            </label>
                            <select id="quick_existing_product" class="form-select form-select-sm bg-white" style="font-size: 0.78rem;">
                                <option value="">-- Buat Barang Baru Manual atau Pilih Standar di Sini --</option>
                                @foreach ($products as $pItem)
                                    <option value="{{ $pItem->id }}"
                                        data-category-id="{{ $pItem->category_id }}"
                                        data-code="{{ $pItem->code }}"
                                        data-name="{{ $pItem->name }}"
                                        data-unit="{{ $pItem->unit }}"
                                        data-stock="{{ $pItem->stock }}"
                                        data-price="{{ $pItem->purchase_price ?? 0 }}"
                                        data-faktur="{{ $pItem->initial_no_faktur }}"
                                        data-uraian="{{ $pItem->initial_uraian }}"
                                        data-desc="{{ $pItem->description }}"
                                        data-image="{{ asset($pItem->image_url) }}">
                                        [{{ $pItem->category->name ?? 'ATK' }}] {{ $pItem->code }} - {{ $pItem->name }} (Stok: {{ $pItem->stock }} {{ $pItem->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select form-select-sm" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" data-kode-barang="{{ $category->kode_barang }}" data-rincian="{{ $category->kd_rincian }}">{{ $category->name }} ({{ $category->kode_barang ?: $category->kd_rincian }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Rincian <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control form-control-sm font-monospace"
                                    placeholder="10.01.1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm"
                                    placeholder="Nama Barang ATK" required>
                            </div>

                            <div class="col-12">
                                <div class="form-check p-2 ps-5 rounded border border-warning-subtle bg-warning-subtle">
                                    <input class="form-check-input" type="checkbox" name="khusus_subdit" id="khusus_subdit" value="1">
                                    <label class="form-check-label fw-semibold" for="khusus_subdit">Khusus Perwakilan Subdit <small class="text-muted fw-normal">(hanya bisa diminta oleh PIC Subdit)</small></label>
                                </div>
                            </div>

                            <!-- No. Faktur, Tgl Faktur & Uraian -->
                            <div class="col-md-4">
                                <label class="form-label">No. Faktur / Bukti Awal</label>
                                <input type="text" name="no_faktur" id="no_faktur" class="form-control form-control-sm"
                                    placeholder="121/FK">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tgl Faktur / Pengadaan</label>
                                <input type="date" name="transaction_date" id="transaction_date" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Uraian / Nama Supplier</label>
                                <input type="text" name="uraian" id="uraian" class="form-control form-control-sm"
                                    placeholder="CV. Milia Pertama">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select name="unit" id="unit" class="form-select form-select-sm" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="Buah">Buah</option>
                                    <option value="Dus">Dus</option>
                                    <option value="Dos">Dos</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Pak">Pak</option>
                                    <option value="Kotak">Kotak</option>
                                    <option value="Doz">Doz</option>
                                    <option value="Set">Set</option>
                                    <option value="Roll">Roll</option>
                                    <option value="Slop">Slop</option>
                                    <option value="Pak/Slop">Pak/Slop</option>
                                    <option value="Buku">Buku</option>
                                    <option value="Tube">Tube</option>
                                    <option value="Box">Box</option>
                                    <option value="Botol">Botol</option>
                                    <option value="Lusin">Lusin</option>
                                    <option value="Lembar">Lembar</option>
                                    <option value="Batang">Batang</option>
                                    <option value="Bungkus">Bungkus</option>
                                    <option value="Pcs">Pcs</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control form-control-sm" min="0"
                                    value="0" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Harga Beli / Satuan <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="purchase_price" id="purchase_price" class="form-control form-control-sm"
                                        min="0" value="0" required>
                                </div>
                            </div>

                            <!-- Upload Foto / Gambar Barang (Katalog Persediaan) -->
                            <div class="col-12" id="boxProductImage">
                                <label class="form-label">Foto / Gambar Barang (Katalog Persediaan)</label>
                                <div class="d-flex align-items-center gap-2.5 p-2 rounded-2 border bg-light-subtle">
                                    <div class="product-img-thumbnail-box">
                                        <img id="imagePreview" src="" alt="Preview" class="d-none">
                                        <div id="imagePlaceholder" class="text-secondary small d-flex flex-column align-items-center justify-content-center text-center p-1">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-muted">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                            <span style="font-size: 0.58rem; line-height: 1;" class="text-muted mt-0.5">No Foto</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1" style="min-width: 0;">
                                        <input type="file" name="image" id="product_image" class="form-control form-control-sm"
                                            accept="image/jpeg,image/png,image/jpg,image/webp">
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-muted text-truncate" style="font-size: 0.7rem;">Format: JPG, PNG, WEBP (Maks. 2MB). Foto akan tampil di Katalog Persediaan.</small>
                                            <button type="button" id="btnResetImage" class="btn btn-link btn-sm text-danger text-decoration-none p-0 d-none" style="font-size: 0.7rem;">Batal Ubah</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" class="form-control form-control-sm" rows="2"
                                    placeholder="Tulis deskripsi singkat barang"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSubmitBarang">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // SweetAlert2 Modal Popup Notification matching Image 1
                @if (session('success'))
                    if (typeof Swal !== 'undefined') {
                        const successMsg = {!! json_encode(session('success')) !!};
                        let titleText = 'Barang Berhasil Ditambahkan!';
                        const lowerMsg = successMsg.toLowerCase();
                        if (lowerMsg.includes('diperbarui') || lowerMsg.includes('diperbaharui')) {
                            titleText = 'Barang Berhasil Diperbarui!';
                        } else if (lowerMsg.includes('dihapus')) {
                            titleText = 'Barang Berhasil Dihapus!';
                        } else if (lowerMsg.includes('ditambahkan')) {
                            titleText = 'Barang Berhasil Ditambahkan!';
                        } else {
                            titleText = 'Barang Berhasil Disimpan!';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: titleText,
                            html: `<div class="text-secondary mt-2" style="font-size: 0.95rem; line-height: 1.5;">${successMsg}</div>`,
                            confirmButtonText: 'Selesai & Tutup',
                            confirmButtonColor: '#059669',
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0 p-4',
                                title: 'fw-bold text-dark fs-4 mb-1',
                                confirmButton: 'btn btn-success px-4 py-2 fw-semibold rounded-3 shadow-sm'
                            },
                            timer: 7000,
                            timerProgressBar: true
                        });
                    }
                @endif

                function escapeHtml(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                // Modern Compact Delete Confirmation matching user design (Shadcn / Tailwind Style)
                $(document).on('click', '.btn-delete-product', function(e) {
                    e.preventDefault();
                    const $btn = $(this);
                    const $form = $btn.closest('form');
                    const prodName = $btn.data('name') || 'barang ini';
                    const prodCode = $btn.data('code') || '';
                    const prodStock = $btn.data('stock') || 0;
                    const prodUnit = $btn.data('unit') || 'unit';

                    const modalHtml = `
                        <div class="text-center pt-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 48px; height: 48px; background-color: #fef2f2;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                            </div>
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; letter-spacing: -0.01em;">Are you sure?</h5>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5; max-width: 270px; margin: 0 auto;">
                                Tindakan ini tidak dapat dibatalkan. Barang <strong>${escapeHtml(prodName)}</strong> ${prodCode ? `[${escapeHtml(prodCode)}]` : ''} akan dihapus permanen.
                            </p>
                            <div class="small text-muted mt-2" style="font-size: 0.76rem;">
                                Sisa stok saat ini: <strong>${prodStock} ${escapeHtml(prodUnit)}</strong>
                            </div>
                        </div>
                    `;

                    Swal.fire({
                        html: modalHtml,
                        width: '350px',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus barang',
                        cancelButtonText: 'Batal',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'custom-delete-popup',
                            actions: 'custom-delete-actions',
                            confirmButton: 'custom-delete-confirm-btn',
                            cancelButton: 'custom-delete-cancel-btn'
                        },
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                html: `
                                    <div class="text-center py-2">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Menghapus Barang...</h6>
                                        <p class="text-muted small mb-0">Mohon tunggu sebentar...</p>
                                    </div>
                                `,
                                width: '300px',
                                padding: '1.25rem',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'custom-delete-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                    $form.submit();
                                }
                            });
                        }
                    });
                });

                const storeUrl = "{{ route('master-data.daftar-barang.store') }}";
                const updateUrlTemplate = "{{ route('master-data.daftar-barang.update', ':id') }}";

                const $modal = $('#modalBarang');
                const $form = $('#formBarang');
                const $method = $('#formMethod');
                const $title = $('#modalBarangLabel');
                const $btnSubmit = $('#btnSubmitBarang');
                const $categoryId = $('#category_id');
                const $code = $('#code');
                const $name = $('#name');
                const $noFaktur = $('#no_faktur');
                const $transactionDate = $('#transaction_date');
                const $uraian = $('#uraian');
                const $unit = $('#unit');
                const $stock = $('#stock');
                const $purchasePrice = $('#purchase_price');
                const $description = $('#description');
                const $detailInfo = $('#detailBarangInfo');
                const $detailStatusStok = $('#detailStatusStok');
                const $detailTransactionCount = $('#detailTransactionCount');
                const $detailCreatedAt = $('#detailCreatedAt');
                const $detailUpdatedAt = $('#detailUpdatedAt');
                const $quickBox = $('#quickCatalogRestockBox');
                const $quickSelect = $('#quick_existing_product');
                const $requestDetailId = $('#request_detail_id');
                const $productImage = $('#product_image');
                const $imagePreview = $('#imagePreview');
                const $imagePlaceholder = $('#imagePlaceholder');
                const $btnResetImage = $('#btnResetImage');
                let currentProductImageUrl = '';

                function setProductImagePreview(url) {
                    currentProductImageUrl = url || '';
                    $productImage.val('');
                    $btnResetImage.addClass('d-none');
                    if (url) {
                        $imagePreview.attr('src', url).removeClass('d-none');
                        $imagePlaceholder.addClass('d-none');
                    } else {
                        $imagePreview.attr('src', '').addClass('d-none');
                        $imagePlaceholder.removeClass('d-none');
                    }
                }

                $productImage.on('change', function() {
                    const file = this.files && this.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Ukuran Gambar Terlalu Besar',
                                    text: 'Ukuran file gambar maksimal adalah 2MB.',
                                    confirmButtonColor: '#0284c7'
                                });
                            } else {
                                alert('Ukuran file gambar maksimal adalah 2MB.');
                            }
                            $(this).val('');
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $imagePreview.attr('src', e.target.result).removeClass('d-none');
                            $imagePlaceholder.addClass('d-none');
                            if (currentProductImageUrl) {
                                $btnResetImage.removeClass('d-none');
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });

                $btnResetImage.on('click', function(e) {
                    e.preventDefault();
                    $productImage.val('');
                    $(this).addClass('d-none');
                    if (currentProductImageUrl) {
                        $imagePreview.attr('src', currentProductImageUrl).removeClass('d-none');
                        $imagePlaceholder.addClass('d-none');
                    } else {
                        $imagePreview.attr('src', '').addClass('d-none');
                        $imagePlaceholder.removeClass('d-none');
                    }
                });

                $categoryId.on('change', function() {
                    const rincian = $(this).find('option:selected').data('rincian');
                    if (rincian && (!$code.val() || $code.val().startsWith('ATK-') || $method.val() !== 'PUT')) {
                        $code.val(rincian);
                    }
                });

                function setCategoryValue(val) {
                    val = val ? String(val) : '';
                    $categoryId.val(val);
                    if ($categoryId.length && $categoryId[0]) {
                        $categoryId[0].dispatchEvent(new Event('change', { bubbles: true }));
                        $categoryId[0].dispatchEvent(new Event('input', { bubbles: true }));
                        if (typeof $categoryId[0]._syncAtekaSelect === 'function') {
                            $categoryId[0]._syncAtekaSelect();
                        }
                    }
                    $categoryId.trigger('change');
                }

                function resetModalBarang() {
                    $form[0].reset();
                    $form.attr('action', storeUrl);
                    $method.prop('disabled', true).val('');
                    $requestDetailId.val('');
                    setCategoryValue('');
                    setProductImagePreview('');
                    $productImage.prop('disabled', false);
                    $categoryId.prop('disabled', false);
                    $code.prop('readonly', false).val('');
                    $name.prop('readonly', false).val('');
                    $noFaktur.prop('readonly', false).val('');
                    $transactionDate.prop('readonly', false).val('');
                    $uraian.prop('readonly', false).val('');
                    $unit.prop('readonly', false).val('Buah');
                    $stock.prop('readonly', false).val(0);
                    $purchasePrice.prop('readonly', false).val(0);
                    $description.prop('readonly', false).val('');
                    $('#khusus_subdit').prop('checked', false);
                    $detailInfo.addClass('d-none');
                    $quickBox.addClass('d-none');
                    $quickSelect.val('');
                    $('#boxProductImage').removeClass('d-none');
                    $detailStatusStok.text('-');
                    $detailTransactionCount.text('0 Transaksi');
                    $detailCreatedAt.text('-');
                    $detailUpdatedAt.text('-');
                    $title.text('Form Barang');
                    $btnSubmit.removeClass('d-none').text('Simpan Barang');
                }

                // Event listener untuk tombol + Isi Stok dari Banner Usulan Pengadaan
                $(document).on('click', '.btn-fulfill-procurement', function() {
                    resetModalBarang();
                    const $btn = $(this).closest('.btn-fulfill-procurement');
                    const detailId = $btn.attr('data-detail-id') || $btn.data('detail-id') || '';
                    const productId = $btn.attr('data-product-id') || $btn.data('product-id') || '';
                    let catId = String($btn.attr('data-category-id') || $btn.data('category-id') || '');
                    let code = $btn.attr('data-code') || $btn.data('code') || '';
                    const name = $btn.attr('data-name') || $btn.data('name') || '';
                    const unit = $btn.attr('data-unit') || $btn.data('unit') || 'Buah';
                    const qty = parseInt($btn.attr('data-qty') || $btn.data('qty')) || 1;
                    const price = $btn.attr('data-price') || $btn.data('price') || 0;
                    const img = $btn.attr('data-image') || '';

                    $requestDetailId.val(detailId);
                    if (productId) {
                        const updateUrl = updateUrlTemplate.replace(':id', productId);
                        $form.attr('action', updateUrl);
                        $method.prop('disabled', false).val('PUT');
                    } else {
                        $form.attr('action', storeUrl);
                        $method.prop('disabled', true).val('');
                    }

                    // Auto-detect kategori dari nama jika belum ada
                    if (!catId && name) {
                        const lowerName = name.toLowerCase();
                        $categoryId.find('option').each(function() {
                            const val = $(this).val();
                            const text = $(this).text().split('(')[0].trim().toLowerCase();
                            if (val && text && lowerName.includes(text)) {
                                catId = val;
                                return false;
                            }
                        });
                    }

                    if (catId) {
                        setCategoryValue(catId);
                        if (!code) {
                            code = $categoryId.find('option:selected').data('rincian') || '';
                        }
                    }

                    if (code) {
                        $code.val(code);
                    }

                    $name.val(name);
                    if ($unit.find('option[value="' + unit + '"]').length === 0 && unit) {
                        $unit.append(new Option(unit, unit, true, true));
                    }
                    $unit.val(unit);
                    $stock.val(qty);
                    $purchasePrice.val(price);
                    $transactionDate.val(new Date().toISOString().split('T')[0]);
                    setProductImagePreview('');
                    $('#boxProductImage').addClass('d-none'); // Form isi stok tidak perlu upload gambar
                    $title.text('Tambahkan / Isi Stok Barang Pengadaan: ' + name);
                    $btnSubmit.text('Simpan Stok & Kirim Notifikasi');
                    $modal.modal('show');
                    setTimeout(() => $stock.focus(), 300);
                });

                // Auto-detect kategori saat mengetik nama barang secara manual
                $name.on('input blur', function() {
                    if (!$categoryId.val()) {
                        const valName = $(this).val().toLowerCase();
                        if (valName.length >= 3) {
                            $categoryId.find('option').each(function() {
                                const optVal = $(this).val();
                                const optText = $(this).text().split('(')[0].trim().toLowerCase();
                                if (optVal && optText && valName.includes(optText)) {
                                    setCategoryValue(optVal);
                                    return false;
                                }
                            });
                        }
                    }
                });

                // Event listener untuk tombol Tambah Barang
                $(document).on('click', '.btn-create-barang', function() {
                    resetModalBarang();
                    $quickBox.removeClass('d-none');
                    $title.text('Tambah Barang / Isi Stok dari Standar ATK');
                    $btnSubmit.text('Simpan Barang');
                    $transactionDate.val(new Date().toISOString().split('T')[0]);
                    $modal.modal('show');
                });

                // Pilih cepat dari standar LIST ATK di dalam modal Tambah Barang
                $quickSelect.on('change', function() {
                    const selectedId = $(this).val();
                    const $opt = $(this).find('option:selected');
                    if (selectedId && $opt.length) {
                        const updateUrl = updateUrlTemplate.replace(':id', selectedId);
                        $form.attr('action', updateUrl);
                        $method.prop('disabled', false).val('PUT');
                        const catId = String($opt.attr('data-category-id') || $opt.data('category-id') || '');
                        if (catId) {
                            setCategoryValue(catId);
                        }
                        const code = $opt.attr('data-code') || $opt.data('code') || '';
                        if (code) {
                            $code.val(code);
                        }
                        $name.val($opt.attr('data-name') || $opt.data('name') || '');
                        const unit = $opt.attr('data-unit') || $opt.data('unit') || 'Buah';
                        if ($unit.find('option[value="' + unit + '"]').length === 0 && unit) {
                            $unit.append(new Option(unit, unit, true, true));
                        }
                        $unit.val(unit);
                        $stock.val(1);
                        $purchasePrice.val($opt.attr('data-price') || $opt.data('price') || 0);
                        $noFaktur.val($opt.attr('data-faktur') || $opt.data('faktur') || '');
                        $uraian.val($opt.attr('data-uraian') || $opt.data('uraian') || '');
                        $description.val($opt.attr('data-desc') || $opt.data('desc') || '');
                        const img = $opt.attr('data-image') || '';
                        setProductImagePreview('');
                        $('#boxProductImage').addClass('d-none');
                        $title.text('Isi Stok / Update Barang Standar ATK');
                        $btnSubmit.text('Simpan Stok & Perubahan');
                        $stock.focus();
                    } else {
                        $form.attr('action', storeUrl);
                        $method.prop('disabled', true).val('');
                        setCategoryValue('');
                        $code.val('');
                        $name.val('');
                        $unit.val('Buah');
                        $stock.val(0);
                        $purchasePrice.val(0);
                        $description.val('');
                        setProductImagePreview('');
                        $('#boxProductImage').removeClass('d-none');
                        $title.text('Tambah Barang Baru');
                        $btnSubmit.text('Simpan Barang');
                    }
                });

                $(document).on('click', '.btn-edit-barang', function() {
                    resetModalBarang();
                    const $btn = $(this).closest('.btn-edit-barang');
                    const id = $btn.attr('data-id') || $btn.data('id');
                    const updateUrl = updateUrlTemplate.replace(':id', id);

                    $form.attr('action', updateUrl);
                    $method.prop('disabled', false).val('PUT');
                    const catId = String($btn.attr('data-category-id') || $btn.data('category-id') || '');
                    if (catId) {
                        setCategoryValue(catId);
                    }
                    const code = $btn.attr('data-code') || $btn.data('code') || '';
                    if (code) {
                        $code.val(code);
                    }
                    $name.val($btn.attr('data-name') || $btn.data('name') || '');
                    $noFaktur.val($btn.attr('data-no-faktur') || $btn.data('no-faktur') || '');
                    $transactionDate.val($btn.attr('data-transaction-date') || $btn.data('transaction-date') || new Date().toISOString().split('T')[0]);
                    $uraian.val($btn.attr('data-uraian') || $btn.data('uraian') || '');
                    $unit.val($btn.attr('data-unit') || $btn.data('unit') || 'Buah');
                    $stock.val($btn.attr('data-stock') || $btn.data('stock') || 0);
                    $purchasePrice.val($btn.attr('data-purchase-price') || $btn.data('purchase-price') || 0);
                    $description.val($btn.attr('data-description') || $btn.data('description') || '');
                    $('#khusus_subdit').prop('checked', String($btn.attr('data-khusus-subdit')) === '1');
                    const img = $btn.attr('data-image') || '';
                    setProductImagePreview(img);
                    $('#boxProductImage').removeClass('d-none'); // Di Edit Barang tetap bisa upload foto

                    $title.text('Edit / Isi Stok Barang');
                    $btnSubmit.text('Simpan Perubahan');
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-detail-barang', function() {
                    resetModalBarang();
                    const categoryId = $(this).data('category-id');
                    const code = $(this).data('code');
                    const name = $(this).data('name');
                    const noFaktur = $(this).data('no-faktur');
                    const transactionDate = $(this).data('transaction-date');
                    const uraian = $(this).data('uraian');
                    const unit = $(this).data('unit');
                    const stock = parseInt($(this).data('stock'));
                    const purchasePrice = $(this).data('purchase-price');
                    const description = $(this).data('description');
                    const transactionCount = $(this).data('transaction-count');
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');
                    const img = $(this).data('image') || $(this).attr('data-image') || '';

                    $categoryId.val(categoryId);
                    $code.val(code);
                    $name.val(name);
                    $noFaktur.val(noFaktur ? noFaktur : '-');
                    $transactionDate.val(transactionDate || '');
                    $uraian.val(uraian ? uraian : '-');
                    $unit.val(unit);
                    $stock.val(stock);
                    $purchasePrice.val(purchasePrice);
                    $description.val(description ? description : '-');
                    if (img) {
                        setProductImagePreview(img);
                        $('#boxProductImage').removeClass('d-none');
                    } else {
                        $('#boxProductImage').addClass('d-none');
                    }
                    $productImage.prop('disabled', true);

                    $categoryId.prop('disabled', true);
                    $code.prop('readonly', true);
                    $name.prop('readonly', true);
                    $noFaktur.prop('readonly', true);
                    $transactionDate.prop('readonly', true);
                    $uraian.prop('readonly', true);
                    $unit.prop('readonly', true);
                    $stock.prop('readonly', true);
                    $purchasePrice.prop('readonly', true);
                    $description.prop('readonly', true);

                    if (stock <= 0) {
                        $detailStatusStok.html('<span class="badge bg-dark">Stok Belum Tersedia</span>');
                    } else {
                        $detailStatusStok.html('<span class="badge bg-success">Tersedia</span>');
                    }

                    $detailTransactionCount.text(transactionCount + ' Transaksi');
                    $detailCreatedAt.text(createdAt);
                    $detailUpdatedAt.text(updatedAt);
                    $detailInfo.removeClass('d-none');
                    $title.text('Detail Barang');

                    $btnSubmit.addClass('d-none');
                    $modal.modal('show');
                });

                $modal.on('hidden.bs.modal', function() {
                    resetModalBarang();
                });
            });
        </script>
    @endpush
@endsection
