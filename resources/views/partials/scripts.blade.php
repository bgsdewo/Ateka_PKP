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

<!-- Global DataTables Default Configuration (Indonesian Language) -->
<script>
    if (typeof $ !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.ext.errMode = 'none';
        $.extend(true, $.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [
                { targets: 'no-sort', orderable: false }
            ],
            language: {
                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                info: "Menampilkan <strong>_START_</strong> sampai <strong>_END_</strong> dari <strong>_TOTAL_</strong> entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)",
                lengthMenu: "Tampilkan _MENU_ entri",
                loadingRecords: "Memuat data...",
                processing: "Sedang memproses...",
                search: "",
                searchPlaceholder: "Ketik kata kunci pencarian...",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                paginate: {
                    first: "&laquo;&laquo;",
                    last: "&raquo;&raquo;",
                    next: "Selanjutnya &rsaquo;",
                    previous: "&lsaquo; Sebelumnya"
                }
            }
        });

        // Pastikan placeholder konsisten jika DataTables di-init otomatis
        $(document).on('init.dt', function(e, settings) {
            var $api = new $.fn.dataTable.Api(settings);
            var $search = $($api.table().container()).find('.dataTables_filter input');
            if ($search.length && !$search.attr('placeholder')) {
                $search.attr('placeholder', 'Ketik kata kunci pencarian...');
            }
            // Hapus teks "Cari:" jika masih ada di label
            var $label = $($api.table().container()).find('.dataTables_filter label');
            $label.contents().filter(function() {
                return this.nodeType === 3 && $.trim(this.nodeValue) !== '';
            }).remove();
        });
    }
</script>

<!-- App Script -->
<script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}?v={{ file_exists(public_path('hope-ui/assets/js/hope-ui.js')) ? filemtime(public_path('hope-ui/assets/js/hope-ui.js')) : '2.1' }}"></script>
<!-- Global Alert Dismiss & Auto-Dismiss System -->
<script src="{{ asset('js/ateka-alerts.js') }}?v=1.0"></script>
<!-- Global Scrollable Period Select System -->
<script src="{{ asset('js/ateka-select-scrollable.js') }}?v={{ file_exists(public_path('js/ateka-select-scrollable.js')) ? filemtime(public_path('js/ateka-select-scrollable.js')) : '1.0' }}"></script>


