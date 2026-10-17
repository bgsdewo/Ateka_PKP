/**
 * Ateka Modern Alert System (GovTech 2026)
 * - Guaranteed instant dismiss on close button click across all pages
 * - Smooth cubic-bezier exit transitions
 * - Smart auto-dismiss for informational & success notifications
 * - Preservation of validation errors so users have time to read
 */
(function() {
    'use strict';

    function dismissAlertElement(alertEl) {
        if (!alertEl || !alertEl.parentNode) return;

        // Dispatch Bootstrap standard event if needed
        const closeEvent = new CustomEvent('close.bs.alert', { bubbles: true, cancelable: true });
        alertEl.dispatchEvent(closeEvent);
        if (closeEvent.defaultPrevented) return;

        alertEl.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), max-height 0.25s ease, margin 0.25s ease, padding 0.25s ease';
        alertEl.style.pointerEvents = 'none';
        alertEl.style.opacity = '0';
        alertEl.style.transform = 'translateY(-10px) scale(0.98)';

        setTimeout(function() {
            if (alertEl && alertEl.parentNode) {
                const closedEvent = new CustomEvent('closed.bs.alert', { bubbles: true });
                alertEl.dispatchEvent(closedEvent);
                alertEl.remove();
            }
        }, 260);
    }

    // 1. Capture-phase global click delegation for any alert close button
    document.addEventListener('click', function(e) {
        const closeBtn = e.target.closest('[data-bs-dismiss="alert"], .alert .btn-close, .btn-close-toast');
        if (closeBtn) {
            const alertEl = closeBtn.closest('.alert, .modern-floating-toast');
            if (alertEl) {
                e.preventDefault();
                e.stopPropagation();
                dismissAlertElement(alertEl);
            }
        }
    }, true);

    // 2. Auto-dismiss handler for success & informational alerts after 6 seconds
    function setupAutoDismiss() {
        const dismissibleAlerts = document.querySelectorAll('.alert.alert-dismissible, .modern-floating-toast');
        dismissibleAlerts.forEach(function(alertEl) {
            // Keep error lists visible so users don't miss form validation errors
            if (alertEl.classList.contains('alert-danger') && (alertEl.querySelector('ul') || alertEl.querySelector('ol'))) {
                return;
            }

            let timeoutId = setTimeout(function() {
                dismissAlertElement(alertEl);
            }, 6000);

            // Pause on hover, resume on mouse leave
            alertEl.addEventListener('mouseenter', function() {
                clearTimeout(timeoutId);
            });
            alertEl.addEventListener('mouseleave', function() {
                timeoutId = setTimeout(function() {
                    dismissAlertElement(alertEl);
                }, 3000);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupAutoDismiss);
    } else {
        setupAutoDismiss();
    }
})();
