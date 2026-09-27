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
                        <a href="{{ route('pengajuan.create') }}" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                            + Buat Pengajuan ATK
                        </a>
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
                        <form action="{{ route('katalog-atk') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </span>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari nama barang atau kode ATK..." value="{{ $search }}">
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

                            <div class="col-12 col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">Cari Barang</button>
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
        <div class="row g-3">
            @forelse ($products as $item)
                @php
                    $isAvailable = $item->stock > 0;
                    $isLowStock = $item->stock > 0 && $item->stock <= ($item->minimum_stock ?? 5);
                @endphp
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                        {{ $item->code }}
                                    </span>
                                    @if ($item->stock <= 0)
                                        <span class="badge bg-danger">Stok Habis</span>
                                    @elseif ($isLowStock)
                                        <span class="badge bg-warning text-dark">Stok Menipis</span>
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
                                        {{ number_format($item->stock) }} <small class="fs-6 fw-normal text-muted">{{ $item->unit }}</small>
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
                <div class="col-12">
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
