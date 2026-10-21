/**
 * ==============================================================================
 * ATEKA SCROLLABLE SELECT SYSTEM (Filter Periode & Katalog Enhancer)
 * Kementerian Perumahan dan Kawasan Permukiman RI (KEMEN PKP RI)
 * Mengubah select native dengan opsi banyak menjadi compact dropdown berbatas
 * tinggi (max-height: 220px) dengan custom slim scrollbar yang rapi & elegan.
 * ==============================================================================
 */

(function () {
    'use strict';

    function initScrollableSelect(select) {
        if (!select || select.dataset.atekaScrollInit === 'true') return;

        // Tandai sudah di-inisialisasi agar tidak duplikat
        select.dataset.atekaScrollInit = 'true';

        // Sembunyikan select native tetapi biarkan di DOM agar submit form tetap berjalan
        select.style.setProperty('display', 'none', 'important');

        // Buat wrapper dropdown
        const wrapper = document.createElement('div');
        wrapper.className = 'dropdown ateka-scroll-select-wrapper';

        // Salin properti layout inline style dari select
        const isInInputGroup = !!select.closest('.input-group');
        if (select.style.width) {
            wrapper.style.width = select.style.width;
        } else if (!isInInputGroup && (select.classList.contains('form-select') || select.classList.contains('w-100') || select.classList.contains('select-scrollable'))) {
            wrapper.style.width = '100%';
        }

        if (select.style.minWidth) wrapper.style.minWidth = select.style.minWidth;
        if (select.style.maxWidth) wrapper.style.maxWidth = select.style.maxWidth;
        if (select.style.flex) wrapper.style.flex = select.style.flex;

        // Kumpulkan class relevan dari select asli
        const originalClasses = Array.from(select.classList)
            .filter(c => c !== 'd-none' && c !== 'hidden' && c !== 'select-scrollable')
            .join(' ');

        // Buat Tombol Trigger Dropdown
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `${originalClasses || 'form-select'} dropdown-toggle ateka-scroll-select-btn`;
        btn.setAttribute('data-bs-toggle', 'dropdown');
        btn.setAttribute('aria-expanded', 'false');
        btn.style.width = '100%';

        // Salin inline visual style ke button
        if (select.style.height) btn.style.height = select.style.height;
        if (select.style.borderColor) btn.style.borderColor = select.style.borderColor;
        if (select.style.borderRadius) btn.style.borderRadius = select.style.borderRadius;
        if (select.style.fontSize) btn.style.fontSize = select.style.fontSize;
        if (select.style.backgroundColor) btn.style.backgroundColor = select.style.backgroundColor;

        // Container Label Teks
        const labelSpan = document.createElement('span');
        labelSpan.className = 'text-truncate me-auto';

        // Chevron Panah SVG
        const arrowSpan = document.createElement('span');
        arrowSpan.className = 'ateka-scroll-select-arrow';
        arrowSpan.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>`;

        btn.appendChild(labelSpan);
        btn.appendChild(arrowSpan);

        // Buat Menu Dropdown
        const menu = document.createElement('ul');
        menu.className = 'dropdown-menu ateka-scroll-select-menu shadow-sm';

        function renderOptions() {
            menu.innerHTML = '';
            const options = Array.from(select.options);
            let selectedText = '';

            options.forEach(opt => {
                const isSelected = opt.selected;
                if (isSelected) {
                    selectedText = opt.text;
                }

                const li = document.createElement('li');
                const a = document.createElement('a');
                a.className = `dropdown-item ateka-scroll-select-item ${isSelected ? 'active' : ''}`;
                a.href = 'javascript:void(0)';
                a.dataset.value = opt.value;
                a.setAttribute('title', opt.text.trim());
                a.innerHTML = `
                    <span class="text-truncate">${opt.text}</span>
                    <span class="ateka-check-icon">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </span>
                `;

                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    const prevValue = select.value;
                    const newValue = opt.value;

                    // Tutup dropdown Bootstrap
                    if (window.bootstrap && bootstrap.Dropdown) {
                        const bsDrop = bootstrap.Dropdown.getInstance(btn) || new bootstrap.Dropdown(btn);
                        bsDrop.hide();
                    }

                    if (prevValue !== newValue) {
                        select.value = newValue;
                        labelSpan.textContent = opt.text;
                        btn.setAttribute('title', opt.text.trim());

                        // Perbarui class active
                        menu.querySelectorAll('.ateka-scroll-select-item').forEach(el => el.classList.remove('active'));
                        a.classList.add('active');

                        // Pemicu event change standar
                        let changeHandled = false;
                        const onSelectChange = () => { changeHandled = true; };
                        select.addEventListener('change', onSelectChange, { once: true });

                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        select.dispatchEvent(new Event('input', { bubbles: true }));

                        // Eksekusi fallback jika inline onchange belum terpanggil
                        if (!changeHandled && typeof select.onchange === 'function') {
                            select.onchange();
                        }
                    }
                });

                li.appendChild(a);
                menu.appendChild(li);
            });

            if (!selectedText && options.length > 0) {
                selectedText = options[0].text;
            }
            labelSpan.textContent = selectedText;
            btn.setAttribute('title', selectedText.trim());
        }

        renderOptions();

        // Masukkan elemen ke DOM tepat setelah select
        select.parentNode.insertBefore(wrapper, select.nextSibling);
        wrapper.appendChild(btn);
        wrapper.appendChild(menu);

        // Auto-scroll ke opsi aktif saat menu dropdown dibuka
        btn.addEventListener('shown.bs.dropdown', function () {
            const activeItem = menu.querySelector('.ateka-scroll-select-item.active');
            if (activeItem) {
                activeItem.scrollIntoView({ block: 'nearest' });
            }
        });

        // Sinkronisasi jika select diubah secara programmatic
        select.addEventListener('change', function () {
            const currentSelected = select.options[select.selectedIndex];
            if (currentSelected) {
                labelSpan.textContent = currentSelected.text;
                btn.setAttribute('title', currentSelected.text.trim());
                menu.querySelectorAll('.ateka-scroll-select-item').forEach(el => {
                    if (el.dataset.value === currentSelected.value) {
                        el.classList.add('active');
                    } else {
                        el.classList.remove('active');
                    }
                });
            }
        });
    }

    function initAllScrollableSelects() {
        const selector = [
            'select[name="month"]',
            'select[name="year"]',
            'select[name="category_id"]',
            'select[name="stock_status"]',
            '#katalog-filter-form select',
            'select.select-scrollable',
            'select[data-scrollable="true"]'
        ].join(', ');
        document.querySelectorAll(selector).forEach(initScrollableSelect);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllScrollableSelects);
    } else {
        initAllScrollableSelects();
    }

    window.initAtekaScrollableSelects = initAllScrollableSelects;
})();
