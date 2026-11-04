<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Kebutuhan ATK dan Supplais Komputer - {{ $monthPeriodLabel ?? ($periodLabel ?? 'Kementerian PKP RI') }}</title>
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
            box-sizing: border-box;
            position: relative;
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
                min-height: 297mm;
                padding: 10mm 15mm;
                page-break-after: always;
                break-after: page;
            }

            .paper:last-child {
                page-break-after: auto;
                break-after: auto;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>

<body>
    @php
        $requesterUser = isset($requestItem) ? $requestItem->user : null;
        $isPicOrSubdit = isset($requestItem)
            ? (($requestItem->type === 'subdit') || ($requesterUser && $requesterUser->isPicDistribusi()))
            : true;
    @endphp

    {{-- Non-printable Bar --}}
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2">
            @if (isset($requestItem))
                <span class="fw-bold">Dokumen Bundel Lengkap ATK</span>
                <span class="badge bg-warning text-dark">{{ $requestItem->request_code }}</span>
                @if ($requestItem->signed_proof)
                    <span class="text-white small ms-1 px-2 py-0.5 rounded" style="background: rgba(40, 167, 69, 0.85); font-size: 11px;">
                        &#10003; Berkas Terunggah
                    </span>
                @endif
            @else
                <span class="fw-bold">Dokumen Resmi Usulan Kebutuhan ATK & Komputer</span>
                <span class="badge bg-light text-dark">Kementerian PKP RI</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold">
                Cetak Dokumen (Print / PDF)
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
                <td style="font-weight: 500; border: none; padding: 2px 0;">Nama</td>
                <td style="border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $signerName ?? ($requesterUser ? ($requesterUser->name_with_title ?: $requesterUser->name) : 'Pengaju Permintaan') }}</td>
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
                    @if (isset($requestItem))
                        <th style="width: 15%;">KODE BARANG</th>
                    @else
                        <th style="width: 20%;">NO. TIKET</th>
                    @endif
                    <th style="text-align: left; padding-left: 10px;">NAMA BARANG</th>
                    <th style="width: 14%;">QTY</th>
                    @if (!isset($requestItem) || $isPicOrSubdit)
                        <th style="width: 25%; text-align: left; padding-left: 10px;">NAMA PENGGUNA BARANG</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($items ?? [] as $index => $item)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        @if (isset($requestItem))
                            <td style="text-align: center; font-family: monospace; font-size: 9pt; font-weight: bold;">
                                {{ $item->product_code ?? ($item->product->kode_barang_laporan ?? ($item->code ?? '-')) }}
                            </td>
                        @else
                            <td style="text-align: center; font-family: monospace; font-size: 9pt; font-weight: bold;">
                                {{ $item->request_code }}
                            </td>
                        @endif
                        <td style="text-align: left; padding-left: 10px;">
                            <span class="fw-bold">{{ $item->item_name }}</span>
                            @if (!empty($item->specification))
                                <br><small class="text-muted fst-italic" style="font-size: 8.5pt;">Spesifikasi: {{ $item->specification }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $item->quantity }} {{ strtolower($item->unit) }}
                        </td>
                        @if (!isset($requestItem) || $isPicOrSubdit)
                            <td style="text-align: left; padding-left: 10px; font-weight: bold;">
                                {{ $item->user_or_spec }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (!isset($requestItem) || $isPicOrSubdit) ? 5 : 4 }}" style="text-align: center; padding: 20px;">
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

    @if (isset($requestItem))
        @php
            $requesterUser = $requestItem->user;
            $isPicOrSubdit = ($requestItem->type === 'subdit') || ($requesterUser && $requesterUser->isPicDistribusi());

            // Kasubbag TU resmi
            $kasubagUser = $requestItem->verifier ?? \App\Models\User::whereIn('role', ['kasubag_tu', 'admin'])->first();
            $namaKasubag = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
            $nipKasubag = $kasubagUser ? $kasubagUser->nip : null;

            // Petugas Gudang
            $distributorUser = $requestItem->distributor;
            if ($distributorUser && ($distributorUser->name === 'tes' || $distributorUser->isKasubagTU())) {
                $distributorUser = null;
            }

            if (!$distributorUser) {
                if (auth()->check() && auth()->user()->isPegawaiGudang() && auth()->user()->isDistributor() && auth()->user()->name !== 'tes') {
                    $distributorUser = auth()->user();
                } else {
                    $distributorUser = \App\Models\User::where('role', 'pegawai_gudang')
                                    ->where('is_distributor', true)
                                    ->where('name', '!=', 'tes')
                                    ->latest('id')
                                    ->first()
                                    ?? \App\Models\User::where('role', 'pegawai_gudang')
                                    ->where('name', '!=', 'tes')
                                    ->latest('id')
                                    ->first()
                                    ?? \App\Models\User::where('role', 'pegawai_gudang')
                                    ->latest('id')
                                    ->first();
                }
            }

            $namaDistributor = $distributorUser ? ($distributorUser->name_with_title ?: $distributorUser->name) : 'Petugas Gudang';
            $nipDistributor = $distributorUser ? $distributorUser->nip : null;
            $jabatanDistributor = $distributorUser ? ($distributorUser->position ?: 'Petugas Distribusi Gudang ATK') : 'Petugas Distribusi Gudang ATK';

            $userPos = strtolower($requesterUser->position ?? '');
            $userSubdit = strtolower($requesterUser->subdit_name ?? '');
            $userText = $userPos . ' ' . $userSubdit;

            // Subdit Label untuk PIC
            $detectedSubdit = null;
            if ($requesterUser && !empty($requesterUser->subdit_name)) {
                $detectedSubdit = $requesterUser->subdit_name;
            } elseif (str_contains($userText, 'sdk') || str_contains($userText, 'sumber daya')) {
                $detectedSubdit = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
            } elseif (str_contains($userText, 'pjf') || str_contains($userText, 'fungsional')) {
                $detectedSubdit = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
            } elseif (str_contains($userText, 'kbr') || str_contains($userText, 'kendala')) {
                $detectedSubdit = 'Subdirektorat Kendala Bangunan Rumah';
            } elseif (str_contains($userText, 'tata usaha') || str_contains($userText, 'tu')) {
                $detectedSubdit = 'Subbagian Tata Usaha';
            } else {
                $detectedSubdit = $requesterUser->position ?? ($isPicOrSubdit ? 'Subdirektorat' : 'Pegawai Perorangan');
            }

            // Label Ringkas Subdirektorat untuk Akun PIC (Format ringkas: Perwakilan Subdirektorat SDK / PJF / KBR)
            if (str_contains($userText, 'sdk') || str_contains($userText, 'sumber daya')) {
                $shortSubdit = 'Subdirektorat SDK';
            } elseif (str_contains($userText, 'pjf') || str_contains($userText, 'fungsional')) {
                $shortSubdit = 'Subdirektorat PJF';
            } elseif (str_contains($userText, 'kbr') || str_contains($userText, 'kendala')) {
                $shortSubdit = 'Subdirektorat KBR';
            } elseif (str_contains($userText, 'tata usaha') || str_contains($userText, 'tu')) {
                $shortSubdit = 'Subbagian TU';
            } else {
                $candidate = preg_replace('/^pic\s+distribusi\s+/i', '', trim($requesterUser->position ?? ($requesterUser->subdit_name ?? 'Subdirektorat')));
                if (stripos($candidate, 'subdirektorat') === 0 || stripos($candidate, 'subbagian') === 0) {
                    $shortSubdit = $candidate;
                } else {
                    $shortSubdit = 'Subdirektorat ' . $candidate;
                }
            }
            $permintaanText = 'Perwakilan ' . $shortSubdit;
        @endphp

        <!-- ========================================================================= -->
        <!-- LEMBAR 2: BUKTI SERAH TERIMA BARANG ATK (GAMBAR 2)                        -->
        <!-- ========================================================================= -->
        <div class="paper">
            <!-- KOP SURAT BIRO UMUM / SUBBAG TU SESUAI GAMBAR 2 -->
            <div style="border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px;" class="d-flex align-items-center">
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
                        Sistem Informasi Inventori Persediaan &amp; Pengelolaan Distribusi ATK
                    </p>
                </div>
            </div>

            <!-- JUDUL SURAT SESUAI GAMBAR 2 -->
            <div class="text-center my-3">
                <h5 class="fw-bold text-uppercase text-decoration-underline mb-1" style="font-size: 15px;">
                    BUKTI SERAH TERIMA BARANG ATK
                </h5>
                <span style="font-size: 13px;">Nomor Pengajuan: <strong>{{ $requestItem->request_code }}</strong></span>
            </div>

            <p style="font-size: 13px; line-height: 1.6;" class="mb-3">
                Pada hari ini, <strong>{{ \Carbon\Carbon::parse($requestItem->distributed_at ?? now())->locale('id')->translatedFormat('l, d F Y') }}</strong>, 
                telah diserahterimakan kebutuhan Alat Tulis Kantor (ATK) untuk kegiatan operasional kedinasan dengan rincian data sebagai berikut:
            </p>

            <!-- IDENTITAS PEMOHON SESUAI GAMBAR 2 -->
            <table class="table table-borderless table-sm mb-3" style="font-size: 13px; width: 100%; table-layout: fixed;">
                <colgroup>
                    <col style="width: 210px;">
                    <col style="width: 15px;">
                    <col style="width: auto;">
                </colgroup>
                <tbody>
                    @if ($isPicOrSubdit)
                        <tr>
                            <td style="vertical-align: top; padding: 2px 0;"><strong>Permintaan</strong></td>
                            <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                            <td style="vertical-align: top; word-break: break-word; overflow-wrap: break-word; padding: 2px 0;">
                                <strong>{{ $permintaanText }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;"><strong>{{ $isPicOrSubdit ? 'Nama Penerima (Perwakilan)' : 'Nama Penerima / Pemohon' }}</strong></td>
                        <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                        <td style="vertical-align: top; word-break: break-word; overflow-wrap: break-word; padding: 2px 0;">
                            <strong>{{ $requestItem->receiver_name ?? ($requesterUser ? ($requesterUser->name_with_title ?: $requesterUser->name) : '-') }}</strong>
                            @if ($isPicOrSubdit)
                                <span class="text-muted ms-1">(Perwakilan Subdirektorat)</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;"><strong>Jenis Permintaan</strong></td>
                        <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                        <td style="vertical-align: top; word-break: break-word; overflow-wrap: break-word; padding: 2px 0;">
                            {{ $isPicOrSubdit ? 'Permintaan Subdirektorat (Perwakilan PIC)' : 'Individu (Pegawai)' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;"><strong>Keperluan / Catatan</strong></td>
                        <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                        <td style="vertical-align: top; word-break: break-word; overflow-wrap: break-word; padding: 2px 0;">{{ $requestItem->notes ?? 'Kebutuhan operasional kantor' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; padding: 2px 0;"><strong>Status Verifikasi</strong></td>
                        <td style="vertical-align: top; text-align: center; padding: 2px 0;">:</td>
                        <td style="vertical-align: top; word-break: break-word; overflow-wrap: break-word; padding: 2px 0;">
                            @if ($requestItem->status === 'distributed')
                                <span class="fw-bold text-dark">Disetujui Kasubbag TU &amp; Telah Didistribusikan</span>
                            @else
                                <span class="fw-bold text-dark">Disetujui Kasubbag TU (Siap Diserahkan)</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- TABEL RINCIAN BARANG SESUAI GAMBAR 2 -->
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
                            <td class="text-center font-monospace">{{ $detail->product->kode_barang_laporan ?? '-' }}</td>
                            <td>
                                <strong>{{ $detail->item_name }}</strong>
                                @if ($detail->specification)
                                    <br><small class="text-muted fst-italic">Spesifikasi: {{ $detail->specification }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $detail->quantity }} {{ $detail->item_unit }}</td>
                            <td class="text-center fw-bold">{{ $detail->effective_quantity }} {{ $detail->item_unit }} (Baik)</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>



            <p style="font-size: 13px; line-height: 1.5;" class="mt-3 mb-4">
                Demikian Bukti Serah Terima Barang ini dibuat dengan sebenarnya dalam rangkap untuk dipergunakan sebagai arsip administrasi persediaan ATK kantor.
            </p>

            <!-- KOLOM TANDA TANGAN (BARIS 1: YANG MENYERAHKAN & YANG MENERIMA, BARIS 2: MENGETAHUI & MENYETUJUI) -->
            <div style="margin-top: 25px; page-break-inside: avoid; font-size: 13px;">
                <!-- Baris 1: Yang Menyerahkan & Yang Menerima -->
                <table style="width: 100%; border: none; margin-bottom: 25px; text-align: center;">
                    <tr>
                        <td style="width: 45%; border: none; vertical-align: top; text-align: center;">
                            <p class="mb-1" style="margin-bottom: 4px;">Yang Menyerahkan,</p>
                            <p class="text-muted small mb-0" style="margin-bottom: 0;">Petugas Distribusi Gudang</p>
                            <div style="height: 55px;"></div>
                            <p class="mb-0 fw-bold" style="margin-bottom: 0;"><u>{{ $namaDistributor }}</u></p>
                            <small class="text-muted d-block">{{ $jabatanDistributor }}</small>
                            @if(!empty($nipDistributor))
                                <small class="text-muted d-block">NIP. {{ $nipDistributor }}</small>
                            @endif
                        </td>
                        <td style="width: 10%; border: none;"></td>
                        <td style="width: 45%; border: none; vertical-align: top; text-align: center;">
                            <p class="mb-1" style="margin-bottom: 4px;">Yang Menerima,</p>
                            <p class="text-muted small mb-0" style="margin-bottom: 0;">{{ $isPicOrSubdit ? 'Perwakilan Subdirektorat' : 'Pegawai Pemohon' }}</p>
                            <div style="height: 55px;"></div>
                            <p class="mb-0 fw-bold" style="margin-bottom: 0;"><u>{{ $requestItem->receiver_name ?? ($requesterUser->name_with_title ?? ($requesterUser->name ?? 'Penerima')) }}</u></p>
                            <small class="text-muted d-block">{{ $isPicOrSubdit ? ($requesterUser->position ?? 'Perwakilan Subdirektorat') : ($requesterUser->position ?? 'Pegawai') }}</small>
                            @if(!empty($requesterUser->nip))
                                <small class="text-muted d-block">NIP. {{ $requesterUser->nip }}</small>
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Baris 2: Mengetahui & Menyetujui (Posisi Tengah di Bawah) -->
                <table style="width: 100%; border: none; text-align: center;">
                    <tr>
                        <td style="width: 25%; border: none;"></td>
                        <td style="width: 50%; border: none; vertical-align: top; text-align: center;">
                            <p class="mb-1" style="margin-bottom: 4px;">Mengetahui &amp; Menyetujui,</p>
                            <p class="text-muted small mb-0" style="margin-bottom: 0;">Kasubbag Tata Usaha</p>
                            <div style="height: 55px;"></div>
                            <p class="mb-0 fw-bold" style="margin-bottom: 0;"><u>{{ $namaKasubag }}</u></p>
                            <small class="text-muted d-block">Kasubbag Tata Usaha</small>
                            @if(!empty($nipKasubag))
                                <small class="text-muted d-block">NIP. {{ $nipKasubag }}</small>
                            @endif
                        </td>
                        <td style="width: 25%; border: none;"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- LEMBAR 3: LAMPIRAN DOKUMENTASI FISIK & BERKAS TANDA TANGAN                -->
        <!-- ========================================================================= -->
        <div class="paper">
            <div style="border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px;" class="d-flex align-items-center">
                <img src="{{ asset('hope-ui/assets/images/auth/kemenkp.jpeg') }}" alt="Logo KemenKP"
                    style="width: 70px; height: 70px; object-fit: contain;" class="me-3">
                <div class="text-center flex-grow-1">
                    <h5 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 14px;">
                        KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN REPUBLIK INDONESIA
                    </h5>
                    <h6 class="mb-0 fw-bold text-uppercase" style="font-size: 12.5px;">
                        LAMPIRAN RESMI DOKUMENTASI BUKTI SERAH TERIMA ATK
                    </h6>
                    <p class="mb-0 small" style="font-size: 10.5px;">
                        Nomor Pengajuan: <strong class="font-monospace">{{ $requestItem->request_code }}</strong>
                    </p>
                </div>
            </div>

            <!-- 1. FOTO DOKUMENTASI FISIK DARI PETUGAS GUDANG -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase text-dark" style="font-size: 12.5px;">
                        Foto Dokumentasi Fisik Penyerahan Barang
                    </h6>
                    <span class="text-muted small" style="font-size: 11px;">
                        Tiket: <strong>{{ $requestItem->request_code }}</strong>
                    </span>
                </div>

                @if ($requestItem->distribution_proof)
                    <div class="text-center p-3 border rounded bg-light" style="background-color: #fafbfc !important;">
                        <a href="{{ $requestItem->distribution_proof_url }}" target="_blank" title="Klik untuk memperbesar foto">
                            <img src="{{ $requestItem->distribution_proof_url }}" 
                                 alt="Foto Dokumentasi Serah Terima {{ $requestItem->request_code }}" 
                                 class="img-fluid border shadow-sm rounded" 
                                 style="max-height: 520px; max-width: 100%; object-fit: contain; background: #fff;">
                        </a>
                        <div class="mt-2 text-muted" style="font-size: 11px; line-height: 1.4;">
                            <span class="fst-italic">
                                Foto fisik serah terima barang persediaan ATK tiket <strong>{{ $requestItem->request_code }}</strong>
                                @if($requestItem->receiver_name)
                                    diserahkan kepada <strong>{{ $requestItem->receiver_name }}</strong>
                                @endif
                                @if($namaDistributor)
                                    oleh <strong>{{ $namaDistributor }}</strong>
                                @endif
                                @if($requestItem->distributed_at)
                                    pada <strong>{{ $requestItem->distributed_at->locale('id')->translatedFormat('l, d F Y (H:i') }} WIB)</strong>
                                @endif.
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center p-4 border border-dashed rounded bg-light text-muted" style="font-size: 12px;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 text-muted">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <p class="mb-0 fw-semibold">Foto dokumentasi fisik serah terima belum diunggah oleh petugas gudang.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</body>

</html>
