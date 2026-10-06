<!-- Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
<script>
    if (typeof $ !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.ext.errMode = 'none';
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                lengthMenu: "Tampilkan _MENU_ entri",
                loadingRecords: "Memuat...",
                processing: "Sedang memproses...",
                search: "Cari:",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }
</script>
<!-- External Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/external.min.js') }}"></script>
<!-- Widgetchart Script -->
<script src="{{ asset('hope-ui/assets/js/charts/widgetcharts.js') }}"></script>
<!-- Mapchart Script -->
<script src="{{ asset('hope-ui/assets/js/charts/vectore-chart.js') }}"></script>
<script src="{{ asset('hope-ui/assets/js/charts/dashboard.js') }}"></script>
<!-- Fslightbox Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/fslightbox.js') }}"></script>
<!-- Settings Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/setting.js') }}"></script>
<!-- Slider-tab Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/slider-tabs.js') }}"></script>
<!-- Form Wizard Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/form-wizard.js') }}"></script>
<!-- SweetAlert2 Plugin -->
<script src="{{ asset('hope-ui/assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<!-- App Script -->
<script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}" defer></script>

