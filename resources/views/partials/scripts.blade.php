<!-- Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
<!-- External Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/external.min.js') }}"></script>
<!-- Settings Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/setting.js') }}"></script>
<!-- Slider-tab Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/slider-tabs.js') }}"></script>
<!-- SweetAlert2 Plugin -->
<script src="{{ asset('hope-ui/assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<!-- App Script -->
<script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}"></script>
<!-- Global Alert Dismiss & Auto-Dismiss System -->
<script src="{{ asset('js/ateka-alerts.js') }}?v=1.0"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>

