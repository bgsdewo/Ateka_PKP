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
            // Responsif: bungkus tabel agar bisa digeser horizontal di layar sempit
            var $tbl = $($api.table().node());
            if (!$tbl.parent().hasClass('table-responsive')) {
                $tbl.wrap('<div class="table-responsive"></div>');
            }
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

<!-- Pagination ringan sisi-klien: <div data-page-size="3"> berisi tabel -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-page-size]').forEach(function (box) {
        var size = parseInt(box.getAttribute('data-page-size'), 10) || 3;
        var rows = Array.prototype.slice.call(box.querySelectorAll('tbody > tr'));
        if (rows.length <= size) return;
        var pages = Math.ceil(rows.length / size), cur = 1;
        var bar = document.createElement('div');
        bar.className = 'ateka-pager';
        bar.innerHTML = '<span class="ateka-pager-info"></span><div class="ateka-pager-btns">' +
            '<button type="button" data-d="-1" aria-label="Halaman sebelumnya">&lsaquo; Sebelumnya</button>' +
            '<button type="button" data-d="1" aria-label="Halaman berikutnya">Berikutnya &rsaquo;</button></div>';
        box.appendChild(bar);
        var info = bar.querySelector('.ateka-pager-info'), btns = bar.querySelectorAll('button');
        function render() {
            rows.forEach(function (r, i) { r.style.display = (i >= (cur - 1) * size && i < cur * size) ? '' : 'none'; });
            var a = (cur - 1) * size + 1, b = Math.min(cur * size, rows.length);
            info.textContent = a + '-' + b + ' dari ' + rows.length + ' \u2022 Hal. ' + cur + '/' + pages;
            btns[0].disabled = cur === 1; btns[1].disabled = cur === pages;
        }
        bar.addEventListener('click', function (e) {
            var btn = e.target.closest('button'); if (!btn || btn.disabled) return;
            cur = Math.min(pages, Math.max(1, cur + parseInt(btn.getAttribute('data-d'), 10))); render();
        });
        render();
    });
});
</script>

<!-- HP: naikkan teks yang terlalu kecil (<11px) di tabel & badge agar terbaca -->
<script>
(function () {
    var mq = window.matchMedia('(max-width: 767.98px)');
    function bump(root) {
        if (!mq.matches) return;
        (root || document).querySelectorAll('td, th, td *, th *, .badge, small').forEach(function (el) {
            if (!el.textContent || !el.textContent.trim()) return;
            if (parseFloat(getComputedStyle(el).fontSize) < 11) el.style.setProperty('font-size', '11px', 'important');
        });
    }
    document.addEventListener('DOMContentLoaded', function () { bump(); setTimeout(bump, 600); });
    if (window.jQuery) { jQuery(document).on('draw.dt init.dt', function () { bump(); }); }
})();
</script>
