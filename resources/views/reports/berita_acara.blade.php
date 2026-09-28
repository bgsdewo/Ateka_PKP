<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Berita Acara Rekapitulasi Persediaan ATK - {{ $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css') }}">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Times New Roman', Times, serif;
            color: #000;
        }

        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 12px;
        }

        .table-items th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .no-print-bar {
            background: #144b59;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        @media print {
            body {
                background: #fff;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                padding: 10mm 15mm;
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</head>

<body>
    <!-- Bar Navigasi Cetak -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold">Dokumen Resmi Berita Acara Rekapitulasi Mutasi ATK</span>
            <span class="badge bg-warning text-dark">{{ $periodLabel }}</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                Cetak Dokumen Resmi (Print / PDF)
            </button>
            <button onclick="window.close(); if(history.length > 1) { history.back(); }" class="btn btn-outline-light btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <!-- Halaman Kertas Resmi -->
    <div class="paper">
        <!-- KOP SURAT -->
        <div class="kop-surat d-flex align-items-center">
            <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                style="width: 80px; height: 80px; object-fit: contain;" class="me-3">
            <div class="text-center flex-grow-1">
                <h5 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 16px;">
                    KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN
                </h5>
                <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 14px;">
                    REPUBLIK INDONESIA
                </h6>
                <p class="mb-0 small" style="font-size: 11px;">
                    Biro Umum dan Pengadaan Barang / Subbagian Tata Usaha
                </p>
                <p class="mb-0 small fst-italic" style="font-size: 10px;">
                    Subbagian Tata Usaha & Pengelolaan Arsip Administrasi Persediaan ATK
                </p>
            </div>
        </div>

        <!-- JUDUL SURAT -->
        <div class="text-center my-3">
            <h5 class="fw-bold text-uppercase text-decoration-underline mb-1" style="font-size: 14px;">
                BERITA ACARA REKAPITULASI MUTASI PERSEDIAAN BARANG ATK
            </h5>
            <span style="font-size: 12px;">Nomor: <strong>BA.{{ date('Ymd') }}/PKP.1/TU-ATK/{{ $year }}</strong></span><br>
            <span style="font-size: 12px;">Periode Rekapitulasi: <strong>{{ $periodLabel }}</strong></span>
        </div>

        <p style="font-size: 12px; line-height: 1.6;" class="mb-2">
            Pada hari ini, <strong>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</strong>, 
            telah dilakukan penatausahaan, verifikasi arsip, dan rekapitulasi data mutasi persediaan Alat Tulis Kantor (ATK) 
            berdasarkan dokumen transaksi penerimaan dan pengeluaran barang dengan ringkasan sebagai berikut:
        </p>

        <!-- KOTAK RINGKASAN REKAPITULASI -->
        <table class="table table-bordered table-sm my-3" style="font-size: 12px; width: 100%;">
            <tr class="table-light">
                <th width="35%">Indikator Persediaan</th>
                <th width="35%" class="text-center">Kuantitas Fisik</th>
                <th width="30%" class="text-end">Keterangan / Nilai</th>
            </tr>
            <tr>
                <td>Total Mutasi Barang Masuk (Pengadaan)</td>
                <td class="text-center fw-bold text-success">+{{ number_format($totalIn) }} Pcs</td>
                <td class="text-muted small">Penerimaan gudang</td>
            </tr>
            <tr>
                <td>Total Mutasi Barang Keluar (Distribusi Pemohon)</td>
                <td class="text-center fw-bold text-danger">-{{ number_format($totalOut) }} Pcs</td>
                <td class="text-muted small">Diserahterimakan ke Subdit/Pegawai</td>
            </tr>
            <tr>
                <td>Sisa Saldo Stok Akhir di Gudang</td>
                <td class="text-center fw-bold">{{ number_format($totalStock) }} Pcs</td>
                <td class="text-muted small">Kondisi baik & tersedia</td>
            </tr>
            <tr class="table-light fw-bold">
                <td>Total Estimasi Nilai Saldo Persediaan</td>
                <td class="text-center">{{ number_format($totalProducts) }} Jenis Barang</td>
                <td class="text-end text-dark">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- DAFTAR MUTASI TERAKHIR DALAM PERIODE -->
        <h6 class="fw-bold mb-1" style="font-size: 12px;">Rincian Mutasi Transaksi Terlampir ({{ $transactions->count() }} Transaksi):</h6>
        <table class="table-items">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="14%">Kode Barang</th>
                    <th width="30%">Nama Barang ATK</th>
                    <th width="10%">Jenis</th>
                    <th width="10%">Jumlah</th>
                    <th width="20%">Penerima / Uraian</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions->take(20) as $index => $t)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center font-monospace">{{ $t->product->code ?? '-' }}</td>
                        <td>{{ $t->product->name ?? '-' }}</td>
                        <td class="text-center fw-bold {{ $t->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                            {{ ucfirst($t->type) }}
                        </td>
                        <td class="text-center">{{ number_format($t->quantity) }} Pcs</td>
                        <td>{{ $t->uraian ?? ($t->user->name ?? '-') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-2 text-muted">Tidak ada mutasi pada periode ini.</td>
                    </tr>
                @endforelse
                @if ($transactions->count() > 20)
                    <tr>
                        <td colspan="7" class="text-center fst-italic py-1 small">
                            ... dan {{ $transactions->count() - 20 }} transaksi lainnya tercatat dalam buku besar arsip digital ...
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <p style="font-size: 12px; line-height: 1.5;" class="mt-3 mb-4">
            Demikian Berita Acara Rekapitulasi Mutasi Persediaan ATK ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagai 
            arsip pelaporan resmi pertanggungjawaban penatausahaan barang inventaris kantor.
        </p>

        <!-- DUAL TANDA TANGAN: ARSIPARIS & KASUBAG TU -->
        <div class="row text-center mt-5" style="font-size: 12px;">
            <div class="col-6">
                <p class="mb-1">Disiapkan & Direkapitulasi oleh,</p>
                <p class="text-muted small mb-0"><strong>Arsiparis (Pelaporan & Administrasi)</strong></p>
                <div style="height: 65px;"></div>
                <p class="mb-0 fw-bold"><u>Devi Ismiyanti</u></p>
                <small class="text-muted">NIP. 199508142022032005</small>
            </div>

            <div class="col-6">
                <p class="mb-1">Mengetahui & Mengesahkan,</p>
                <p class="text-muted small mb-0"><strong>Kasubag Tata Usaha (Pimpinan)</strong></p>
                <div style="height: 65px;"></div>
                <p class="mb-0 fw-bold"><u>Utami M.</u></p>
                <small class="text-muted">NIP. 199812132025062010</small>
            </div>
        </div>
    </div>
</body>

</html>
