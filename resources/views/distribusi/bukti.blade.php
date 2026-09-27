<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bukti Serah Terima ATK - {{ $requestItem->request_code }}</title>
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
            padding: 6px 10px;
            font-size: 13px;
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
    <!-- Bar Navigasi Cetak (Tidak ikut dicetak) -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold">Dokumen Resmi Bukti Penyerahan ATK</span>
            <span class="badge bg-warning text-dark">{{ $requestItem->request_code }}</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                Cetak Dokumen (Print / PDF)
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
                    Sistem Informasi Inventori Persediaan & Pengelolaan Distribusi ATK
                </p>
            </div>
        </div>

        <!-- JUDUL SURAT -->
        <div class="text-center my-3">
            <h5 class="fw-bold text-uppercase text-decoration-underline mb-1" style="font-size: 15px;">
                BUKTI SERAH TERIMA BARANG ATK
            </h5>
            <span style="font-size: 13px;">Nomor Pengajuan: <strong>{{ $requestItem->request_code }}</strong></span>
        </div>

        <p style="font-size: 13px; line-height: 1.6;" class="mb-3">
            Pada hari ini, <strong>{{ \Carbon\Carbon::parse($requestItem->distributed_at ?? now())->translatedFormat('l, d F Y') }}</strong>, 
            telah diserahterimakan kebutuhan Alat Tulis Kantor (ATK) untuk kegiatan operasional kedinasan dengan rincian data sebagai berikut:
        </p>

        <!-- IDENTITAS PEMOHON -->
        <table class="table table-borderless table-sm mb-3" style="font-size: 13px; width: 100%;">
            <tr>
                <td width="25%"><strong>Nama Penerima / Pemohon</strong></td>
                <td width="2%">:</td>
                <td width="73%">{{ $requestItem->receiver_name ?? ($requestItem->user->name ?? '-') }}</td>
            </tr>
            <tr>
                <td><strong>Unit Kerja / Subdirektorat</strong></td>
                <td>:</td>
                <td>{{ $requestItem->user->position ?? ($requestItem->user->name ?? '-') }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Permintaan</strong></td>
                <td>:</td>
                <td>
                    {{ $requestItem->type === 'subdit' ? 'Kolektif Subdirektorat' : 'Individu (Pegawai)' }}
                </td>
            </tr>
            <tr>
                <td><strong>Keperluan / Catatan</strong></td>
                <td>:</td>
                <td>{{ $requestItem->notes ?? 'Kebutuhan operasional kantor' }}</td>
            </tr>
            <tr>
                <td><strong>Status Verifikasi</strong></td>
                <td>:</td>
                <td>
                    <span class="badge bg-success">Disetujui Kasubag TU & Telah Didistribusikan</span>
                </td>
            </tr>
        </table>

        <!-- TABEL RINCIAN BARANG -->
        <h6 class="fw-bold mb-1" style="font-size: 13px;">Rincian Barang yang Diserahkan:</h6>
        <table class="table-items">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Barang</th>
                    <th width="45%">Nama Barang / Spesifikasi</th>
                    <th width="15%">Jumlah Diminta</th>
                    <th width="20%">Jumlah Diserahkan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requestItem->details as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-monospace">{{ $detail->product->code ?? '-' }}</td>
                        <td>{{ $detail->product->name ?? 'Barang' }}</td>
                        <td class="text-center">{{ $detail->quantity }} Pcs</td>
                        <td class="text-center fw-bold">{{ $detail->quantity }} Pcs (Baik)</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($requestItem->distribution_notes)
            <div class="p-2 border bg-light my-2" style="font-size: 12px;">
                <strong>Catatan Khusus PIC Distribusi:</strong> {{ $requestItem->distribution_notes }}
            </div>
        @endif

        <p style="font-size: 13px; line-height: 1.5;" class="mt-3 mb-4">
            Demikian Bukti Serah Terima Barang ini dibuat dengan sebenarnya dalam rangkap untuk dipergunakan sebagai arsip administrasi persediaan ATK kantor.
        </p>

        <!-- KOLOM TANDA TANGAN (3 PIHAK) -->
        <div class="row text-center mt-4" style="font-size: 13px;">
            <div class="col-4">
                <p class="mb-1">Yang Menerima,</p>
                <p class="text-muted small mb-0">{{ $requestItem->type === 'subdit' ? 'Perwakilan Subdirektorat' : 'Pegawai Pemohon' }}</p>
                <div style="height: 60px;"></div>
                <p class="mb-0 fw-bold"><u>{{ $requestItem->receiver_name ?? ($requestItem->user->name ?? 'Penerima') }}</u></p>
                <small class="text-muted">{{ $requestItem->user->position ?? 'Pegawai' }}</small>
            </div>

            <div class="col-4">
                <p class="mb-1">Yang Menyerahkan,</p>
                <p class="text-muted small mb-0">PIC Distribusi & Gudang</p>
                <div style="height: 60px;"></div>
                <p class="mb-0 fw-bold"><u>{{ $requestItem->distributor->name ?? 'Fairuz' }}</u></p>
                <small class="text-muted">PIC Distribusi & Rekapitulasi</small>
            </div>

            <div class="col-4">
                <p class="mb-1">Mengetahui & Menyetujui,</p>
                <p class="text-muted small mb-0">Kasubag Tata Usaha</p>
                <div style="height: 60px;"></div>
                <p class="mb-0 fw-bold"><u>Utami M.</u></p>
                <small class="text-muted">NIP. 199812132025062010</small>
            </div>
        </div>
    </div>
</body>

</html>
