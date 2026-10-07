<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Kebutuhan ATK dan Supplais Komputer - {{ $monthPeriodLabel ?? $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css') }}">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            color: #000;
        }

        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .kop-surat {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }

        .table-items th,
        .table-items td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .table-items th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .paper {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 10mm 15mm;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>

<body>
    {{-- Non-printable Bar --}}
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold">Dokumen Resmi Usulan Kebutuhan ATK & Komputer</span>
            <span class="badge bg-light text-dark">Kementerian PKP RI</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                Cetak Dokumen
            </button>
            <a href="{{ route('rekapitulasi-permintaan.index') }}" class="btn btn-outline-light btn-sm">
                Kembali
            </a>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <div class="paper">
        {{-- KOP SURAT RESMI SESUAI GAMBAR 1 --}}
        <div class="kop-surat">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 15%; text-align: center; vertical-align: middle; border: none; padding-right: 12px;">
                        <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenPKP"
                            style="width: 82px; height: 82px; object-fit: contain;">
                    </td>
                    <td style="text-align: center; vertical-align: middle; border: none; line-height: 1.25;">
                        <h4 style="margin: 0; font-family: Arial, sans-serif; font-weight: bold; font-size: 11pt; letter-spacing: 0.3px; color: #0b3c5d;">
                            KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN <span style="color: #c59b27;">REPUBLIK INDONESIA</span>
                        </h4>
                        <h3 style="margin: 3px 0 2px 0; font-family: Arial, sans-serif; font-weight: bold; font-size: 13pt; color: #000; letter-spacing: 0.5px;">
                            DIREKTORAT JENDERAL KAWASAN PERMUKIMAN
                        </h3>
                        <h4 style="margin: 0; font-family: Arial, sans-serif; font-weight: bold; font-size: 10.5pt; color: #000;">
                            DIREKTORAT BINA TEKNIK PERUMAHAN DAN KAWASAN PERMUKIMAN
                        </h4>
                        <p style="margin: 4px 0 0 0; font-size: 8pt; color: #333; line-height: 1.2;">
                            Menara BRIPens, Jalan Gatot Subroto, Nomor Kaveling 9-11, Karet Semanggi, Setiabudi, Jakarta Selatan 12930
                        </p>
                    </td>
                    <td style="width: 8%; border: none;"></td>
                </tr>
            </table>
        </div>

        {{-- JUDUL DOKUMEN SESUAI GAMBAR 1 --}}
        <div class="text-center" style="margin-bottom: 18px;">
            <h4 style="margin: 0; font-weight: bold; font-size: 12pt; text-transform: uppercase;">
                DAFTAR KEBUTUHAN ALAT TULIS KANTOR DAN SUPPLAIS KOMPUTER
            </h4>
            <h5 style="margin: 3px 0 0 0; font-weight: bold; font-size: 11pt; text-transform: uppercase;">
                DIREKTORAT BINA TEKNIK PERUMAHAN DAN KAWASAN PERMUKIMAN
            </h5>
        </div>

        {{-- INFORMASI PENGUSUL SESUAI GAMBAR 1 + NO. TIKET --}}
        <table style="width: 100%; border: none; margin-bottom: 15px; font-size: 10pt; line-height: 1.5;">
            <tr>
                <td style="width: 220px; font-weight: 500; border: none; padding: 2px 0;">Subdirektorat/Subbagian</td>
                <td style="width: 18px; border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $subditLabel ?? 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 500; border: none; padding: 2px 0;">Bulan</td>
                <td style="border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $monthPeriodLabel ?? ($periodLabel ?? '-') }}</td>
            </tr>
            <tr>
                <td style="font-weight: 500; border: none; padding: 2px 0;">No. Tiket</td>
                <td style="border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">
                    <strong style="font-family: monospace; font-size: 10.5pt;">{{ $ticketNumbersLabel ?? '-' }}</strong>
                </td>
            </tr>
        </table>

        {{-- TABEL DAFTAR BARANG SESUAI GAMBAR 1 + NO. TIKET --}}
        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 20%;">NO. TIKET</th>
                    <th style="text-align: left; padding-left: 10px;">NAMA BARANG</th>
                    <th style="width: 14%;">QTY</th>
                    <th style="width: 30%; text-align: left; padding-left: 10px;">NAMA PENGGUNA BARANG</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items ?? [] as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace; font-size: 9pt; font-weight: bold;">
                            {{ $item->request_code }}
                        </td>
                        <td style="text-align: left; padding-left: 10px;">
                            <span class="fw-bold">{{ $item->item_name }}</span>
                            @if (!empty($item->specification))
                                <br><small class="text-muted fst-italic" style="font-size: 8.5pt;">Spesifikasi: {{ $item->specification }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $item->quantity }} {{ strtolower($item->unit) }}
                        </td>
                        <td style="text-align: left; padding-left: 10px; font-weight: bold;">
                            {{ $item->user_or_spec }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            Tidak ada data kebutuhan barang pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN YANG MENGAJUKAN SESUAI GAMBAR 1 --}}
        <div style="margin-top: 35px; page-break-inside: avoid;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 55%; border: none;"></td>
                    <td style="width: 45%; border: none; text-align: center;">
                        <p style="margin: 0 0 3px 0; font-size: 10.5pt;">
                            Jakarta, {{ $signingDate ?? now()->translatedFormat('d F Y') }}
                        </p>
                        <p style="margin: 0 0 70px 0; font-size: 10.5pt;">
                            Yang Mengajukan,
                        </p>
                        <p style="margin: 0; font-size: 10.5pt; font-weight: bold; text-decoration: underline;">
                            {{ $signerName ?? 'Pengaju Permintaan' }}
                        </p>
                        <p style="margin: 3px 0 0 0; font-size: 9.5pt;">
                            NIP. {{ (!empty($signerNip) && $signerNip !== '-') ? $signerNip : '-' }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
