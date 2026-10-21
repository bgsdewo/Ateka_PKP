@extends('layouts.app')

@section('page-header')
    <div class="iq-navbar-header" style="height: 220px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="flex-wrap d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h1 class="text-white fw-bold mb-1">Kategori Barang ATK</h1>
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
                    <div class="alert alert-bottom alert-success alert-dismissible fade show" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <span>
                            <strong>Berhasil!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-bottom alert-danger alert-dismissible fade show" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <span>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-bottom alert-danger alert-dismissible fade show" role="alert">
                        <span>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                            aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Master Data Kategori Barang</h4>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm btn-create-kategori">
                            Tambah Kategori
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-striped table-hover word-wrap"
                            data-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th width="15%">Tanggal Dibuat</th>
                                        <th width="25%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-primary font-monospace">{{ $category->kode_barang ?: ($category->kd_rincian ?: '-') }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                            </td>
                                            <td>
                                                @if ($category->description)
                                                    {{ $category->description }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $category->created_at ? $category->created_at->format('d-m-Y H:i') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                                    <button type="button" class="btn btn-info btn-sm btn-detail-kategori"
                                                        data-id="{{ $category->id }}" data-name="{{ e($category->name) }}"
                                                        data-kode-barang="{{ e($category->kode_barang ?? $category->kd_rincian ?? '') }}"
                                                        data-description="{{ e($category->description) }}"
                                                        data-products-count="{{ $category->products_count }}"
                                                        data-created-at="{{ $category->created_at ? $category->created_at->format('d-m-Y H:i') : '-' }}"
                                                        data-updated-at="{{ $category->updated_at ? $category->updated_at->format('d-m-Y H:i') : '-' }}">
                                                        Detail
                                                    </button>
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit-kategori"
                                                        data-id="{{ $category->id }}" data-name="{{ e($category->name) }}"
                                                        data-kode-barang="{{ e($category->kode_barang ?? $category->kd_rincian ?? '') }}"
                                                        data-description="{{ e($category->description) }}">
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('master-data.kategori-barang.destroy', $category->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
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

    <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formKategori" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="" disabled>

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKategoriLabel">
                            Form Kategori Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div id="detailKategoriInfo" class="alert alert-info d-none mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tanggal Dibuat</small>
                                    <strong id="detailCreatedAt">-</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Terakhir Diupdate</small>
                                    <strong id="detailUpdatedAt">-</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Kode Barang BMN
                                </label>
                                <input type="text" name="kode_barang" id="kode_barang" class="form-control font-monospace"
                                    placeholder="1010301001">
                                <small class="text-muted" style="font-size: 0.74rem;">Standar Kode Barang Persediaan BMN (10 Digit)</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Pensil" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Tulis deskripsi singkat kategori barang"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitKategori">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const storeUrl = "{{ route('master-data.kategori-barang.store') }}";
                const updateUrlTemplate = "{{ route('master-data.kategori-barang.update', ':id') }}";

                const $modal = $('#modalKategori');
                const $form = $('#formKategori');
                const $method = $('#formMethod');
                const $title = $('#modalKategoriLabel');
                const $btnSubmit = $('#btnSubmitKategori');
                const $inputName = $('#name');
                const $inputKodeBarang = $('#kode_barang');
                const $inputDescription = $('#description');
                const $detailInfo = $('#detailKategoriInfo');
                const $detailProductsCount = $('#detailProductsCount');
                const $detailCreatedAt = $('#detailCreatedAt');
                const $detailUpdatedAt = $('#detailUpdatedAt');

                function resetModalKategori() {
                    $form[0].reset();
                    $form.attr('action', storeUrl);
                    $method.prop('disabled', true).val('');
                    $inputName.prop('readonly', false);
                    $inputKodeBarang.prop('readonly', false).val('');
                    $inputDescription.prop('readonly', false);
                    $detailInfo.addClass('d-none');
                    $detailProductsCount.text('0 Produk');
                    $detailCreatedAt.text('-');
                    $detailUpdatedAt.text('-');
                    $title.text('Form Kategori Barang');
                    $btnSubmit.removeClass('d-none').text('Simpan Kategori');
                    $form.find('button[type="submit"]').prop('disabled', false);
                }

                $('.btn-create-kategori').on('click', function() {
                    resetModalKategori();
                    $title.text('Tambah Kategori Barang');
                    $btnSubmit.text('Simpan Kategori');
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-edit-kategori', function() {
                    resetModalKategori();
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const description = $(this).data('description');
                    const updateUrl = updateUrlTemplate.replace(':id', id);

                    $form.attr('action', updateUrl);
                    $method.prop('disabled', false).val('PUT');
                    $inputName.val(name);
                    $inputKodeBarang.val($(this).data('kode-barang') || '');
                    $inputDescription.val(description);
                    $title.text('Edit Kategori Barang');
                    $btnSubmit.text('Simpan Perubahan');
                    $modal.modal('show');
                });

                $(document).on('click', '.btn-detail-kategori', function() {
                    resetModalKategori();
                    const name = $(this).data('name');
                    const description = $(this).data('description');
                    const productsCount = $(this).data('products-count');
                    const createdAt = $(this).data('created-at');
                    const updatedAt = $(this).data('updated-at');

                    $inputName.val(name);
                    $inputKodeBarang.val($(this).data('kode-barang') || '-');
                    $inputDescription.val(description ? description : '-');

                    $inputName.prop('readonly', true);
                    $inputKodeBarang.prop('readonly', true);
                    $inputDescription.prop('readonly', true);

                    $detailProductsCount.text(productsCount + ' Produk');
                    $detailCreatedAt.text(createdAt);
                    $detailUpdatedAt.text(updatedAt);
                    $detailInfo.removeClass('d-none');

                    $title.text('Detail Kategori Barang');

                    // Karena mode detail hanya lihat data, tombol submit disembunyikan
                    $btnSubmit.addClass('d-none');
                    $modal.modal('show');
                });

                $modal.on('hidden.bs.modal', function() {
                    resetModalKategori();
                });
            });
        </script>
    @endpush
@endsection
