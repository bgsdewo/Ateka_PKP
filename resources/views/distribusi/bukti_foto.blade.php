<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bukti Foto Penyerahan ATK - {{ $requestItem->request_code }}</title>
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hope-ui/assets/css/hope-ui.min.css') }}">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #212529;
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

        .container-box {
            max-width: 820px;
            margin: 24px auto;
            padding: 0 16px;
        }

        .photo-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .photo-img {
            max-height: 520px;
            width: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- Bar Navigasi Atas -->
    <div class="no-print-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold">Bukti Foto Penyerahan Barang (PIC)</span>
            <span class="badge bg-warning text-dark font-monospace">{{ $requestItem->request_code }}</span>
        </div>
        <div class="d-flex gap-2">
            @if ($requestItem->distribution_proof)
                <a href="{{ $requestItem->distribution_proof_url }}" target="_blank" class="btn btn-warning btn-sm fw-bold">
                    Buka Ukuran Asli
                </a>
            @endif
            <button onclick="window.close(); if(history.length > 1) { history.back(); }" class="btn btn-outline-light btn-sm">
                Tutup / Kembali
            </button>
        </div>
    </div>

    <div class="container-box">
        <div class="photo-card p-4">
            <!-- Header Dokumen Bukti Foto -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Dokumentasi Bukti Serah Terima ATK</h5>
                    <small class="text-muted">Diunggah oleh Petugas PIC Distribusi sebagai tanda bukti fisik barang telah diserahkan.</small>
                </div>
                <span class="badge bg-success-subtle text-success border px-3 py-1.5 rounded-pill fs-7 fw-bold">
                    Telah Diserahterimakan
                </span>
            </div>

            <!-- Konten Foto -->
            @if ($requestItem->distribution_proof)
                <div class="text-center p-3 bg-light border rounded mb-3">
                    <img src="{{ $requestItem->distribution_proof_url }}" alt="Bukti Penyerahan {{ $requestItem->request_code }}" class="photo-img border shadow-sm">
                </div>
            @else
                <div class="alert alert-warning text-center py-5 my-3 rounded">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-2 text-warning">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Bukti Foto</h6>
                    <p class="text-muted small mb-0">Petugas PIC Distribusi belum mengunggah foto dokumentasi serah terima untuk pengajuan ini.</p>
                </div>
            @endif

            <!-- Rincian Serah Terima -->
            <div class="card bg-light border-0 mb-3">
                <div class="card-body p-3">
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Penerima Barang Fisik:</span>
                            <strong class="text-dark fs-6">{{ $requestItem->receiver_name ?? ($requestItem->user->name ?? '-') }}</strong>
                            <small class="text-muted d-block">{{ $requestItem->user->position ?? '-' }}</small>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Diserahkan oleh PIC:</span>
                            <strong class="text-dark fs-6">{{ $requestItem->distributor->name_with_title ?? ($requestItem->distributor->name ?? 'PIC Gudang') }}</strong>
                            <small class="text-muted d-block">{{ $requestItem->distributor->position ?? 'PIC Distribusi' }}</small>
                        </div>
                        <div class="col-sm-6 mt-2 pt-2 border-top">
                            <span class="text-muted d-block">Waktu Penyerahan:</span>
                            <strong class="text-dark">{{ $requestItem->distributed_at ? $requestItem->distributed_at->locale('id')->translatedFormat('l, d F Y - H:i') . ' WIB' : '-' }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2 pt-2 border-top">
                            <span class="text-muted d-block">Catatan Penyerahan PIC:</span>
                            <span class="text-dark fst-italic">{{ $requestItem->distribution_notes ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <button onclick="window.close(); if(history.length > 1) { history.back(); }" class="btn btn-secondary btn-sm px-4">
                    Tutup Tampilan Bukti
                </button>
            </div>
        </div>
    </div>
</body>

</html>
