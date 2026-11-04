<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Persediaan Barang - {{ $month ? $monthName : 'Tahun ' . $year }}</title>
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
            color: #000000;
            font-size: 8.5pt;
        }

        .paper {
            background: #ffffff;
            width: 297mm;
            min-height: 210mm;
            padding: 10mm 14mm;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            page-break-after: always;
            break-after: page;
        }

        .paper:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        .header-section {
            width: 100%;
            margin-bottom: 8px;
        }

        .header-title {
            text-align: center;
            line-height: 1.25;
        }

        .header-title h4 {
            font-size: 11.5pt;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header-title h5 {
            font-size: 10pt;
            font-weight: 700;
            margin: 2px 0 0 0;
            text-transform: uppercase;
        }

        .header-meta {
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .header-meta td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* Tabel 12 Kolom Resmi */
        .table-stock-card {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 6px;
        }

        .table-stock-card th,
        .table-stock-card td {
            border: 1px solid #111111;
            padding: 3px 5px;
            vertical-align: middle;
        }

        .table-stock-card th {
            text-align: center;
            font-weight: 700;
            background-color: #ffffff;
            font-size: 8pt;
        }

        .sub-col-num th {
            font-size: 7.2pt;
            font-weight: normal;
            padding: 2px 4px;
            background-color: #ffffff;
        }

        /* Pola Arsir Abu-abu untuk Sel Kosong / Non-Aktif (Seperti pada gambar) */
        .cell-shaded {
            background-color: #64748b !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background: #1e293b;
            color: #ffffff;
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
                background: #ffffff !important;
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

            .signature-block {
                page-break-inside: avoid !important;
            }

            tr {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>

<body>
    <!-- Bar Navigasi Cetak Sticky -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold">Dokumen Resmi Kartu Persediaan Barang</span>
            <span class="badge bg-warning text-dark">
                {{ count($cards) > 1 ? count($cards) . ' Jenis Barang' : ($cards->first()->product->name ?? 'ATK') }}
            </span>
            <span class="badge bg-light text-dark">Periode: {{ $month ? $monthName : 'Semua Bulan' }} {{ $year }}</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1.5 text-dark">
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

    @foreach ($cards as $card)
        @php
            $product = $card->product;
            $saldoAwal = $card->saldoAwal;
            $transactions = $card->transactions;
        @endphp

        <!-- Halaman Kertas Resmi A4 Landscape -->
        <div class="paper">
            <!-- HEADER KARTU PERSEDIAAN BARANG (IDENTIK GAMBAR 1) -->
            <table class="header-section" style="border: none; width: 100%;">
                <tr>
                    <!-- KIRI: BULAN, UAKPB, KODE UAKPB -->
                    <td style="width: 38%; vertical-align: top; border: none;">
                        <table class="header-meta" style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 100px; border: none; font-weight: normal;">BULAN</td>
                                <td style="width: 12px; border: none;">:</td>
                                <td style="border: none; font-weight: normal;">{{ $month ? $monthName : 'Juli' }}</td>
                            </tr>
                            <tr>
                                <td style="border: none; font-weight: normal;">UAKPB</td>
                                <td style="border: none;">:</td>
                                <td style="border: none; font-weight: normal;">Satker Direktorat Jenderal Kawasan Permukiman</td>
                            </tr>
                            <tr>
                                <td style="border: none; font-weight: normal;">KODE UAKPB</td>
                                <td style="border: none;">:</td>
                                <td style="border: none; font-weight: normal; font-family: monospace;">146.03.01.994.694308.000</td>
                            </tr>
                        </table>
                    </td>

                    <!-- TENGAH: JUDUL KARTU PERSEDIAAN BARANG & TAHUN ANGGARAN -->
                    <td style="width: 32%; vertical-align: top; text-align: center; border: none;">
                        <div class="header-title">
                            <h4>KARTU PERSEDIAAN BARANG</h4>
                            <h5>TAHUN ANGGARAN {{ $year }}</h5>
                        </div>
                    </td>

                    <!-- KANAN: KODE BARANG, JENIS BARANG, SATUAN -->
                    <td style="width: 30%; vertical-align: top; border: none;">
                        <table class="header-meta" style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 110px; border: none; font-weight: normal;">KODE BARANG</td>
                                <td style="width: 12px; border: none;">:</td>
                                <td style="border: none; font-weight: normal; font-family: monospace;">
                                    {{ $product->kode_barang_laporan }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; font-weight: normal;">JENIS BARANG</td>
                                <td style="border: none;">:</td>
                                <td style="border: none; font-weight: normal;">{{ $product->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="border: none; font-weight: normal;">SATUAN</td>
                                <td style="border: none;">:</td>
                                <td style="border: none; font-weight: normal;">{{ $product->unit ?? 'pak' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- TABEL KARTU PERSEDIAAN 12 KOLOM SESUAI FORMAT GAMBAR 1 -->
            <table class="table-stock-card">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 35px;">NO</th>
                        <th rowspan="2" style="width: 130px;">NO. FAKTUR</th>
                        <th rowspan="2" style="width: 95px;">TANGGAL<br>FAKTUR</th>
                        <th rowspan="2" style="min-width: 160px;">URAIAN</th>
                        <th rowspan="2" style="width: 80px;">KONDISI<br>BARANG</th>
                        <th rowspan="2" style="width: 100px;">HARGA<br>PEROLEHAN</th>
                        <th rowspan="2" style="width: 55px;">MASUK</th>
                        <th rowspan="2" style="width: 55px;">KELUAR</th>
                        <th colspan="2">SALDO</th>
                        <th rowspan="2" style="width: 65px;">PARAF</th>
                        <th rowspan="2" style="width: 110px;">KETERANGAN</th>
                    </tr>
                    <tr>
                        <th style="width: 60px;">Jumlah</th>
                        <th style="width: 95px;">Nilai</th>
                    </tr>
                    <tr class="sub-col-num">
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
                        $rowCounter = 1;
                        $totalRenderedRows = 0;
                    @endphp

                    @foreach ($transactions as $t)
                        @php
                            $totalRenderedRows++;
                            $inQty = $t->type === 'masuk' ? (int)$t->quantity : 0;
                            $outQty = $t->type === 'keluar' ? (int)$t->quantity : 0;
                            $runningStock = max(0, $runningStock + ($inQty - $outQty));
                            $hargaSatuan = $t->harga_satuan;
                            $nilaiSaldo = $runningStock * $hargaSatuan;

                            // Cek nomor faktur: jangan tampilkan kode tiket REQ / REQ-BUY
                            $isReqCode = str_contains(strtoupper($t->no_faktur ?? ''), 'REQ');
                            $fakturDisplay = ($t->type === 'masuk' && !$isReqCode) ? ($t->no_faktur ?: '') : '';
                        @endphp
                        <tr>
                            <!-- 1. NO -->
                            <td style="text-align: center;">
                                {{ $t->type === 'masuk' ? $rowCounter++ : '' }}
                            </td>

                            <!-- 2. NO. FAKTUR -->
                            <td style="text-align: center; font-size: 7.5pt;">
                                {{ $fakturDisplay }}
                            </td>

                            <!-- 3. TANGGAL FAKTUR -->
                            <td style="text-align: center; font-size: 7.5pt;">
                                @if ($t->type === 'masuk' && $t->transaction_date)
                                    {{ \Carbon\Carbon::parse($t->transaction_date)->translatedFormat('d F Y') }}
                                @endif
                            </td>

                            <!-- 4. URAIAN -->
                            <td style="font-size: 7.8pt;">
                                @if ($t->type === 'masuk')
                                    {{ $t->uraian ?: ($t->note ?: 'Penerimaan Barang') }}
                                @else
                                    {{ $t->recipient_name }}
                                @endif
                            </td>

                            <!-- 5. KONDISI BARANG -->
                            <td style="text-align: center; font-size: 7.5pt;">
                                {{ $t->type === 'masuk' ? ($t->kondisi ?: 'Baru') : '' }}
                            </td>

                            <!-- 6. HARGA PEROLEHAN -->
                            <td style="text-align: right; font-family: monospace; font-size: 7.5pt;">
                                @if ($t->type === 'masuk')
                                    Rp. {{ number_format($hargaSatuan, 0, ',', '.') }}
                                @endif
                            </td>

                            <!-- 7. MASUK -->
                            <td style="text-align: center; font-weight: bold;">
                                {{ $inQty > 0 ? $inQty : '0' }}
                            </td>

                            <!-- 8. KELUAR -->
                            <td style="text-align: center; font-weight: bold;">
                                {{ $outQty > 0 ? $outQty : '0' }}
                            </td>

                            <!-- 9 = (7-8). SALDO JUMLAH -->
                            <td style="text-align: center; font-weight: bold;">
                                {{ $runningStock }}
                            </td>

                            <!-- 10 = (6x9). SALDO NILAI -->
                            @if ($t->type === 'masuk')
                                <td style="text-align: right; font-weight: bold; font-family: monospace; font-size: 7.5pt;">
                                    Rp. {{ number_format($nilaiSaldo, 0, ',', '.') }}
                                </td>
                            @else
                                {{-- Pada baris pengeluaran, kolom 10 diarsir abu-abu sesuai dokumen asli --}}
                                <td class="{{ ($totalRenderedRows % 2 === 0) ? 'cell-shaded' : '' }}" style="text-align: center;">
                                    &nbsp;
                                </td>
                            @endif

                            <!-- 11. PARAF (Kosongkan untuk paraf fisik tanda tangan basah saat dicetak) -->
                            <td style="text-align: center; vertical-align: middle; padding: 2px;">
                                &nbsp;
                            </td>

                            <!-- 12. KETERANGAN: Penyerahan "jenis barang" kepada "nama" -->
                            <td class="{{ ($t->type === 'keluar' && $totalRenderedRows % 2 !== 0) ? 'cell-shaded' : '' }}" style="font-size: 7.2pt;">
                                @if ($t->type === 'keluar')
                                    Penyerahan {{ $product->name }} kepada {{ $t->recipient_name }}
                                @else
                                    {{ $t->note ?: ('Penerimaan ' . $product->name) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- Baris Kisi Tambahan Agar Format Kertas Menyerupai Buku Kas Fisik Pemerintah (Minimal 16 Baris) --}}
                    @php
                        $minimumTotalRows = 16;
                        $remainingRows = max(0, $minimumTotalRows - $totalRenderedRows);
                    @endphp
                    @for ($i = 0; $i < $remainingRows; $i++)
                        <tr>
                            <td style="height: 22px;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- KOLOM TANDA TANGAN (MENGETAHUI & PEMBUAT DAFTAR TANPA GAMBAR TANDA TANGAN KARENA UNTUK DICETAK FISIK) -->
            <table class="signature-block" style="width: 100%; border: none; margin-top: 30px; font-size: 8.5pt;">
                <tr>
                    <!-- KIRI: Mengetahui, Kepala Subbagian Tata Usaha -->
                    <td style="width: 40%; text-align: center; vertical-align: top; border: none;">
                        <p style="margin: 0; font-size: 8.5pt;">Mengetahui,</p>
                        <p style="margin: 2px 0 0 0; font-size: 8.5pt;">Kepala Subbagian Tata Usaha,</p>
                        <div style="height: 55px;"></div>
                        <p style="margin: 0; font-size: 8.5pt; font-weight: bold; text-decoration: underline;">
                            {{ $kasubagName }}
                        </p>
                        <p style="margin: 2px 0 0 0; font-size: 8pt; color: #111;">
                            NIP {{ $kasubagNip }}
                        </p>
                    </td>

                    <td style="width: 20%; border: none;"></td>

                    <!-- KANAN: Pembuat Daftar (Petugas Gudang yang Memeriksa Laporan) -->
                    <td style="width: 40%; text-align: center; vertical-align: top; border: none;">
                        <p style="margin: 0; font-size: 8.5pt;">&nbsp;</p>
                        <p style="margin: 2px 0 0 0; font-size: 8.5pt;">Pembuat Daftar,</p>
                        <div style="height: 55px;"></div>
                        <p style="margin: 0; font-size: 8.5pt; font-weight: bold; text-decoration: underline;">
                            {{ $examinerName }}
                        </p>
                        <p style="margin: 2px 0 0 0; font-size: 8pt; color: #111;">
                            NIP {{ $examinerNip }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

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
