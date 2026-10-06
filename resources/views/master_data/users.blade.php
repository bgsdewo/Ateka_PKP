@extends('layouts.app')

@section('page-header')
    <style>
        .iq-navbar-header {
            position: relative;
            background-image: linear-gradient(rgba(0, 70, 80, 0.45), rgba(0, 70, 80, 0.45)), url("{{ asset('hope-ui/assets/images/dashboard/bg-2.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .iq-navbar-header .iq-container {
            position: relative;
            z-index: 2;
        }
    </style>
    <div class="iq-navbar-header" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Manajemen Pengguna & Peran (Role)</h1>
                            <p class="text-white mb-0" style="opacity: 0.9;">
                                Kelola hak akses akun pengguna untuk 5 peran: Kasubag TU, Arsiparis, PIC Distribusi, Subdirektorat (Kolektif), dan Pegawai (Individu).
                            </p>
                        </div>
                        <button type="button" class="btn btn-warning text-dark fw-bold btn-sm btn-create-user shadow-sm">
                            + Tambah Pengguna Baru
                        </button>
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
                            <small class="text-secondary">
                                Daftar seluruh pengguna terdaftar dengan peran wewenangnya dalam aplikasi
                            </small>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm btn-create-user">
                            + Tambah Pengguna
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
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
                                                'arsiparis' => 'bg-info text-white',
                                                'pic_distribusi' => 'bg-warning text-dark',
                                                'subdit' => 'bg-success text-white',
                                                default => 'bg-secondary text-white',
                                            };
                                            $roleLabel = match ($user->role) {
                                                'kasubag_tu', 'admin' => 'Kasubag TU (Approver)',
                                                'arsiparis' => 'Arsiparis (Pelaporan)',
                                                'pic_distribusi' => 'PIC Distribusi (Gudang)',
                                                'subdit' => 'Subdirektorat (Kolektif)',
                                                default => 'Pegawai (Individu)',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong class="text-dark">{{ $user->name }}</strong>
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
                                                        data-id="{{ $user->id }}" 
                                                        data-name="{{ e($user->name) }}"
                                                        data-nip="{{ e($user->nip) }}"
                                                        data-email="{{ e($user->email) }}"
                                                        data-role="{{ e($user->role) }}"
                                                        data-position="{{ e($user->position) }}"
                                                        data-subdit-name="{{ e($user->subdit_name) }}"
                                                        data-role-label="{{ $roleLabel }}"
                                                        data-created-at="{{ $user->created_at ? $user->created_at->format('d-m-Y H:i') : '-' }}"
                                                        data-updated-at="{{ $user->updated_at ? $user->updated_at->format('d-m-Y H:i') : '-' }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-user text-dark"
                                                        data-id="{{ $user->id }}" 
                                                        data-name="{{ e($user->name) }}"
                                                        data-nip="{{ e($user->nip) }}"
                                                        data-email="{{ e($user->email) }}"
                                                        data-role="{{ e($user->role) }}"
                                                        data-position="{{ e($user->position) }}"
                                                        data-subdit-name="{{ e($user->subdit_name) }}">
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('master-data.manajemen-pengguna.destroy', $user->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            {{ auth()->id() == $user->id ? 'disabled' : '' }}>
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
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Nama Pengguna <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Contoh: Utami M" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nip" id="nip" class="form-control"
                                    placeholder="Contoh: 19950510 202012 1 002" required>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    Wajib diisi untuk identitas dinas & verifikasi keamanan lupa password.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="email@perumahan.go.id" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">
                                    Peran / Role <span class="text-danger">*</span>
                                </label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="kasubag_tu">Kasubag TU (Approver / Pimpinan)</option>
                                    <option value="arsiparis">Arsiparis (Pelaporan & Administrasi)</option>
                                    <option value="pic_distribusi">PIC Distribusi (Pengelola / Gudang)</option>
                                    <option value="subdit">Subdirektorat (Pemohon Kolektif)</option>
                                    <option value="pegawai" selected>Pegawai (Pemohon Individu)</option>
                                </select>
                            </div>

                            {{-- Pilihan 4 Perwakilan Unit PIC Distribusi (TU, KBR, SDK, PJF) --}}
                            <div class="col-md-6 d-none" id="picUnitContainer">
                                <label class="form-label fw-bold small text-uppercase">
                                    Perwakilan Divisi PIC (TU, KBR, SDK, PJF) <span class="text-danger">*</span>
                                </label>
                                <select name="pic_unit_choice" id="pic_unit_choice" class="form-select">
                                    <option value="" disabled selected>-- Pilih 1 dari 4 Divisi PIC --</option>
                                    <option value="TU">Subbagian TU (Tata Usaha)</option>
                                    <option value="KBR">Subdirektorat KBR (Kendala Bangunan Rumah)</option>
                                    <option value="SDK">Subdirektorat SDK (Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman)</option>
                                    <option value="PJF">Subdirektorat PJF (Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi)</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.75rem;">Pilih unit penugasan PIC Distribusi.</small>
                            </div>

                            {{-- Pilihan Unit Subdirektorat (Jika role == subdit) --}}
                            <div class="col-md-6 d-none" id="subditUnitContainer">
                                <label class="form-label fw-bold small text-uppercase">
                                    Unit Subdirektorat <span class="text-danger">*</span>
                                </label>
                                <select name="subdit_unit_choice" id="subdit_unit_choice" class="form-select">
                                    <option value="" disabled selected>-- Pilih Unit Subdirektorat --</option>
                                    <option value="Subbagian TU">Subbagian TU (Tata Usaha)</option>
                                    <option value="Subdirektorat KBR">Subdirektorat KBR (Kendala Bangunan Rumah)</option>
                                    <option value="Subdirektorat SDK">Subdirektorat SDK (Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman)</option>
                                    <option value="Subdirektorat PJF">Subdirektorat PJF (Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi)</option>
                                </select>
                                <small class="text-muted" style="font-size: 0.75rem;">Pilih unit Subdirektorat resmi yang diwakili.</small>
                            </div>

                            <div class="col-md-6" id="positionContainer">
                                <label class="form-label fw-bold small text-uppercase">
                                    Jabatan / Posisi Kerja
                                </label>
                                <input type="text" name="position" id="position" class="form-control"
                                    placeholder="Contoh: Kasubag TU / PIC Distribusi Subdirektorat SDK / Staf Keuangan">
                                <small class="text-muted" style="font-size: 0.75rem;">Terisi otomatis saat memilih perwakilan PIC/Subdit, atau dapat disesuaikan.</small>
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
                const storeUrl = "{{ route('master-data.manajemen-pengguna.store') }}";
                const updateUrlTemplate = "{{ route('master-data.manajemen-pengguna.update', ':id') }}";

                const $modal = $('#modalUser');
                const $form = $('#formUser');
                const $method = $('#formMethod');
                const $title = $('#modalUserLabel');
                const $btnSubmit = $('#btnSubmitUser');
                const $name = $('#name');
                const $nip = $('#nip');
                const $email = $('#email');
                const $role = $('#role');
                const $position = $('#position');
                const $picUnitContainer = $('#picUnitContainer');
                const $picUnitChoice = $('#pic_unit_choice');
                const $subditUnitContainer = $('#subditUnitContainer');
                const $subditUnitChoice = $('#subdit_unit_choice');
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

                function updateRoleContainers() {
                    const roleVal = $role.val();
                    if (roleVal === 'pic_distribusi') {
                        $picUnitContainer.removeClass('d-none');
                        $subditUnitContainer.addClass('d-none');
                    } else if (roleVal === 'subdit') {
                        $subditUnitContainer.removeClass('d-none');
                        $picUnitContainer.addClass('d-none');
                    } else {
                        $picUnitContainer.addClass('d-none');
                        $subditUnitContainer.addClass('d-none');
                        if (roleVal === 'arsiparis' && !$position.val()) {
                            $position.val('Arsiparis Ahli Pertama');
                        } else if (roleVal === 'kasubag_tu' && !$position.val()) {
                            $position.val('Kasubag TU');
                        }
                    }
                }

                $role.on('change', updateRoleContainers);

                $picUnitChoice.on('change', function() {
                    const val = $(this).val();
                    const map = {
                        'TU': 'PIC Distribusi Subbagian Tata Usaha',
                        'KBR': 'PIC Distribusi Subdirektorat KBR',
                        'SDK': 'PIC Distribusi Subdirektorat SDK',
                        'PJF': 'PIC Distribusi Subdirektorat PJF',
                    };
                    if (map[val]) {
                        $position.val(map[val]);
                    }
                });

                $subditUnitChoice.on('change', function() {
                    const val = $(this).val();
                    if (val) {
                        $position.val(val);
                    }
                });

                function resetModalUser() {
                    $form[0].reset();
                    $form.attr('action', storeUrl);
                    $method.prop('disabled', true).val('');
                    $name.prop('readonly', false).val('');
                    $nip.prop('readonly', false).val('');
                    $email.prop('readonly', false).val('');
                    $role.prop('disabled', false).val('pegawai');
                    $position.prop('readonly', false).val('');
                    $picUnitChoice.val('').prop('disabled', false);
                    $subditUnitChoice.val('').prop('disabled', false);
                    $picUnitContainer.addClass('d-none');
                    $subditUnitContainer.addClass('d-none');
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

                $('.btn-create-user').on('click', function() {
                    resetModalUser();
                    $title.text('Tambah Pengguna Baru');
                    $btnSubmit.text('Simpan Pengguna');
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-edit-user', function() {
                    resetModalUser();
                    const id = $(this).data('id');
                    const updateUrl = updateUrlTemplate.replace(':id', id);

                    $form.attr('action', updateUrl);
                    $method.prop('disabled', false).val('PUT');
                    $name.val($(this).data('name'));
                    $nip.val($(this).data('nip'));
                    $email.val($(this).data('email'));
                    $role.val($(this).data('role') || 'pegawai');
                    $position.val($(this).data('position'));

                    updateRoleContainers();

                    const subditName = ($(this).data('subdit-name') || '').toString();
                    const pos = ($(this).data('position') || '').toString().toLowerCase();
                    const sub = subditName.toLowerCase();

                    if ($(this).data('role') === 'pic_distribusi') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $picUnitChoice.val('TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $picUnitChoice.val('KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $picUnitChoice.val('SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $picUnitChoice.val('PJF');
                    } else if ($(this).data('role') === 'subdit') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $subditUnitChoice.val('Subbagian TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $subditUnitChoice.val('Subdirektorat KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $subditUnitChoice.val('Subdirektorat SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $subditUnitChoice.val('Subdirektorat PJF');
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
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-detail-user', function() {
                    resetModalUser();
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const nip = $(this).data('nip');
                    const email = $(this).data('email');
                    const role = $(this).data('role');
                    const roleLabel = $(this).data('role-label');
                    const position = $(this).data('position');
                    const subditName = $(this).data('subdit-name') || '';
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');

                    $name.val(name).prop('readonly', true);
                    $nip.val(nip).prop('readonly', true);
                    $email.val(email).prop('readonly', true);
                    $role.val(role).prop('disabled', true);
                    $position.val(position).prop('readonly', true);

                    updateRoleContainers();
                    $picUnitChoice.prop('disabled', true);
                    $subditUnitChoice.prop('disabled', true);

                    const pos = (position || '').toString().toLowerCase();
                    const sub = (subditName || '').toString().toLowerCase();

                    if (role === 'pic_distribusi') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $picUnitChoice.val('TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $picUnitChoice.val('KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $picUnitChoice.val('SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $picUnitChoice.val('PJF');
                    } else if (role === 'subdit') {
                        if (pos.includes('tu') || sub.includes('tata usaha')) $subditUnitChoice.val('Subbagian TU');
                        else if (pos.includes('kbr') || sub.includes('kendala')) $subditUnitChoice.val('Subdirektorat KBR');
                        else if (pos.includes('sdk') || sub.includes('sumber daya')) $subditUnitChoice.val('Subdirektorat SDK');
                        else if (pos.includes('pjf') || sub.includes('fungsional')) $subditUnitChoice.val('Subdirektorat PJF');
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
                    $modal.modal('show');
                });

                $modal.on('hidden.bs.modal', function() {
                    resetModalUser();
                });
            });
        </script>
    @endpush
@endsection
