<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dokumen Lengkap ATK - {{ $requestItem->request_code }}</title>
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css') }}">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            color: #000;
        }

        .sheet-page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 25px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
        }

        .sheet-badge {
            background: #144b59;
            color: #fff;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            margin-bottom: 12px;
            display: inline-block;
        }

        .kop-surat-usulan {
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }

        .kop-surat-bast {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
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
            html,
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet-page {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                max-width: 100% !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .sheet-page:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .sheet-page img {
                max-height: 480px !important;
            }

            .sheet-badge {
                display: none !important;
            }

            .badge {
                background-color: transparent !important;
                color: #000 !important;
                border: none !important;
                padding: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>

<body>
    @php
        $requesterUser = $requestItem->user;
        $isPicOrSubdit = ($requestItem->type === 'subdit') || ($requesterUser && $requesterUser->isPicDistribusi());

        // Signer Sheet 1 (Usulan)
        $signerName = $requesterUser ? ($requesterUser->name_with_title ?: $requesterUser->name) : 'Pengaju Permintaan';
        $signerNip = ($requesterUser && !empty($requesterUser->nip) && $requesterUser->nip !== '-') ? $requesterUser->nip : '-';
        $signingDate = ($requestItem->created_at ?? now())->locale('id')->translatedFormat('d F Y');
        $monthPeriodLabel = ($requestItem->created_at ?? now())->locale('id')->translatedFormat('F Y');

        // Subdit Label Sheet 1
        if ($requesterUser && !empty($requesterUser->subdit_name)) {
            $subditLabel = $requesterUser->subdit_name;
        } elseif ($requesterUser && ($requesterUser->isSdk() || str_contains(strtolower($requesterUser->position ?? ''), 'sdk'))) {
            $subditLabel = 'Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman';
        } elseif ($requesterUser && ($requesterUser->isPjf() || str_contains(strtolower($requesterUser->position ?? ''), 'pjf'))) {
            $subditLabel = 'Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi';
        } elseif ($requesterUser && ($requesterUser->isKbr() || str_contains(strtolower($requesterUser->position ?? ''), 'kbr'))) {
            $subditLabel = 'Subdirektorat Kendala Bangunan Rumah';
        } elseif ($requesterUser && ($requesterUser->isTu() || str_contains(strtolower($requesterUser->position ?? ''), 'tata usaha'))) {
            $subditLabel = 'Subbagian Tata Usaha';
        } else {
            $subditLabel = $requesterUser->position ?? ($isPicOrSubdit ? 'Subdirektorat' : 'Pegawai Perorangan');
        }

        // Team Name Sheet 1 (TIM SDK, TIM PJF, dsb)
        if (!$isPicOrSubdit) {
            $teamName = $requesterUser ? ($requesterUser->name_with_title ?: $requesterUser->name) : 'Pegawai';
        } elseif (str_contains(strtolower($subditLabel), 'sumber daya') || str_contains(strtolower($subditLabel), 'sdk')) {
            $teamName = 'TIM SDK';
        } elseif (str_contains(strtolower($subditLabel), 'fungsional') || str_contains(strtolower($subditLabel), 'pjf')) {
            $teamName = 'TIM PJF';
        } elseif (str_contains(strtolower($subditLabel), 'kendala') || str_contains(strtolower($subditLabel), 'kbr')) {
            $teamName = 'TIM KBR';
        } elseif (str_contains(strtolower($subditLabel), 'tata usaha') || str_contains(strtolower($subditLabel), 'tu')) {
            $teamName = 'TIM TU';
        } else {
            $teamName = 'TIM ' . strtoupper($requesterUser->position ?? 'SUBDIT');
        }

        // Kasubbag TU resmi
        $kasubagUser = $requestItem->verifier ?? \App\Models\User::whereIn('role', ['kasubag_tu', 'admin'])->first();
        $namaKasubag = $kasubagUser ? ($kasubagUser->name_with_title ?: $kasubagUser->name) : 'Utami M.';
        $nipKasubag = $kasubagUser ? $kasubagUser->nip : null;

        // Data Petugas Gudang / Orang yang Mendistribusikan Barang
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
    @endphp

    <!-- Bar Navigasi Cetak (Tidak ikut dicetak) -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold">Dokumen Bundel Lengkap ATK</span>
            <span class="badge bg-warning text-dark font-monospace">{{ $requestItem->request_code }}</span>
            @if ($requestItem->signed_proof)
                <span class="text-white small ms-1 px-2 py-0.5 rounded" style="background: rgba(40, 167, 69, 0.85); font-size: 11px;">
                    &#10003; Berkas Terunggah
                </span>
            @endif
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold shadow-sm">
                Cetak Dokumen (Print / PDF)
            </button>
            <button onclick="window.close(); if(history.length > 1) { history.back(); }" class="btn btn-outline-light btn-sm">
                Tutup
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 1: SURAT USULAN PERMINTAAN ATK & SUPPLAIS KOMPUTER (GAMBAR 1)     -->
    <!-- ========================================================================= -->
    <div id="lembar-1" class="sheet-page">

        {{-- KOP SURAT DIREKTORAT BINA TEKNIK SESUAI GAMBAR 1 --}}
        <div class="kop-surat-usulan">
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
                <td style="border: none; padding: 2px 0;">{{ $subditLabel }}</td>
            </tr>
            <tr>
                <td style="font-weight: 500; border: none; padding: 2px 0;">Bulan</td>
                <td style="border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">{{ $monthPeriodLabel }}</td>
            </tr>
            <tr>
                <td style="font-weight: 500; border: none; padding: 2px 0;">No. Tiket</td>
                <td style="border: none; padding: 2px 0;">:</td>
                <td style="border: none; padding: 2px 0;">
                    <strong style="font-family: monospace; font-size: 10.5pt;">{{ $requestItem->request_code }}</strong>
                </td>
            </tr>
        </table>

        {{-- TABEL DAFTAR BARANG SESUAI GAMBAR 1 --}}
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
                @foreach ($requestItem->details as $index => $detail)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center; font-family: monospace; font-size: 9pt; font-weight: bold;">
                            {{ $requestItem->request_code }}
                        </td>
                        <td style="text-align: left; padding-left: 10px;">
                            <span class="fw-bold">{{ $detail->item_name }}</span>
                            @if (!empty($detail->specification))
                                <br><small class="text-muted fst-italic" style="font-size: 8.5pt;">Spesifikasi: {{ $detail->specification }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            {{ $detail->quantity }} {{ strtolower($detail->item_unit) }}
                        </td>
                        <td style="text-align: left; padding-left: 10px; font-weight: bold;">
                            {{ $teamName }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TANDA TANGAN YANG MENGAJUKAN SESUAI GAMBAR 1 --}}
        <div style="margin-top: 35px; page-break-inside: avoid;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 55%; border: none;"></td>
                    <td style="width: 45%; border: none; text-align: center;">
                        <p style="margin: 0 0 3px 0; font-size: 10.5pt;">
                            Jakarta, {{ $signingDate }}
                        </p>
                        <p style="margin: 0 0 70px 0; font-size: 10.5pt;">
                            Yang Mengajukan,
                        </p>
                        <p style="margin: 0; font-size: 10.5pt; font-weight: bold; text-decoration: underline;">
                            {{ $signerName }}
                        </p>
                        <p style="margin: 3px 0 0 0; font-size: 9.5pt;">
                            NIP. {{ (!empty($signerNip) && $signerNip !== '-') ? $signerNip : '-' }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR 2: BUKTI SERAH TERIMA BARANG ATK (GAMBAR 2)                        -->
    <!-- ========================================================================= -->
    <div id="lembar-2" class="sheet-page">

        <!-- KOP SURAT BIRO UMUM / SUBBAG TU SESUAI GAMBAR 2 -->
        <div class="kop-surat-bast d-flex align-items-center">
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
        <table class="table table-borderless table-sm mb-3" style="font-size: 13px; width: 100%;">
            <tr>
                <td width="25%"><strong>{{ $isPicOrSubdit ? 'Nama Penerima (Perwakilan)' : 'Nama Penerima / Pemohon' }}</strong></td>
                <td width="2%">:</td>
                <td width="73%">
                    <strong>{{ $requestItem->receiver_name ?? ($requesterUser ? ($requesterUser->name_with_title ?: $requesterUser->name) : '-') }}</strong>
                    @if ($isPicOrSubdit)
                        <span class="text-muted ms-1">(Perwakilan Subdirektorat)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Unit Kerja / Subdirektorat</strong></td>
                <td>:</td>
                <td>
                    @if ($isPicOrSubdit && !empty($requesterUser->subdit_name))
                        <strong>{{ $requesterUser->subdit_name }}</strong>
                        @if (!empty($requesterUser->position))
                            <span class="text-muted d-block small">{{ $requesterUser->position }}</span>
                        @endif
                    @else
                        {{ $requesterUser->position ?? ($requesterUser->name ?? '-') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Jenis Permintaan</strong></td>
                <td>:</td>
                <td>
                    {{ $isPicOrSubdit ? 'Permintaan Subdirektorat (Perwakilan PIC)' : 'Individu (Pegawai)' }}
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
                    @if ($requestItem->status === 'distributed')
                        <span class="fw-bold text-dark">Disetujui Kasubbag TU &amp; Telah Didistribusikan</span>
                    @else
                        <span class="fw-bold text-dark">Disetujui Kasubbag TU (Siap Diserahkan)</span>
                    @endif
                </td>
            </tr>
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
                        <td class="text-center font-monospace">{{ $detail->product->code ?? '-' }}</td>
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

        @if ($requestItem->verification_notes)
            <div class="p-2 border bg-light my-2" style="font-size: 12px;">
                <strong>Catatan Verifikasi Subbag TU:</strong> {{ $requestItem->verification_notes }}
            </div>
        @endif

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
    <div id="lembar-3" class="sheet-page">

        <div class="kop-surat-bast d-flex align-items-center mb-3">
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
                    Foto Dokumentasi Fisik Penyerahan Barang (Petugas Gudang)
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
</body>

</html>
