@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Manajemen Pengguna &amp; Peran</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid content-inner mt-n5 py-0">
        <div class="row">
            <div class="col-sm-12">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-1 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-0 fw-bold">Master Data Akun Pengguna</h4>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm btn-create-user"
                            data-bs-toggle="modal" data-bs-target="#modalUser">
                            + Tambah Pengguna Baru
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <table id="datatable" class="table table-striped table-hover align-middle word-wrap"
                            data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th width="4%">No</th>
                                        <th>Nama & Jabatan</th>
                                        <th>Peran (Role)</th>
                                        <th>Email</th>
                                        <th>Tanggal Dibuat</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        @php
                                            $roleBadge = match ($user->role) {
                                                'kasubag_tu', 'admin' => 'bg-primary',
                                                'pic_distribusi' => 'bg-warning text-dark',
                                                'pegawai_gudang' => 'bg-dark text-white',
                                                default => 'bg-secondary text-white',
                                            };
                                            $roleLabel = match ($user->role) {
                                                'kasubag_tu', 'admin' => 'Kasubag TU (Approver)',
                                                'pic_distribusi' => 'PIC Subdirektorat (Pemohon)',
                                                'pegawai_gudang' => 'Pegawai Gudang',
                                                default => 'Pegawai (Individu)',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong class="text-dark">{{ $user->name_with_title ?? $user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->position ?? '-' }}</small>
                                                @if($user->nip)
                                                    <br><span class="badge bg-light text-secondary border mt-1" style="font-size: 0.72rem;">NIP: {{ $user->nip }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $roleBadge }} px-2.5 py-1">
                                                    {{ $roleLabel }}
                                                </span>
                                                @if ($user->role === 'pegawai_gudang' && $user->is_distributor)
                                                    <span class="badge bg-success-subtle text-success border ms-1 px-2 py-0.5" style="font-size: 0.72rem;" title="Ditugaskan sebagai Petugas yang Mendistribusikan Barang (Yang Menyerahkan pada Bukti Serah Terima)">
                                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-0.5">
                                                            <path d="M1 3h15v13H1z"></path>
                                                            <path d="M16 8h4l3 3v5h-7V8z"></path>
                                                            <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                                            <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                                        </svg>
                                                        Petugas Distribusi
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="font-monospace small">{{ $user->email }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                                    <button type="button" class="btn btn-info btn-sm btn-detail-user text-white"
                                                        data-bs-toggle="modal" data-bs-target="#modalUser"
                                                        data-id="{{ $user->id }}" 
                                                        data-name="{{ e($user->name) }}"
                                                        data-title="{{ e($user->title) }}"
                                                        data-nip="{{ e($user->nip) }}"
                                                        data-email="{{ e($user->email) }}"
                                                        data-role="{{ e($user->role) }}"
                                                        data-is-distributor="{{ $user->is_distributor ? '1' : '0' }}"
                                                        data-position="{{ e($user->position) }}"
                                                        data-subdit-name="{{ e($user->subdit_name) }}"
                                                        data-role-label="{{ $roleLabel }}"
                                                        data-created-at="{{ $user->created_at ? $user->created_at->format('d-m-Y H:i') : '-' }}"
                                                        data-updated-at="{{ $user->updated_at ? $user->updated_at->format('d-m-Y H:i') : '-' }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-user text-dark"
                                                        data-bs-toggle="modal" data-bs-target="#modalUser"
                                                        data-id="{{ $user->id }}" 
                                                        data-name="{{ e($user->name) }}"
                                                        data-title="{{ e($user->title) }}"
                                                        data-nip="{{ e($user->nip) }}"
                                                        data-email="{{ e($user->email) }}"
                                                        data-role="{{ e($user->role) }}"
                                                        data-is-distributor="{{ $user->is_distributor ? '1' : '0' }}"
                                                        data-position="{{ e($user->position) }}"
                                                        data-subdit-name="{{ e($user->subdit_name) }}">
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('master-data.manajemen-pengguna.destroy', $user->id) }}"
                                                        method="POST"
                                                        class="d-inline form-delete-user">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm btn-delete-user"
                                                            data-id="{{ $user->id }}"
                                                            data-name="{{ e($user->name_with_title ?? $user->name) }}"
                                                            data-email="{{ e($user->email) }}"
                                                            data-role="{{ e($roleLabel) }}"
                                                            data-position="{{ e($user->position ?? '-') }}"
                                                            data-nip="{{ e($user->nip ?? '') }}"
                                                            {{ auth()->id() == $user->id ? 'disabled title="Akun Anda sendiri tidak dapat dihapus"' : '' }}>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create / Update / Detail Pengguna --}}
    <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formUser" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUserLabel">
                            Form Pengguna
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="detailUserInfo" class="alert alert-info d-none mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Peran Pengguna</small>
                                    <strong id="detailRoleLabel">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">ID Akun</small>
                                    <strong id="detailUserId">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tanggal Dibuat</small>
                                    <strong id="detailCreatedAt">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Nomor Induk Pegawai (NIP)</small>
                                    <strong id="detailNip">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Unit / Subdirektorat Resmi</small>
                                    <strong id="detailSubditName">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Terakhir Diupdate</small>
                                    <strong id="detailUpdatedAt">-</strong>
                                </div>
                                <div class="col-md-12 d-none" id="detailDistributorWrap">
                                    <small class="text-muted d-block">Wewenang Penyerahan Barang</small>
                                    <div id="detailDistributorStatus" class="mt-1">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Nama Pengguna <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Utami M" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Gelar Akademik / Titel <span class="text-muted font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="title" id="userTitle" class="form-control"
                                    placeholder="S.T. / M.Si. / S.Kom.">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Nomor Induk Pegawai (NIP) <span class="text-danger" id="nipRequiredAsterisk">*</span> <span class="text-muted font-normal d-none" id="nipOptionalLabel">(Opsional)</span>
                                </label>
                                <input type="text" name="nip" id="nip" class="form-control"
                                    placeholder="19950510 202012 1 002" required>
                                <small class="text-muted" id="nipHelpText" style="font-size: 0.75rem;">
                                    Wajib diisi untuk identitas dinas & verifikasi keamanan lupa password.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="nama@pkp.go.id" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Peran / Role <span class="text-danger">*</span>
                                </label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="kasubag_tu">Kasubag TU (Approver / Pimpinan)</option>
                                    <option value="pic_distribusi">PIC Subdirektorat (Pemohon Kolektif)</option>
                                    <option value="pegawai_gudang">Pegawai Gudang</option>
                                    <option value="pegawai" selected>Pegawai</option>
                                </select>
                            </div>

                            {{-- Pilihan Unit Subdirektorat PIC Distribusi --}}
                            <div class="col-md-6 d-none" id="picUnitContainer">
                                <label class="form-label fw-bold small text-uppercase">
                                    Pilih Subdirektorat / Unit Kerja PIC <span class="text-danger">*</span>
                                </label>
                                <select name="pic_unit_choice" id="pic_unit_choice" class="form-select">
                                    <option value="" disabled selected>-- Pilih Unit Penugasan PIC --</option>
                                    <option value="Subdirektorat SDK">Subdirektorat SDK (Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman)</option>
                                    <option value="Subdirektorat PJF">Subdirektorat PJF (Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi)</option>
                                    <option value="Subdirektorat KBR">Subdirektorat KBR (Kendala Bangunan Rumah)</option>
                                    <option value="Subbagian TU">Subbagian TU (Tata Usaha)</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.75rem;">Pilih unit penugasan resmi untuk penyerahan barang dan dokumen bukti distribusi.</small>
                            </div>

                            <div class="col-md-6" id="positionContainer">
                                <label class="form-label fw-bold small text-uppercase">
                                    Jabatan / Posisi Kerja <span class="text-muted font-normal">(Opsional)</span>
                                </label>
                                <input type="text" name="position" id="position" class="form-control"
                                    placeholder="PIC Distribusi Subdirektorat SDK / Staf Keuangan">
                                <small class="text-muted" style="font-size: 0.75rem;">Terisi otomatis saat memilih unit penugasan PIC, atau dapat disesuaikan.</small>
                            </div>

                            {{-- Pengaturan Wewenang Distribusi untuk Pegawai Gudang --}}
                            <div class="col-12 d-none" id="gudangDistribusiContainer">
                                <div class="p-3 border rounded-3 bg-light border-primary-subtle shadow-xs">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div style="max-width: 80%;">
                                            <label class="form-check-label fw-bold text-dark mb-0 d-block" for="is_distributor">
                                                Tugaskan Sebagai Petugas Distribusi (Yang Menyerahkan Barang)
                                            </label>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                Aktifkan jika akun Pegawai Gudang ini bertugas menyerahkan barang ke pemohon. Namanya akan otomatis diambil dan dicantumkan pada <strong>Bukti Serah Terima Barang</strong>.
                                            </small>
                                        </div>
                                        <div class="form-check form-switch fs-4 mb-0">
                                            <input class="form-check-input" type="checkbox" name="is_distributor" id="is_distributor" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 password-wrapper">
                                <label class="form-label fw-bold small text-uppercase">
                                    Password <span class="text-danger password-required">*</span>
                                </label>
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Minimal 6 karakter">
                                <small class="text-muted password-help d-none">
                                    Kosongkan jika tidak ingin mengubah password.
                                </small>
                            </div>

                            <div class="col-md-6 password-wrapper">
                                <label class="form-label fw-bold small text-uppercase">
                                    Konfirmasi Password <span class="text-danger password-required">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitUser">
                            Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // SweetAlert2 Flash Notifications
                @if (session('success'))
                    if (typeof Swal !== 'undefined') {
                        const successMsg = {!! json_encode(session('success')) !!};
                        let titleText = 'Berhasil!';
                        const lowerMsg = successMsg.toLowerCase();
                        if (lowerMsg.includes('dihapus')) {
                            titleText = 'Pengguna Dihapus!';
                        } else if (lowerMsg.includes('diperbarui') || lowerMsg.includes('diperbaharui')) {
                            titleText = 'Pengguna Diperbarui!';
                        } else if (lowerMsg.includes('ditambahkan') || lowerMsg.includes('dibuat')) {
                            titleText = 'Pengguna Ditambahkan!';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: titleText,
                            html: `<div class="text-secondary small mt-1">${successMsg}</div>`,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#059669',
                            width: '350px',
                            padding: '1.25rem',
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0 p-3',
                                title: 'fw-bold text-dark fs-5 mb-0',
                                confirmButton: 'btn btn-success btn-sm px-3 py-1.5 fw-semibold rounded-3'
                            },
                            timer: 4000,
                            timerProgressBar: true
                        });
                    }
                @endif

                @if (session('error'))
                    if (typeof Swal !== 'undefined') {
                        const errorMsg = {!! json_encode(session('error')) !!};
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: `<div class="text-secondary small mt-1">${errorMsg}</div>`,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#ef4444',
                            width: '350px',
                            padding: '1.25rem',
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0 p-3',
                                title: 'fw-bold text-dark fs-5 mb-0',
                                confirmButton: 'btn btn-danger btn-sm px-3 py-1.5 fw-semibold rounded-3'
                            }
                        });
                    }
                @endif

                function escapeHtml(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                // Modern Compact Delete Confirmation matching user design (Shadcn / Tailwind Style)
                $(document).on('click', '.btn-delete-user', function(e) {
                    e.preventDefault();
                    const $btn = $(this);
                    if ($btn.prop('disabled')) return;

                    const $form = $btn.closest('form');
                    const userName = $btn.data('name') || 'pengguna ini';

                    const modalHtml = `
                        <div class="text-center pt-1">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 48px; height: 48px; background-color: #fef2f2;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                            </div>
                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; letter-spacing: -0.01em;">Are you sure?</h5>
                            <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.5; max-width: 270px; margin: 0 auto;">
                                Tindakan ini tidak dapat dibatalkan. Seluruh data pengguna <strong>${escapeHtml(userName)}</strong> akan dihapus permanen.
                            </p>
                        </div>
                    `;

                    Swal.fire({
                        html: modalHtml,
                        width: '350px',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus pengguna',
                        cancelButtonText: 'Batal',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'custom-delete-popup',
                            actions: 'custom-delete-actions',
                            confirmButton: 'custom-delete-confirm-btn',
                            cancelButton: 'custom-delete-cancel-btn'
                        },
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                html: `
                                    <div class="text-center py-2">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Menghapus Pengguna...</h6>
                                        <p class="text-muted small mb-0">Mohon tunggu sebentar...</p>
                                    </div>
                                `,
                                width: '300px',
                                padding: '1.25rem',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'custom-delete-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                    $form.submit();
                                }
                            });
                        }
                    });
                });

                const storeUrl = "{{ route('master-data.manajemen-pengguna.store') }}";
                const updateUrlTemplate = "{{ route('master-data.manajemen-pengguna.update', ':id') }}";

                const $modal = $('#modalUser');
                const $form = $('#formUser');
                const $method = $('#formMethod');
                const $title = $('#modalUserLabel');
                const $btnSubmit = $('#btnSubmitUser');
                const $name = $('#name');
                const $userTitle = $('#userTitle');
                const $nip = $('#nip');
                const $nipRequiredAsterisk = $('#nipRequiredAsterisk');
                const $nipOptionalLabel = $('#nipOptionalLabel');
                const $nipHelpText = $('#nipHelpText');
                const $email = $('#email');
                const $role = $('#role');
                const $position = $('#position');
                const $picUnitContainer = $('#picUnitContainer');
                const $picUnitChoice = $('#pic_unit_choice');
                const $gudangDistribusiContainer = $('#gudangDistribusiContainer');
                const $isDistributor = $('#is_distributor');
                const $password = $('#password');
                const $passwordConfirmation = $('#password_confirmation');
                const $passwordWrapper = $('.password-wrapper');
                const $passwordHelp = $('.password-help');
                const $passwordRequired = $('.password-required');
                const $detailInfo = $('#detailUserInfo');
                const $detailUserId = $('#detailUserId');
                const $detailRoleLabel = $('#detailRoleLabel');
                const $detailNip = $('#detailNip');
                const $detailSubditName = $('#detailSubditName');
                const $detailCreatedAt = $('#detailCreatedAt');
                const $detailUpdatedAt = $('#detailUpdatedAt');
                const $detailDistributorWrap = $('#detailDistributorWrap');
                const $detailDistributorStatus = $('#detailDistributorStatus');

                function updateRoleContainers() {
                    const roleVal = $role.val();
                    if (roleVal === 'pic_distribusi') {
                        $picUnitContainer.removeClass('d-none');
                        $gudangDistribusiContainer.addClass('d-none');
                        $isDistributor.prop('checked', false);
                    } else if (roleVal === 'pegawai_gudang') {
                        $picUnitContainer.addClass('d-none');
                        $gudangDistribusiContainer.removeClass('d-none');
                        if (!$position.val()) {
                            $position.val('Pegawai Gudang');
                        }
                    } else {
                        $picUnitContainer.addClass('d-none');
                        $gudangDistribusiContainer.addClass('d-none');
                        $isDistributor.prop('checked', false);
                        if (roleVal === 'kasubag_tu' && !$position.val()) {
                            $position.val('Kasubag TU');
                        }
                    }

                    // Dinamis: Ketika role adalah Pegawai Gudang, field NIP menjadi opsional
                    if (roleVal === 'pegawai_gudang') {
                        $nip.prop('required', false);
                        $nipRequiredAsterisk.addClass('d-none');
                        $nipOptionalLabel.removeClass('d-none');
                        $nipHelpText.text('Opsional untuk Pegawai Gudang. Wajib diisi untuk ASN/pegawai dinas lainnya.');
                    } else {
                        $nip.prop('required', true);
                        $nipRequiredAsterisk.removeClass('d-none');
                        $nipOptionalLabel.addClass('d-none');
                        $nipHelpText.text('Wajib diisi untuk identitas dinas & verifikasi keamanan lupa password.');
                    }
                }

                $role.on('change', updateRoleContainers);

                $isDistributor.on('change', function() {
                    if ($(this).is(':checked')) {
                        if (!$position.val() || $position.val() === 'Pegawai Gudang') {
                            $position.val('Petugas Distribusi Gudang ATK');
                        }
                    } else {
                        if ($position.val() === 'Petugas Distribusi Gudang ATK') {
                            $position.val('Pegawai Gudang');
                        }
                    }
                });

                $picUnitChoice.on('change', function() {
                    const val = $(this).val();
                    const map = {
                        'Subbagian TU': 'PIC Distribusi Subbagian Tata Usaha',
                        'Subdirektorat KBR': 'PIC Distribusi Subdirektorat KBR',
                        'Subdirektorat SDK': 'PIC Distribusi Subdirektorat SDK',
                        'Subdirektorat PJF': 'PIC Distribusi Subdirektorat PJF',
                        'TU': 'PIC Distribusi Subbagian Tata Usaha',
                        'KBR': 'PIC Distribusi Subdirektorat KBR',
                        'SDK': 'PIC Distribusi Subdirektorat SDK',
                        'PJF': 'PIC Distribusi Subdirektorat PJF',
                    };
                    if (map[val]) {
                        $position.val(map[val]);
                    }
                });

                function resetModalUser() {
                    $form[0].reset();
                    $form.attr('action', storeUrl);
                    $method.prop('disabled', true).val('');
                    $name.prop('readonly', false).val('');
                    $userTitle.prop('readonly', false).val('');
                    $nip.prop('readonly', false).val('');
                    $email.prop('readonly', false).val('');
                    $role.prop('disabled', false).val('pegawai');
                    $position.prop('readonly', false).val('');
                    $picUnitChoice.val('').prop('disabled', false);
                    $picUnitContainer.addClass('d-none');
                    $isDistributor.prop('checked', false).prop('disabled', false);
                    $gudangDistribusiContainer.addClass('d-none');
                    $detailDistributorWrap.addClass('d-none');
                    $detailDistributorStatus.text('-');
                    updateRoleContainers();
                    $password.prop('readonly', false).val('');
                    $passwordConfirmation.prop('readonly', false).val('');
                    $passwordWrapper.removeClass('d-none');
                    $passwordHelp.addClass('d-none');
                    $passwordRequired.removeClass('d-none');
                    $password.prop('required', true);
                    $passwordConfirmation.prop('required', true);
                    $detailInfo.addClass('d-none');
                    $detailUserId.text('-');
                    $detailRoleLabel.text('-');
                    $detailNip.text('-');
                    $detailSubditName.text('-');
                    $detailCreatedAt.text('-');
                    $detailUpdatedAt.text('-');
                    $title.text('Form Pengguna');
                    $btnSubmit.removeClass('d-none').text('Simpan Pengguna');
                }

                function showUserModal() {
                    const modalEl = document.getElementById('modalUser');
                    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else if (typeof $modal.modal === 'function') {
                        $modal.modal('show');
                    }
                }

                $(document).on('click', '.btn-create-user', function() {
                    resetModalUser();
                    $title.text('Tambah Pengguna Baru');
                    $btnSubmit.text('Simpan Pengguna');
                    showUserModal();
                });

                $(document).on('click', '.btn-edit-user', function() {
                    resetModalUser();
                    const id = $(this).data('id');
                    const updateUrl = updateUrlTemplate.replace(':id', id).replace('%3Aid', id);

                    $form.attr('action', updateUrl);
                    $method.prop('disabled', false).val('PUT');
                    $name.val($(this).data('name'));
                    $userTitle.val($(this).data('title') || '').prop('readonly', false);
                    $nip.val($(this).data('nip'));
                    $email.val($(this).data('email'));
                    $role.val($(this).data('role') || 'pegawai');
                    $position.val($(this).data('position'));

                    updateRoleContainers();

                    const isDist = $(this).data('is-distributor') == '1';
                    $isDistributor.prop('checked', isDist).prop('disabled', false);

                    const subditName = ($(this).data('subdit-name') || '').toString();
                    const pos = ($(this).data('position') || '').toString().toLowerCase();
                    const sub = subditName.toLowerCase();

                    if ($(this).data('role') === 'pic_distribusi') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $picUnitChoice.val('Subbagian TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $picUnitChoice.val('Subdirektorat KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $picUnitChoice.val('Subdirektorat SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $picUnitChoice.val('Subdirektorat PJF');
                    }

                    // Pada edit, password boleh kosong
                    $password.prop('required', false);
                    $passwordConfirmation.prop('required', false);
                    $passwordHelp.removeClass('d-none');
                    $passwordRequired.addClass('d-none');
                    $password.attr('placeholder', 'Kosongkan jika tidak ingin mengubah');
                    $passwordConfirmation.attr('placeholder', 'Kosongkan jika tidak ingin mengubah');

                    $title.text('Edit Pengguna');
                    $btnSubmit.text('Simpan Perubahan');
                    showUserModal();
                });

                $(document).on('click', '.btn-detail-user', function() {
                    resetModalUser();
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const titleVal = $(this).data('title') || '';
                    const nip = $(this).data('nip');
                    const email = $(this).data('email');
                    const role = $(this).data('role');
                    const roleLabel = $(this).data('role-label');
                    const position = $(this).data('position');
                    const subditName = $(this).data('subdit-name') || '';
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');
                    const isDist = $(this).data('is-distributor') == '1';

                    $name.val(name).prop('readonly', true);
                    $userTitle.val(titleVal).prop('readonly', true);
                    $nip.val(nip).prop('readonly', true);
                    $email.val(email).prop('readonly', true);
                    $role.val(role).prop('disabled', true);
                    $position.val(position).prop('readonly', true);

                    updateRoleContainers();
                    $picUnitChoice.prop('disabled', true);
                    $isDistributor.prop('checked', isDist).prop('disabled', true);

                    if (role === 'pegawai_gudang') {
                        $detailDistributorWrap.removeClass('d-none');
                        $detailDistributorStatus.html(isDist 
                            ? '<span class="badge bg-success-subtle text-success border px-2.5 py-1.5"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M1 3h15v13H1z"></path><path d="M16 8h4l3 3v5h-7V8z"></path><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>Petugas Distribusi Resmi (Yang Menyerahkan pada Bukti Serah Terima)</span>' 
                            : '<span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Staf Gudang Umum (Bukan Petugas Distribusi)</span>');
                    } else {
                        $detailDistributorWrap.addClass('d-none');
                    }

                    const pos = (position || '').toString().toLowerCase();
                    const sub = (subditName || '').toString().toLowerCase();

                    if (role === 'pic_distribusi') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $picUnitChoice.val('Subbagian TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $picUnitChoice.val('Subdirektorat KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $picUnitChoice.val('Subdirektorat SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $picUnitChoice.val('Subdirektorat PJF');
                    }

                    $passwordWrapper.addClass('d-none');
                    $password.prop('required', false);
                    $passwordConfirmation.prop('required', false);

                    $detailUserId.text(id);
                    $detailRoleLabel.text(roleLabel);
                    $detailNip.text(nip || '-');
                    $detailSubditName.text(subditName || '-');
                    $detailCreatedAt.text(createdAt);
                    $detailUpdatedAt.text(updatedAt);
                    $detailInfo.removeClass('d-none');

                    $title.text('Detail Pengguna');
                    $btnSubmit.addClass('d-none');
                    showUserModal();
                });

                $modal.on('hidden.bs.modal', function() {
                    resetModalUser();
                });
            });
        </script>
    @endpush
@endsection
