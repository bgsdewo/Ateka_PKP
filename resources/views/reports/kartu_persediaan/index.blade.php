@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header py-0" style="height: auto; min-height: 0; background: transparent; box-shadow: none; border: none; padding: 1.25rem 2rem 0.5rem 2rem; margin-bottom: 0.75rem !important;">
        <div class="container-fluid iq-container p-0">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h1 class="fw-bold mb-1" style="color: #0c2b27 !important; font-size: var(--ateka-h1-size, 2.25rem) !important; letter-spacing: -0.035em !important; line-height: 1.15 !important; text-shadow: none !important;">
                                Kartu Persediaan Barang
                            </h1>
                            <p class="mb-0 text-muted" style="font-size: 0.84rem !important; color: #64748b !important; text-shadow: none !important;">
                                Sistem Informasi Inventori Persediaan ATK (ATEKA) &bull; <strong style="color: #10b981;">Buku Kendali Mutasi Persediaan BMN</strong>
                            </p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5 shadow-sm d-inline-flex align-items-center gap-1.5 fw-semibold" style="border-color: #94a3b8 !important; color: #0c2b27 !important; background-color: #ffffff !important;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                Kembali ke Laporan Mutasi
                            </a>
                            @if ($activeProducts->count() > 1)
                                <a href="{{ route('reports.kartu-persediaan.cetak', ['product_id' => 'all', 'month' => $month, 'year' => $year]) }}" target="_blank"
                                    class="btn btn-info rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold text-white"
                                    title="Cetak sekaligus semua kartu barang yang ada mutasi di bulan ini">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg>
                                    Cetak Semua Kartu Bulan Ini ({{ $activeProducts->count() }})
                                </a>
                            @endif
                            @if ($selectedProduct)
                                <a href="{{ route('reports.kartu-persediaan.cetak', ['product_id' => $selectedProduct->id, 'month' => $month, 'year' => $year]) }}" target="_blank"
                                    class="btn btn-warning rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5 fw-bold text-dark">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                        <rect x="6" y="14" width="12" height="8"></rect>
                                    </svg>
                                    Cetak Kartu Persediaan (A4)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .report-card {
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            background: #ffffff;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .form-select-modern, .form-control-modern {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-select-modern:focus, .form-control-modern:focus {
            background-color: #ffffff;
            border-color: #3a57e8;
            box-shadow: 0 0 0 3px rgba(58, 87, 232, 0.15);
            outline: none;
        }

        .card-table-official {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.835rem;
        }

        .card-table-official th, .card-table-official td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .card-table-official thead th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-align: center;
        }

        .sub-header-num th {
            background-color: #e2e8f0 !important;
            font-size: 0.75rem;
            color: #475569;
            padding: 4px 6px;
        }

        .pattern-shaded {
            background-color: #f1f5f9;
            background-image: repeating-linear-gradient(45deg, #e2e8f0 0, #e2e8f0 2px, transparent 0, transparent 8px);
        }

        .active-item-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.815rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .active-item-pill:hover {
            background: #eef2ff;
            color: #3a57e8;
            border-color: #3a57e8;
        }

        .active-item-pill.active {
            background: #3a57e8;
            color: #ffffff;
            border-color: #3a57e8;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(58, 87, 232, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid iq-container py-0" style="margin-top: 0.75rem !important; padding-bottom: 2.5rem !important;">
        {{-- Card Filter & Pemilihan Barang Cerdas --}}
        <div class="card report-card mb-4 shadow-sm">
            <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Pilih Barang &amp; Periode Kartu Persediaan</h5>
                        <p class="text-muted small mb-0">Saring kartu <i>stok</i> persediaan berdasarkan jenis barang ATK, bulan, dan tahun anggaran</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Total Bermutasi di Periode Ini: <strong class="text-primary">{{ $activeProducts->count() }} Barang</strong>
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                {{-- Quick Access: Tombol Pintas Barang yang Bermutasi di Bulan Ini --}}
                @if ($activeProducts->isNotEmpty())
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                            <span>
                                <svg width="15" height="15" class="me-1 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                                <strong>Akses Cepat Barang Bermutasi ({{ $month ? $monthName : 'Periode Ini' }}):</strong>
                                <span class="text-muted fw-normal ms-1">(Klik salah satu tanpa perlu <i>scroll</i>)</span>
                            </span>
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($activeProducts as $ap)
                                <a href="{{ route('reports.kartu-persediaan', ['product_id' => $ap->id, 'month' => $month, 'year' => $year, 'only_active' => $onlyActive ? 1 : 0]) }}"
                                    class="active-item-pill {{ ($selectedProduct && $selectedProduct->id == $ap->id) ? 'active' : '' }}">
                                    <span>{{ $ap->name }}</span>
                                    <span class="badge rounded-pill {{ ($selectedProduct && $selectedProduct->id == $ap->id) ? 'bg-light text-primary' : 'bg-primary-subtle text-primary' }}" style="font-size: 0.72rem;">
                                        {{ $ap->transactions_count }} mutasi
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('reports.kartu-persediaan') }}" method="GET" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-12 col-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-dark small mb-0">
                                Pilih Jenis Barang ATK <span class="text-danger">*</span>
                            </label>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="checkbox" name="only_active" value="1" id="onlyActiveCheck" {{ $onlyActive ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                <label class="form-check-label small text-muted" for="onlyActiveCheck" style="font-size: 0.78rem;">
                                    Hanya barang bermutasi ({{ $activeProducts->count() }})
                                </label>
                            </div>
                        </div>

                        {{-- Input Live Filter Cepat --}}
                        <input type="text" id="liveSearchInput" class="form-control form-control-sm mb-1.5" placeholder="Ketik untuk memfilter daftar barang..." style="border-radius: 8px; font-size: 0.8rem;" onkeyup="filterDropdownOptions()">

                        <select name="product_id" id="productSelect" class="form-select form-select-modern" required onchange="this.form.submit()">
                            @if ($onlyActive && $activeProducts->isNotEmpty())
                                <optgroup label="-- BARANG DENGAN MUTASI BULAN INI ({{ $activeProducts->count() }}) --">
                                    @foreach ($activeProducts as $p)
                                        <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                            [{{ $p->bmn_code ?: ($p->code ?: 'ATK') }}] {{ $p->name }} ({{ $p->unit }}) - {{ $p->transactions_count }} Transaksi
                                        </option>
                                    @endforeach
                                </optgroup>
                            @else
                                @if ($activeProducts->isNotEmpty())
                                    <optgroup label="-- BARANG DENGAN MUTASI BULAN INI ({{ $activeProducts->count() }}) --">
                                        @foreach ($activeProducts as $p)
                                            <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                                ★ [{{ $p->bmn_code ?: ($p->code ?: 'ATK') }}] {{ $p->name }} ({{ $p->unit }}) - {{ $p->transactions_count }} Transaksi
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <optgroup label="-- SEMUA KATALOG BARANG ATK ({{ $allProducts->count() }}) --">
                                    @foreach ($allProducts as $p)
                                        <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                            [{{ $p->bmn_code ?: ($p->code ?: 'ATK') }}] {{ $p->name }} ({{ $p->unit }}) - Sisa <i>Stok</i>: {{ $p->stock }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold text-dark small mb-1">Bulan Anggaran</label>
                        <select name="month" class="form-select form-select-modern" onchange="this.form.submit()">
                            <option value="">Semua Bulan (Kumulatif)</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-dark small mb-1">Tahun Anggaran</label>
                        <select name="year" class="form-select form-select-modern" onchange="this.form.submit()">
                            @for ($y = date('Y') + 1; $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5" style="height: 42px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($selectedProduct)
            {{-- Header Informasi Kartu Persediaan (Sesuai Format Gambar Resmi) --}}
            <div class="card report-card mb-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-12 col-md-4">
                            <table class="table table-borderless table-sm mb-0" style="font-size: 0.875rem;">
                                <tr>
                                    <td class="fw-bold text-muted ps-0" style="width: 110px;">BULAN</td>
                                    <td class="fw-bold" style="width: 15px;">:</td>
                                    <td class="fw-bold text-primary">{{ $month ? $monthName : 'Semua Bulan' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted ps-0">UAKPB</td>
                                    <td class="fw-bold">:</td>
                                    <td>Satker Direktorat Jenderal Kawasan Permukiman</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted ps-0">KODE UAKPB</td>
                                    <td class="fw-bold">:</td>
                                    <td class="font-monospace">146.03.01.994.694308.000</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-12 col-md-4 text-center my-3 my-md-0">
                            <h4 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.5px;">KARTU PERSEDIAAN BARANG</h4>
                            <h5 class="fw-bold text-secondary mb-0">TAHUN ANGGARAN {{ $year }}</h5>
                        </div>

                        <div class="col-12 col-md-4">
                            <table class="table table-borderless table-sm mb-0" style="font-size: 0.875rem;">
                                <tr>
                                    <td class="fw-bold text-muted ps-0" style="width: 120px;">KODE BARANG</td>
                                    <td class="fw-bold" style="width: 15px;">:</td>
                                    <td class="font-monospace fw-bold">{{ $selectedProduct->kode_barang_laporan }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted ps-0">JENIS BARANG</td>
                                    <td class="fw-bold">:</td>
                                    <td class="fw-bold text-dark">{{ $selectedProduct->nama_jenis }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted ps-0">SATUAN</td>
                                    <td class="fw-bold">:</td>
                                    <td>{{ $selectedProduct->unit ?: 'pak' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Tabel 12 Kolom Resmi --}}
                    <div class="table-responsive">
                        <table class="card-table-official">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 45px;">NO</th>
                                    <th rowspan="2" style="width: 140px;">NO. FAKTUR</th>
                                    <th rowspan="2" style="width: 110px;">TANGGAL FAKTUR</th>
                                    <th rowspan="2" style="min-width: 180px;">URAIAN</th>
                                    <th rowspan="2" style="width: 90px;">KONDISI BARANG</th>
                                    <th rowspan="2" style="width: 115px;">HARGA PEROLEHAN</th>
                                    <th rowspan="2" style="width: 70px;">MASUK</th>
                                    <th rowspan="2" style="width: 70px;">KELUAR</th>
                                    <th colspan="2">SALDO</th>
                                    <th rowspan="2" style="width: 80px;">PARAF</th>
                                    <th rowspan="2" style="width: 150px;">KETERANGAN</th>
                                </tr>
                                <tr>
                                    <th style="width: 75px;">Jumlah</th>
                                    <th style="width: 115px;">Nilai</th>
                                </tr>
                                <tr class="sub-header-num text-center">
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                    <th>6</th>
                                    <th>7</th>
                                    <th>8</th>
                                    <th>9 = (7-8)</th>
                                    <th>10 = (6x9)</th>
                                    <th>11</th>
                                    <th>12</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $runningStock = $saldoAwal;
                                    $rowNo = 1;
                                @endphp

                                @forelse ($transactions as $t)
                                    @php
                                        $inQty = $t->type === 'masuk' ? (int)$t->quantity : 0;
                                        $outQty = $t->type === 'keluar' ? (int)$t->quantity : 0;
                                        $runningStock = max(0, $runningStock + ($inQty - $outQty));
                                        $hargaSatuan = $t->harga_satuan;
                                        $nilaiSaldo = $runningStock * $hargaSatuan;

                                        // Kolom No Faktur: jangan tampilkan kode REQ-BUY dan jangan pakai "-" jika kosong
                                        $isReqCode = str_contains(strtoupper($t->no_faktur ?? ''), 'REQ');
                                        $displayFaktur = ($t->type === 'masuk' && !$isReqCode) ? ($t->no_faktur ?: '') : '';
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-semibold">
                                            {{ $t->type === 'masuk' ? $rowNo++ : '' }}
                                        </td>
                                        <td class="text-center font-monospace small">
                                            {{ $displayFaktur }}
                                        </td>
                                        {{-- Tanggal Faktur: hanya untuk barang masuk, sebaris dengan penerima dikosongkan --}}
                                        <td class="text-center small">
                                            {{ ($t->type === 'masuk' && $t->transaction_date) ? \Carbon\Carbon::parse($t->transaction_date)->translatedFormat('d F Y') : '' }}
                                        </td>
                                        <td>
                                            @if ($t->type === 'masuk')
                                                <span class="fw-bold text-primary">{{ $t->uraian ?: ($t->note ?: 'Penerimaan Barang') }}</span>
                                            @else
                                                <span class="fw-bold text-dark">{{ $t->recipient_name }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($t->type === 'masuk')
                                                <span class="badge bg-success-subtle text-success">{{ $t->kondisi ?: 'Baru' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end font-monospace">
                                            @if ($t->type === 'masuk')
                                                Rp {{ number_format($hargaSatuan, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="text-center font-monospace fw-bold {{ $inQty > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ $inQty > 0 ? $inQty : '0' }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold {{ $outQty > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ $outQty > 0 ? $outQty : '0' }}
                                        </td>
                                        <td class="text-center font-monospace fw-bold bg-light">
                                            {{ $runningStock }}
                                        </td>
                                        <td class="text-end font-monospace {{ $t->type === 'keluar' ? 'pattern-shaded' : 'fw-bold bg-light' }}">
                                            @if ($t->type === 'masuk')
                                                Rp {{ number_format($nilaiSaldo, 0, ',', '.') }}
                                            @else
                                                &nbsp;
                                            @endif
                                        </td>
                                        {{-- Kolom 11: Paraf (Kosongkan tanpa tanda "-" untuk paraf manual saat dicetak) --}}
                                        <td class="text-center text-muted" style="height: 32px;">
                                            &nbsp;
                                        </td>
                                        {{-- Kolom 12: Keterangan (Penyerahan "jenis barang" kepada "nama") --}}
                                        <td class="small text-muted {{ $t->type === 'keluar' && empty($t->note) ? 'pattern-shaded' : '' }}">
                                            @if ($t->type === 'keluar')
                                                Penyerahan {{ $selectedProduct->name }} kepada {{ $t->recipient_name }}
                                            @else
                                                {{ $t->note ?: ('Penerimaan ' . $selectedProduct->name) }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-muted">
                                            Belum ada rekaman transaksi mutasi persediaan untuk barang ini pada periode yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card report-card text-center p-5">
                <h5 class="text-muted">Silakan pilih barang ATK terlebih dahulu untuk menampilkan Kartu Persediaan.</h5>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function filterDropdownOptions() {
                var query = document.getElementById('liveSearchInput').value.toLowerCase();
                var select = document.getElementById('productSelect');
                var options = select.getElementsByTagName('option');

                for (var i = 0; i < options.length; i++) {
                    var text = options[i].text.toLowerCase();
                    if (text.indexOf(query) > -1) {
                        options[i].style.display = '';
                    } else {
                        options[i].style.display = 'none';
                    }
                }
            }
        </script>
    @endpush
@endsection
