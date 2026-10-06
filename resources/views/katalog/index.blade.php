@extends('layouts.app')

@section('page-header')
    <style>
        .iq-navbar-header {
            position: relative;
            background-image: linear-gradient(rgba(0, 70, 80, 0.45), rgba(0, 70, 80, 0.45)), url("{{ asset('hope-ui/assets/images/dashboard/bg-2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .iq-navbar-header .iq-container {
            position: relative;
            z-index: 2;
        }
    </style>
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Katalog Alat Tulis Kantor (ATK)</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Cek ketersediaan stok ATK terkini di gudang sebelum mengajukan permohonan kebutuhan kantor.
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
        {{-- Baris Filter & Pencarian --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <form id="katalog-filter-form" action="{{ route('katalog-atk') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <span id="search-spinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                                        <svg id="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </span>
                                    <input type="text" id="katalog-search" name="search" class="form-control"
                                        placeholder="Cari nama barang atau kode ATK..." value="{{ $search }}" autocomplete="off">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-clear-search" style="{{ empty($search) ? 'display: none;' : '' }}" title="Hapus pencarian">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <select name="category_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Semua Kategori Barang --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-3 d-flex gap-2" id="katalog-reset-container">
                                <button type="submit" id="btn-katalog-search" class="btn btn-primary flex-grow-1">Cari Barang</button>
                                @if ($search || $categoryId)
                                    <a href="{{ route('katalog-atk') }}" class="btn btn-light border" title="Reset Filter">
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
                @endphp
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 katalog-card-item"
                    data-search-text="{{ strtolower($item->name . ' ' . $item->code . ' ' . ($item->category->name ?? '')) }}"
                    data-category-id="{{ $item->category_id }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                        {{ $item->code }}
                                    </span>
                                    @if ($item->stock <= 0)
                                        <span class="badge bg-danger">Stok Habis</span>
                                    @else
                                        <span class="badge bg-success">Tersedia</span>
                                    @endif
                                </div>

                                <h6 class="fw-bold text-dark mb-1" style="min-height: 38px; line-height: 1.3;">
                                    {{ $item->name }}
                                </h6>
                                <small class="text-muted d-block mb-3">
                                    Kategori: <strong class="text-secondary">{{ $item->category->name ?? '-' }}</strong>
                                </small>
                            </div>

                            <div class="border-top pt-2.5 mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Sisa Stok Fisik:</span>
                                    <span class="fw-bold {{ $item->stock <= 0 ? 'text-danger' : 'text-primary' }} fs-5">
                                        {{ number_format($item->stock) }} <small class="fs-6 fw-normal text-muted">Stok</small>
                                    </span>
                                </div>

                                @if ($isAvailable)
                                    <a href="{{ route('pengajuan.create') }}?product_id={{ $item->id }}"
                                        class="btn btn-sm btn-outline-primary w-100 d-inline-flex justify-content-center align-items-center gap-1">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Ajukan Barang Ini
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-light text-muted w-100 border" disabled>
                                        Tidak Dapat Diajukan (Kosong)
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
});
</script>
@endpush
