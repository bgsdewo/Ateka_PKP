<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Mutasi & Persediaan Barang (ATK) - {{ $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css') }}">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }

        body {
            background-color: #f3f4f6;
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            color: #111;
            font-size: 8.5pt;
        }

        .paper {
            background: #fff;
            width: 297mm;
            min-height: 210mm;
            padding: 12mm 15mm;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .kop-surat {
            border-bottom: 2.5px solid #144B59;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 7.8pt;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #94a3b8;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .table-items th {
            background-color: #144B59 !important;
            color: #ffffff !important;
            text-align: center;
            font-weight: 700;
            font-size: 8pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-items tfoot th {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-items tbody tr:nth-child(even) {
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: #144B59;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        @media print {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }

            .signature-section {
                page-break-inside: avoid !important;
            }

            tr {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>

<body>
    @php
        // 1. Kolom Memeriksa: Pegawai Gudang yang ditugaskan sebagai Petugas Pemeriksa Laporan
        $examinerUser = \App\Models\User::where('role', 'pegawai_gudang')->where('is_report_examiner', true)->first()
            ?: \App\Models\User::where('role', 'pegawai_gudang')->first();
        $examinerName = $examinerUser ? ($examinerUser->name_with_title ?: $examinerUser->name) : 'Petugas Pemeriksa';
        $examinerNip = $examinerUser && $examinerUser->nip ? $examinerUser->nip : '-';
        $examinerTitle = 'Petugas Pemeriksa Laporan';

        // 2. Kolom Menyetujui: Kasubbag TU
        $kasubagUser = \App\Models\User::whereIn('role', ['kasubag_tu', 'admin'])->first();
        $kasubagName = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
        $kasubagNip = $kasubagUser->nip ?? '19850612 201001 2 003';
        $kasubagTitle = 'Kasubbag Tata Usaha';

        // 3. Kolom Mengetahui: Pegawai Gudang yang ditugaskan sebagai Petugas Distribusi (Yang Menyerahkan Barang)
        $distributorUser = \App\Models\User::where('role', 'pegawai_gudang')
            ->where('is_distributor', true)
            ->latest('id')
            ->first()
            ?: \App\Models\User::where('role', 'pic_distribusi')->latest('id')->first()
            ?: \App\Models\User::where('role', 'pegawai_gudang')->latest('id')->first();

        $mengetahuiTitle = $distributorUser ? ($distributorUser->position ?: 'Petugas Distribusi Gudang ATK') : 'Petugas Distribusi Gudang ATK';
        $mengetahuiName = $distributorUser ? ($distributorUser->name_with_title ?: $distributorUser->name) : 'Petugas Distribusi';
        $mengetahuiNip = $distributorUser && $distributorUser->nip ? $distributorUser->nip : '-';

        // Variabel akumulasi total footer
        $runningTotalAwal = 0;
        $runningTotalDipinjam = 0;
        $runningTotalSisa = 0;
        $runningTotalSaldo = 0;
    @endphp

    <!-- Bar Navigasi Cetak Sticky -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold">Dokumen Resmi Laporan Mutasi &amp; Persediaan Barang (ATK)</span>
            <span class="badge bg-warning text-dark">{{ $periodLabel }}</span>
            @if (!empty($type))
                <span class="badge {{ $type == 'masuk' ? 'bg-success' : 'bg-danger' }}">
                    {{ $type == 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}
                </span>
            @endif
            @if (!empty($recipientType))
                <span class="badge bg-info text-dark">
                    {{ $recipientType == 'subdit' ? 'Penerima: Subdit' : 'Penerima: Pegawai' }}
                </span>
            @endif
            @if (!empty($selectedCategory))
                <span class="badge bg-light text-dark">
                    Kategori: {{ $selectedCategory->name }}
                </span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Cetak Dokumen Resmi (Ctrl+P)
            </button>
            <button onclick="window.close(); if(history.length > 1) { history.back(); }" class="btn btn-outline-light btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Kertas Resmi -->
    <div class="paper">
        <!-- KOP SURAT KEMENTERIAN RESMI -->
        <div class="kop-surat d-flex align-items-center">
            <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                style="width: 72px; height: 72px; object-fit: contain;" class="me-3">
            <div class="flex-grow-1 text-center">
                <h4 class="mb-0 fw-bold text-uppercase" style="font-size: 13.5pt; color: #144B59; letter-spacing: 0.5px;">
                    KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN REPUBLIK INDONESIA
                </h4>
                <p class="mb-0 fw-semibold text-secondary" style="font-size: 9pt;">
                    SEKRETARIAT JENDERAL - PENGELOLAAN ARSIP &amp; PERSEDIAAN BARANG (ATK)
                </p>
                <h5 class="mb-0 fw-bold mt-1 text-dark" style="font-size: 11pt;">
                    LAPORAN MUTASI &amp; PERSEDIAAN BARANG (ATK)
                </h5>
                <p class="mb-0 small text-muted" style="font-size: 8.5pt;">
                    Periode: <strong>{{ $periodLabel }}</strong> &bull; Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    @if (!empty($type) || !empty($recipientType) || !empty($selectedCategory))
                        &bull; Filter: 
                        {{ $type ? ($type == 'masuk' ? 'Barang Masuk' : 'Barang Keluar') : 'Semua Mutasi' }}
                        {{ $recipientType ? ' (' . ($recipientType == 'subdit' ? 'Subdit' : 'Pegawai') . ')' : '' }}
                        {{ $selectedCategory ? ' - Kategori: ' . $selectedCategory->name : '' }}
                    @endif
                </p>
            </div>
        </div>

        <!-- TABEL RINCIAN MUTASI PERSEDIAAN (14 KOLOM) -->
        <table class="table-items">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="7%">Tgl Faktur</th>
                    <th width="7%">Kode Barang</th>
                    <th width="8%">Kategori</th>
                    <th width="14%">Nama Barang</th>
                    <th width="8%">Uraian</th>
                    <th width="6%">Jenis</th>
                    <th width="5%">Stok Awal</th>
                    <th width="5%">Stok Keluar</th>
                    <th width="5%">Stok Sisa</th>
                    <th width="8%">Harga Satuan</th>
                    <th width="9%">Nilai Saldo</th>
                    <th width="9%">Nama Penerima</th>
                    <th width="11%">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $index => $transaction)
                    @php
                        $harga = $transaction->harga_satuan ?? (($transaction->harga_perolehan > 0) ? (float) $transaction->harga_perolehan : (float) ($transaction->product->purchase_price ?? 0));
                        $jumlah = (int) ($transaction->quantity ?? 0);
                        $stockSisa = (int) ($transaction->stock_after ?? 0);
                        
                        if ($transaction->type === 'keluar') {
                            $stockAwal = $stockSisa + $jumlah;
                        } else {
                            $stockAwal = ($stockSisa > $jumlah) ? ($stockSisa - $jumlah) : $jumlah;
                        }

                        $stockDipinjam = $transaction->type === 'keluar' ? $jumlah : 0;
                        $nilaiSaldo = $stockSisa * $harga;

                        $runningTotalAwal += $stockAwal;
                        $runningTotalDipinjam += $stockDipinjam;
                        $runningTotalSisa += $stockSisa;
                        $runningTotalSaldo += $nilaiSaldo;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-monospace">
                            {{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="text-center font-monospace">{{ $transaction->product->kode_barang_laporan ?? '-' }}</td>
                        <td class="text-center">{{ $transaction->product->category->name ?? '-' }}</td>
                        <td class="fw-semibold">{{ $transaction->product->name ?? '-' }}</td>
                        <td class="text-center">
                            @if ($transaction->type === 'masuk')
                                {{ $transaction->uraian ?? '-' }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold {{ $transaction->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ ucfirst($transaction->type) }}
                            @if ($transaction->type === 'keluar')
                                <br><small style="font-size: 6.8pt;" class="text-muted">({{ $transaction->is_subdit ? 'Subdit' : 'Individu' }})</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($stockAwal) }} Pcs</td>
                        <td class="text-center {{ $stockDipinjam > 0 ? 'text-danger fw-bold' : '' }}">
                            {{ $stockDipinjam > 0 ? number_format($stockDipinjam) . ' Pcs' : '-' }}
                        </td>
                        <td class="text-center fw-bold">{{ number_format($stockSisa) }} Pcs</td>
                        <td class="text-end font-monospace">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="text-end font-monospace fw-bold">Rp {{ number_format($nilaiSaldo, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if ($transaction->type === 'keluar')
                                {{ $transaction->recipient_name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="font-size: 7.2pt; line-height: 1.2;">
                            {{ $transaction->formatted_note ?? ($transaction->note ?? '-') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center py-4 text-muted">
                            Tidak ada data transaksi persediaan pada kriteria filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <th colspan="7" class="text-center py-2">TOTAL REKAPITULASI</th>
                    <th class="text-center py-2">{{ number_format($runningTotalAwal) }} Pcs</th>
                    <th class="text-center py-2 text-danger">{{ number_format($runningTotalDipinjam) }} Pcs</th>
                    <th class="text-center py-2">{{ number_format($runningTotalSisa) }} Pcs</th>
                    <th class="text-end py-2">-</th>
                    <th class="text-end py-2 text-primary">Rp {{ number_format($runningTotalSaldo, 0, ',', '.') }}</th>
                    <th class="text-center py-2">-</th>
                    <th class="text-center py-2">-</th>
                </tr>
            </tfoot>
        </table>

        <!-- SECTION TANDA TANGAN RESMI: BARIS 1 (MEMERIKSA & MENGETAHUI), BARIS 2 (MENYETUJUI) -->
        <div class="signature-section mt-4 pt-3" style="width: 100%; page-break-inside: avoid;">
            <!-- Baris 1: Memeriksa (Kiri) & Mengetahui (Kanan) -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                <div style="text-align: center; width: 38%;">
                    <p style="margin-bottom: 2px; font-size: 8.5pt;">&nbsp;</p>
                    <p style="margin-bottom: 2px; font-size: 9pt; font-weight: bold;">Memeriksa,</p>
                    <p style="margin-bottom: 50px; font-size: 8.5pt; color: #475569;">{{ $examinerTitle }}</p>
                    <p style="margin-bottom: 0; font-size: 9pt;">
                        <strong><u>{{ $examinerName }}</u></strong><br>
                        <span style="color: #475569; font-size: 8.5pt;">NIP. {{ $examinerNip }}</span>
                    </p>
                </div>
                <div style="text-align: center; width: 38%;">
                    <p style="margin-bottom: 2px; font-size: 8.5pt;">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p style="margin-bottom: 2px; font-size: 9pt; font-weight: bold;">Mengetahui,</p>
                    <p style="margin-bottom: 50px; font-size: 8.5pt; color: #475569;">{{ $mengetahuiTitle }}</p>
                    <p style="margin-bottom: 0; font-size: 9pt;">
                        <strong><u>{{ $mengetahuiName }}</u></strong><br>
                        <span style="color: #475569; font-size: 8.5pt;">NIP. {{ $mengetahuiNip }}</span>
                    </p>
                </div>
            </div>

            <!-- Baris 2: Menyetujui (Posisi Tengah di Bawah) -->
            <div style="display: flex; justify-content: center;">
                <div style="text-align: center; width: 38%;">
                    <p style="margin-bottom: 2px; font-size: 9pt; font-weight: bold;">Menyetujui,</p>
                    <p style="margin-bottom: 50px; font-size: 8.5pt; color: #475569;">{{ $kasubagTitle }}</p>
                    <p style="margin-bottom: 0; font-size: 9pt;">
                        <strong><u>{{ $kasubagName }}</u></strong><br>
                        <span style="color: #475569; font-size: 8.5pt;">NIP. {{ $kasubagNip }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Otomatis picu dialog print saat halaman selesai dimuat jika dibuka dari browser
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
