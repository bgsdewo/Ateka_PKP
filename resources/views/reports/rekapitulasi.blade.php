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
                            <h1 class="text-white fw-bold mb-1">Rekapitulasi Mutasi & Arsip Dokumen</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Pengelolaan arsip persediaan, rekap data pergerakan barang masuk/keluar, dan penyusunan berita acara resmi kantor.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info text-white px-3 py-2 fw-bold">
                                Arsiparis (Pelaporan & Administrasi)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        {{-- KPI Cards Arsiparis --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Total Item Terdaftar</span>
                            <span class="badge bg-primary text-white rounded-pill">Master ATK</span>
                        </div>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($totalAllProducts) }} <small class="fs-6 text-muted">Barang</small></h2>
                        <small class="text-muted">Dalam {{ $categoriesRekap->count() }} Kategori ATK</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Total Saldo Fisik Stok</span>
                            <span class="badge bg-success text-white rounded-pill">Gudang</span>
                        </div>
                        <h2 class="fw-bold text-success mb-0">{{ number_format($totalAllStock) }} <small class="fs-6 text-muted">Pcs</small></h2>
                        <small class="text-muted">Kondisi persediaan saat ini</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold text-uppercase">Nilai Aset Saldo Persediaan</span>
                            <span class="badge bg-warning text-dark rounded-pill">Valuasi</span>
                        </div>
                        <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($totalAllValue, 0, ',', '.') }}</h2>
                        <small class="text-muted">Total nilai perolehan persediaan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Menu Cetak Berita Acara --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3">
                        <form action="{{ route('reports.rekapitulasi') }}" method="GET" class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
                                {{-- Periode Bulan --}}
                                <div>
                                    <label class="small fw-bold text-secondary text-uppercase mb-1 d-block" style="font-size: 0.72rem;">Bulan:</label>
                                    <select name="month" class="form-select form-select-sm" style="min-width: 140px;">
                                        <option value="">Semua Bulan (Tahunan)</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" {{ ($month ?? '') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Periode Tahun --}}
                                <div>
                                    <label class="small fw-bold text-secondary text-uppercase mb-1 d-block" style="font-size: 0.72rem;">Tahun:</label>
                                    <select name="year" class="form-select form-select-sm" style="min-width: 95px;">
                                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ ($year ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>

                                {{-- Filter Barang Masuk & Barang Keluar --}}
                                <div>
                                    <label class="small fw-bold text-secondary text-uppercase mb-1 d-block" style="font-size: 0.72rem;">Jenis Mutasi:</label>
                                    <select name="type" id="filter-type" class="form-select form-select-sm" style="min-width: 165px;">
                                        <option value="">Semua (Masuk & Keluar)</option>
                                        <option value="masuk" {{ ($type ?? '') == 'masuk' ? 'selected' : '' }}>Hanya Barang Masuk</option>
                                        <option value="keluar" {{ ($type ?? '') == 'keluar' ? 'selected' : '' }}>Hanya Barang Keluar</option>
                                    </select>
                                </div>

                                {{-- Filter Pegawai & Subdit --}}
                                <div>
                                    <label class="small fw-bold text-secondary text-uppercase mb-1 d-block" style="font-size: 0.72rem;">Penerima / Pemohon:</label>
                                    <select name="recipient_type" id="filter-recipient-type" class="form-select form-select-sm" style="min-width: 175px;">
                                        <option value="">Semua (Pegawai & Subdit)</option>
                                        <option value="pegawai" {{ in_array(($recipientType ?? ''), ['pegawai', 'individu']) ? 'selected' : '' }}>👤 Pegawai (Individu)</option>
                                        <option value="subdit" {{ ($recipientType ?? '') == 'subdit' ? 'selected' : '' }}>🏢 Subdit (Kolektif)</option>
                                    </select>
                                </div>

                                {{-- Tombol Terapkan & Reset --}}
                                <div class="d-flex align-items-end gap-1 mt-auto">
                                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-semibold">
                                        Terapkan
                                    </button>
                                    <a href="{{ route('reports.rekapitulasi') }}" class="btn btn-sm btn-light border" title="Reset semua filter">
                                        Reset
                                    </a>
                                </div>
                            </div>

                            {{-- Tombol Cetak Berita Acara --}}
                            <div class="d-flex align-items-end mt-auto pt-2 pt-lg-0">
                                <a href="{{ route('reports.berita-acara', array_filter(['year' => $year, 'month' => $month, 'type' => $type, 'recipient_type' => $recipientType])) }}" target="_blank"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg>
                                    Cetak Berita Acara Rekapitulasi Resmi
                                </a>
                            </div>
                        </form>

                        {{-- Indikator Filter Aktif --}}
                        @if (!empty($type) || !empty($recipientType) || !empty($month))
                            <div class="mt-2.5 pt-2 border-top d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="small text-muted fw-bold" style="font-size: 0.75rem;">Filter Aktif:</span>
                                @if (!empty($month))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        Bulan: {{ \Carbon\Carbon::createFromDate(2026, (int)$month, 1)->translatedFormat('F') }}
                                    </span>
                                @endif
                                @if (!empty($type))
                                    <span class="badge {{ $type == 'masuk' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        Jenis: {{ $type == 'masuk' ? 'Hanya Barang Masuk' : 'Hanya Barang Keluar' }}
                                    </span>
                                @endif
                                @if (!empty($recipientType))
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        Penerima: {{ $recipientType == 'subdit' ? '🏢 Subdit (Kolektif)' : '👤 Pegawai (Individu)' }}
                                    </span>
                                @endif
                                <a href="{{ route('reports.rekapitulasi', ['year' => $year]) }}" class="text-danger small fw-semibold text-decoration-none ms-1" style="font-size: 0.72rem;">
                                    Hapus Semua Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Rekapitulasi per Kategori --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0 fw-bold">Rekapitulasi Mutasi per Kategori Barang ATK</h4>
                            <small class="text-muted">Data rangkuman mutasi barang masuk, barang keluar, dan sisa stok persediaan untuk arsip</small>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">
                            {{ $categoriesRekap->count() }} Kategori
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Kategori ATK</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-center text-success">Barang Masuk</th>
                                        <th class="text-center text-danger">Barang Keluar</th>
                                        <th class="text-center">Sisa Stok Fisik</th>
                                        <th class="text-end">Nilai Saldo (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sumMasuk = 0;
                                        $sumKeluar = 0;
                                        $sumStok = 0;
                                        $sumNilai = 0;
                                    @endphp
                                    @forelse ($categoriesRekap as $index => $cat)
                                        @php
                                            $sumMasuk += $cat->total_in;
                                            $sumKeluar += $cat->total_out;
                                            $sumStok += $cat->current_stock;
                                            $sumNilai += $cat->total_value;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-dark">{{ $cat->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill">
                                                    {{ $cat->product_count }} Jenis
                                                </span>
                                            </td>
                                            <td class="text-center fw-semibold text-success">
                                                +{{ number_format($cat->total_in) }} Pcs
                                            </td>
                                            <td class="text-center fw-semibold text-danger">
                                                -{{ number_format($cat->total_out) }} Pcs
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ number_format($cat->current_stock) }} Pcs
                                            </td>
                                            <td class="text-end font-monospace fw-bold text-dark">
                                                Rp {{ number_format($cat->total_value, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light fw-bold border-top border-2">
                                    <tr>
                                        <td colspan="3" class="text-center text-uppercase">Total Rekapitulasi Keseluruhan</td>
                                        <td class="text-center text-success">+{{ number_format($sumMasuk) }} Pcs</td>
                                        <td class="text-center text-danger">-{{ number_format($sumKeluar) }} Pcs</td>
                                        <td class="text-center text-dark">{{ number_format($sumStok) }} Pcs</td>
                                        <td class="text-end text-primary font-monospace">Rp {{ number_format($sumNilai, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('filter-type');
            const recipientSelect = document.getElementById('filter-recipient-type');

            function syncRecipientState() {
                if (typeSelect && recipientSelect) {
                    if (typeSelect.value === 'masuk') {
                        recipientSelect.value = '';
                        recipientSelect.disabled = true;
                        recipientSelect.title = 'Filter penerima hanya berlaku untuk barang keluar';
                    } else {
                        recipientSelect.disabled = false;
                        recipientSelect.title = '';
                    }
                }
            }

            if (typeSelect) {
                typeSelect.addEventListener('change', syncRecipientState);
                syncRecipientState();
            }
        });
    </script>
@endpush
