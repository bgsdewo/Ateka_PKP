<!-- Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/libs.min.js') }}"></script>
<!-- External Library Bundle Script -->
<script src="{{ asset('hope-ui/assets/js/core/external.min.js') }}" defer></script>
<!-- Settings Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/setting.js') }}" defer></script>
<!-- Slider-tab Script -->
<script src="{{ asset('hope-ui/assets/js/plugins/slider-tabs.js') }}" defer></script>
<!-- SweetAlert2 Plugin -->
<script src="{{ asset('hope-ui/assets/js/plugins/sweetalert2.all.min.js') }}" defer></script>

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
<script src="{{ asset('hope-ui/assets/js/hope-ui.js') }}?v={{ file_exists(public_path('hope-ui/assets/js/hope-ui.js')) ? filemtime(public_path('hope-ui/assets/js/hope-ui.js')) : '2.1' }}" defer></script>
<!-- Global Alert Dismiss & Auto-Dismiss System -->
<script src="{{ asset('js/ateka-alerts.js') }}?v=1.0" defer></script>
<!-- Global Scrollable Period Select System -->
<script defer src="{{ asset('js/ateka-select-scrollable.js') }}?v={{ file_exists(public_path('js/ateka-select-scrollable.js')) ? filemtime(public_path('js/ateka-select-scrollable.js')) : '1.0' }}"></script>

<!-- Sidebar mobile: backdrop, tutup saat klik di luar / pilih menu -->
<script>
(function () {
    var sb = document.querySelector('.sidebar');
    if (!sb) return;
    var BP = 1200;
    function sync() {
        var open = window.innerWidth < BP && !sb.classList.contains('sidebar-mini');
        document.body.classList.toggle('ateka-sidebar-open', open);
    }
    function closeIfMobile() {
        if (window.innerWidth < BP && !sb.classList.contains('sidebar-mini')) sb.classList.add('sidebar-mini');
    }
    new MutationObserver(sync).observe(sb, { attributes: true, attributeFilter: ['class'] });
    var wasMobile = window.innerWidth < BP;
    window.addEventListener('resize', function () {
        var isMobile = window.innerWidth < BP;
        if (isMobile && !wasMobile) closeIfMobile();
        wasMobile = isMobile;
        sync();
    });
    document.addEventListener('click', function (e) {
        if (window.innerWidth >= BP || sb.classList.contains('sidebar-mini')) return;
        if (e.target.closest('.sidebar') || e.target.closest('[data-toggle="sidebar"]')) return;
        closeIfMobile();
    });
    sb.addEventListener('click', function (e) {
        var a = e.target.closest('a.nav-link');
        if (a && !a.hasAttribute('data-bs-toggle') && a.getAttribute('href') && a.getAttribute('href') !== '#') closeIfMobile();
    });
    closeIfMobile();
    sync();
})();
</script>
