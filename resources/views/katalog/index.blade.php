@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Katalog Alat Tulis Kantor</h1>
                            <p class="text-white mb-0" style="opacity: 0.92; max-width: 820px;">
                                Cek ketersediaan stok fisik ATK di gudang secara real-time. Jika persediaan habis, Anda dapat memesan restock secara instan langsung dari kartu barang tanpa membuka formulir terpisah.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- Flash Notification Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Baris Filter & Pencarian --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3.5">
                        <form id="katalog-filter-form" action="{{ route('katalog-atk') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <span id="search-spinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                                        <svg id="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </span>
                                    <input type="text" id="katalog-search" name="search" class="form-control border-start-0 ps-0"
                                        placeholder="Cari nama barang atau kode ATK (misal: Map, Kertas, Binder)..." value="{{ $search }}" autocomplete="off" aria-label="Cari nama barang atau kode ATK">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="btn-clear-search" style="{{ empty($search) ? 'display: none;' : '' }}" title="Hapus pencarian" aria-label="Hapus pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-3">
                                <select name="category_id" class="form-select" onchange="this.form.submit()" aria-label="Filter berdasarkan kategori ATK">
                                    <option value="">-- Semua Kategori ({{ $categories->count() }}) --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <select name="stock_status" class="form-select" onchange="this.form.submit()" aria-label="Filter berdasarkan status ketersediaan stok ATK">
                                    <option value="">-- Semua Status Stok --</option>
                                    <option value="available" {{ ($stockStatus ?? '') === 'available' ? 'selected' : '' }}>Tersedia di Gudang</option>
                                    <option value="empty" {{ ($stockStatus ?? '') === 'empty' ? 'selected' : '' }}>Stok Belum Tersedia (Bisa Dipesan)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-2 d-flex gap-2" id="katalog-reset-container">
                                <button type="submit" id="btn-katalog-search" class="btn btn-primary flex-grow-1">Cari Barang</button>
                                @if ($search || $categoryId || !empty($stockStatus))
                                    <a href="{{ route('katalog-atk') }}" class="btn btn-light border px-3" title="Reset Filter">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Kartu Barang ATK --}}
        <div class="row g-3" id="katalog-grid-container">
            @forelse ($products as $item)
                @php
                    $isAvailable = $item->stock > 0;
                    $catNameLower = strtolower($item->category->name ?? '');
                    $itemNameLower = strtolower($item->name ?? '');

                    // Visual theme selector based on category / product name
                    if (str_contains($catNameLower, 'holder') || str_contains($itemNameLower, 'holder') || str_contains($catNameLower, 'ordner')) {
                        $visualClass = 'bg-cat-holder';
                        $iconType = 'holder';
                    } elseif (str_contains($catNameLower, 'map') || str_contains($itemNameLower, 'map') || str_contains($catNameLower, 'folder')) {
                        $visualClass = 'bg-cat-folder';
                        $iconType = 'folder';
                    } elseif (str_contains($catNameLower, 'kertas') || str_contains($itemNameLower, 'kertas') || str_contains($catNameLower, 'hvs') || str_contains($itemNameLower, 'buku')) {
                        $visualClass = 'bg-cat-paper';
                        $iconType = 'paper';
                    } elseif (str_contains($catNameLower, 'pena') || str_contains($itemNameLower, 'pena') || str_contains($itemNameLower, 'pulpen') || str_contains($catNameLower, 'alat tulis') || str_contains($itemNameLower, 'spidol')) {
                        $visualClass = 'bg-cat-pen';
                        $iconType = 'pen';
                    } else {
                        $visualClass = 'bg-cat-general';
                        $iconType = 'general';
                    }

                    // Stock percentage calculation for visual progress meter
                    if ($item->stock <= 0) {
                        $stockPercent = 0;
                        $meterClass = 'stock-empty';
                    } elseif ($item->stock <= 10) {
                        $stockPercent = 35;
                        $meterClass = 'stock-low';
                    } else {
                        $stockPercent = min(100, 40 + ($item->stock * 2));
                        $meterClass = 'stock-high';
                    }
                @endphp
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 katalog-card-item"
                    data-search-text="{{ strtolower($item->name . ' ' . $item->code . ' ' . ($item->category->name ?? '')) }}"
                    data-category-id="{{ $item->category_id }}">
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                {{-- Visual Container with Floating Category Badge --}}
                                <div class="katalog-card-visual {{ $visualClass }}">
                                    <span class="position-absolute top-0 end-0 m-2 px-2 py-0.5 rounded-pill font-monospace" style="background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(6px); font-size: 0.65rem; color: #334155; font-weight: 600;">
                                        {{ $item->category->name ?? 'ATK' }}
                                    </span>
                                    @if ($iconType === 'folder')
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    @elseif ($iconType === 'holder')
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="9" y1="3" x2="9" y2="21"></line>
                                            <line x1="3" y1="9" x2="9" y2="9"></line>
                                            <line x1="3" y1="15" x2="9" y2="15"></line>
                                        </svg>
                                    @elseif ($iconType === 'paper')
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                    @elseif ($iconType === 'pen')
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    @else
                                        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                        </svg>
                                    @endif
                                </div>

                                {{-- Baris Kode & Status Badge --}}
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                        {{ $item->code }}
                                    </span>
                                    @if ($item->stock <= 0)
                                        <span class="badge bg-danger">
                                            <span class="badge-dot badge-dot-danger"></span>Stok Belum Tersedia
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <span class="badge-dot badge-dot-success"></span>Tersedia
                                        </span>
                                    @endif
                                </div>

                                {{-- Judul Barang & Kategori --}}
                                <h6 class="fw-bold text-dark mb-1" style="min-height: 40px; line-height: 1.35;" title="{{ $item->name }}">
                                    {{ $item->name }}
                                </h6>
                                <small class="text-muted d-block mb-1" style="font-size: 0.78rem;">
                                    Kategori: <strong class="text-secondary">{{ $item->category->name ?? '-' }}</strong>
                                </small>
                                <small class="text-muted d-block mb-2" style="font-size: 0.74rem;">
                                    Satuan Standar: <span class="badge bg-light text-dark border">{{ implode(' / ', $item->unit_list) }}</span>
                                </small>
                            </div>

                            <div class="border-top pt-2.5 mt-2">
                                {{-- Stock Indicator with Meter --}}
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="small text-muted fw-semibold">Sisa Stok Fisik:</span>
                                    <span class="fw-bold {{ $item->stock <= 0 ? 'text-danger' : 'text-primary' }} fs-5">
                                        {{ number_format($item->stock) }} <small class="fs-6 fw-normal text-muted">{{ $item->unit ?? 'Unit' }}</small>
                                    </span>
                                </div>

                                <div class="stock-meter-bar mb-3">
                                    <div class="stock-meter-fill {{ $meterClass }}" style="width: {{ $stockPercent }}%;"></div>
                                </div>

                                @if ($isAvailable)
                                    <a href="{{ route('pengajuan.create') }}?product_id={{ $item->id }}"
                                        class="btn btn-sm btn-outline-primary w-100 d-inline-flex justify-content-center align-items-center gap-1.5 shadow-sm">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Ajukan Barang Ini
                                    </a>
                                @else
                                    <button type="button"
                                        class="btn btn-sm btn-warning text-white fw-bold w-100 d-inline-flex justify-content-center align-items-center gap-1.5 shadow-sm btn-pesan-katalog"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPesanKatalog"
                                        data-id="{{ $item->id }}"
                                        data-code="{{ $item->code }}"
                                        data-name="{{ $item->name }}"
                                        data-category="{{ $item->category->name ?? '-' }}"
                                        data-unit="{{ $item->unit ?? 'Buah' }}"
                                        data-available-units="{{ implode(',', $item->unit_list) }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                                        </svg>
                                        Pesan Barang Ini (Restock)
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12" id="katalog-initial-empty">
                    <div class="card shadow-sm border-0 py-5 text-center text-muted">
                        <svg class="mx-auto mb-2 text-muted" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <h5>Barang ATK Tidak Ditemukan</h5>
                        <p class="small mb-0">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination Katalog (8 Barang per Halaman) --}}
        <div class="mt-4" id="katalog-pagination-container">
            @if ($products->hasPages())
                <div class="card shadow-sm border-0">
                    <div class="card-body py-2.5 px-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="small text-muted">
                            Menampilkan <strong>{{ $products->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $products->lastItem() ?? 0 }}</strong> dari total <strong>{{ $products->total() }}</strong> barang ATK
                        </div>
                        <div class="katalog-pagination-links">
                            {{ $products->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Modal Pesan Barang (Stok Belum Tersedia) / Restock Langsung dari Katalog --}}
        <div class="modal fade" id="modalPesanKatalog" tabindex="-1" aria-labelledby="modalPesanKatalogLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('katalog.pesan') }}" method="POST" id="formPesanKatalog">
                        @csrf
                        <input type="hidden" name="product_id" id="modal-product-id" value="">

                        <div class="modal-header bg-warning text-dark border-0">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white rounded-circle p-1.5 d-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning">
                                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                        <line x1="3" y1="6" x2="21" y2="6"></line>
                                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalPesanKatalogLabel">Pesan Barang ATK (Stok Belum Tersedia)</h5>
                                    <small class="text-dark-50" style="font-size: 0.78rem;">Pemesanan otomatis diteruskan ke Kasubag TU untuk pengadaan</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            {{-- Alert Info Otomatis ke Pengadaan --}}
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-2.5 p-3 mb-3" style="font-size: 0.85rem; line-height: 1.45;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 text-info mt-0.5">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                <div>
                                    <strong>Stok barang ini belum tersedia di gudang (0).</strong> Anda tidak perlu membuka menu Permintaan Pengadaan secara terpisah. Pesanan ini akan langsung dicatat sebagai usulan pengadaan ke Subbag TU.
                                </div>
                            </div>

                            {{-- Informasi Barang yang Dipilih --}}
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-secondary font-monospace" id="modal-product-code" style="font-size: 0.75rem;">-</span>
                                        <span class="badge bg-danger">Stok Belum Tersedia (0)</span>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" id="modal-product-name">-</h6>
                                    <small class="text-muted d-block">
                                        Kategori: <strong id="modal-product-category" class="text-secondary">-</strong>
                                    </small>
                                </div>
                            </div>

                            {{-- Input Form --}}
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Jenis Permintaan <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select form-select-sm" required>
                                        <option value="individu" selected>
                                            Individu (Staf / Pegawai)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-secondary">Pemohon / Unit Kerja</label>
                                    <input type="text" class="form-control form-control-sm bg-light" value="{{ auth()->user()->name }} ({{ auth()->user()->position ?? 'Pegawai' }})" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Jumlah & Satuan yang Dipesan <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="quantity" id="modal-quantity" class="form-control fw-bold" min="1" value="1" required>
                                        <select name="custom_unit" id="modal-product-unit-select" class="form-select fw-semibold bg-light" style="max-width: 145px;">
                                            <option value="Buah">Buah</option>
                                        </select>
                                    </div>
                                    <span class="d-none" id="modal-product-unit">Buah</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">Tentukan jumlah dan pilih satuan standar barang yang dibutuhkan.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">Keperluan / Catatan Permintaan</label>
                                    <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Contoh: Kebutuhan operasional rapat koordinasi dan administrasi berkas bulan ini"></textarea>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 text-center border-top">
                                <a href="#" id="modal-link-form-lengkap" class="text-decoration-none small text-muted d-inline-flex align-items-center gap-1">
                                    <span>Atau ajukan melalui Formulir Permintaan Lengkap</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-0 py-2.5 px-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Kirim Pesanan Barang ke TU
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('katalog-search');
    const clearBtn = document.getElementById('btn-clear-search');
    const filterForm = document.getElementById('katalog-filter-form');
    const searchSpinner = document.getElementById('search-spinner');
    const searchIcon = document.getElementById('search-icon');
    let debounceTimer = null;
    let abortCtrl = null;

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // 1. Instant local card filtering (0ms)
    function filterCardsLocally(keyword) {
        const cards = document.querySelectorAll('#katalog-grid-container .katalog-card-item');
        let emptyEl = document.getElementById('no-local-match-katalog');
        const initialEmpty = document.getElementById('katalog-initial-empty');
        const categorySelect = filterForm ? filterForm.querySelector('select[name="category_id"]') : null;
        const selectedCat = categorySelect ? categorySelect.value : '';

        if (initialEmpty) {
            return;
        }

        let visibleCount = 0;
        cards.forEach(card => {
            const cardText = (card.getAttribute('data-search-text') || card.innerText).toLowerCase();
            const cardCat = card.getAttribute('data-category-id') || '';

            const matchesText = !keyword || cardText.includes(keyword);
            const matchesCat = !selectedCat || cardCat === selectedCat;

            if (matchesText && matchesCat) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            if (!emptyEl) {
                const grid = document.getElementById('katalog-grid-container');
                if (grid) {
                    emptyEl = document.createElement('div');
                    emptyEl.className = 'col-12';
                    emptyEl.id = 'no-local-match-katalog';
                    emptyEl.innerHTML = `
                        <div class="card shadow-sm border-0 py-5 text-center text-muted">
                            <div class="d-inline-flex align-items-center justify-content-center gap-2 mb-2">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                <span class="fw-bold">Mencari "<strong>${escapeHtml(keyword)}</strong>"...</span>
                            </div>
                            <p class="small mb-0">Sedang memeriksa ketersediaan stok di seluruh gudang...</p>
                        </div>
                    `;
                    grid.appendChild(emptyEl);
                }
            }
        } else {
            if (emptyEl) emptyEl.remove();
        }
    }

    // 2. Debounced background AJAX sync to server
    function performServerSearch(targetUrl = null) {
        let url = targetUrl;
        if (!url && filterForm) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = filterForm.action + '?' + params.toString();
        }

        if (!url) return;

        if (abortCtrl) {
            abortCtrl.abort();
        }
        abortCtrl = new AbortController();

        if (searchSpinner) searchSpinner.classList.remove('d-none');
        if (searchIcon) searchIcon.classList.add('d-none');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: abortCtrl.signal
        })
        .then(response => {
            if (!response.ok) throw new Error('Response error: ' + response.status);
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newGrid = doc.getElementById('katalog-grid-container');
            const currentGrid = document.getElementById('katalog-grid-container');
            if (newGrid && currentGrid) {
                currentGrid.innerHTML = newGrid.innerHTML;
            }

            const newReset = doc.getElementById('katalog-reset-container');
            const currentReset = document.getElementById('katalog-reset-container');
            if (newReset && currentReset) {
                currentReset.innerHTML = newReset.innerHTML;
            }

            const newPagination = doc.getElementById('katalog-pagination-container');
            const currentPagination = document.getElementById('katalog-pagination-container');
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }

            // Sync URL in address bar without reload
            window.history.replaceState(null, '', url);

            // Re-apply local filter if user kept typing while AJAX was in-flight
            if (searchInput) {
                const currentVal = searchInput.value.trim().toLowerCase();
                let searchedVal = '';
                try {
                    const urlParams = new URL(url, window.location.origin).searchParams;
                    searchedVal = (urlParams.get('search') || '').trim().toLowerCase();
                } catch (e) {}

                if (currentVal !== searchedVal) {
                    filterCardsLocally(currentVal);
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error saat sinkronisasi katalog:', err);
            }
        })
        .finally(() => {
            if (searchSpinner) searchSpinner.classList.add('d-none');
            if (searchIcon) searchIcon.classList.remove('d-none');
        });
    }

    if (searchInput) {
        // As-you-type input handler
        searchInput.addEventListener('input', function(e) {
            const val = e.target.value;
            const kw = val.trim().toLowerCase();

            // Toggle clear button
            if (clearBtn) {
                clearBtn.style.display = val.length > 0 ? '' : 'none';
            }

            // Instant local filter (0ms delay)
            filterCardsLocally(kw);

            // Debounced server sync (350ms)
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performServerSearch();
            }, 350);
        });

        // Clear button click
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                searchInput.focus();
                filterCardsLocally('');
                clearTimeout(debounceTimer);
                performServerSearch();
            });
        }
    }

    // Intercept form submit (Enter key or Cari button click)
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            performServerSearch();
        });

        // Dynamic change for dropdown select (Kategori)
        filterForm.querySelectorAll('select').forEach(select => {
            select.onchange = function(e) {
                e.preventDefault();
                clearTimeout(debounceTimer);
                performServerSearch();
            };
        });
    }

    // Intercept pagination link clicks for smooth AJAX navigation
    document.addEventListener('click', function(e) {
        const pageLink = e.target.closest('#katalog-pagination-container a.page-link');
        if (pageLink && pageLink.href) {
            e.preventDefault();
            performServerSearch(pageLink.href);
            const gridEl = document.getElementById('katalog-filter-form');
            if (gridEl) {
                gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    // Modal Pesan Barang Habis dari Katalog
    const modalEl = document.getElementById('modalPesanKatalog');
    const standardUnits = ['Buah', 'Dus', 'Rim', 'Pak', 'Kotak', 'Doz', 'Set', 'Roll', 'Slop', 'Buku', 'Tube', 'Box', 'Botol', 'Lusin', 'Pcs'];
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget ? (event.relatedTarget.closest('[data-bs-toggle="modal"]') || event.relatedTarget) : null;
            if (!button) return;

            const id = button.getAttribute('data-id') || '';
            const code = button.getAttribute('data-code') || '';
            const name = button.getAttribute('data-name') || '';
            const category = button.getAttribute('data-category') || '';
            const unit = button.getAttribute('data-unit') || 'Buah';
            const availUnitsStr = button.getAttribute('data-available-units') || unit;
            const availUnits = availUnitsStr.split(',').map(u => u.trim()).filter(Boolean);

            const inputId = document.getElementById('modal-product-id');
            const spanCode = document.getElementById('modal-product-code');
            const spanName = document.getElementById('modal-product-name');
            const spanCat = document.getElementById('modal-product-category');
            const spanUnit = document.getElementById('modal-product-unit');
            const unitSelect = document.getElementById('modal-product-unit-select');
            const inputQty = document.getElementById('modal-quantity');
            const linkLengkap = document.getElementById('modal-link-form-lengkap');

            if (inputId) inputId.value = id;
            if (spanCode) spanCode.textContent = code;
            if (spanName) spanName.textContent = name;
            if (spanCat) spanCat.textContent = category;
            if (spanUnit) spanUnit.textContent = unit;
            if (unitSelect) {
                unitSelect.innerHTML = '';
                const added = new Set();
                availUnits.forEach(u => {
                    added.add(u.toLowerCase());
                    const opt = document.createElement('option');
                    opt.value = u;
                    opt.textContent = u + ' (Standar)';
                    if (u.toLowerCase() === unit.toLowerCase()) opt.selected = true;
                    unitSelect.appendChild(opt);
                });
                standardUnits.forEach(u => {
                    if (!added.has(u.toLowerCase())) {
                        const opt = document.createElement('option');
                        opt.value = u;
                        opt.textContent = u;
                        unitSelect.appendChild(opt);
                    }
                });
            }
            if (inputQty) inputQty.value = '1';
            if (linkLengkap) {
                linkLengkap.href = "{{ route('pengajuan.create') }}?product_id=" + encodeURIComponent(id);
            }
        });
    }
});
</script>
@endpush
