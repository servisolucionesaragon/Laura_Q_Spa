/* TPV Estética y SPA — JS principal */
(function () {
    'use strict';

    // Toggle sidebar en mobile
    const toggleBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.spa-sidebar');
    const overlay = document.querySelector('.spa-overlay');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    // Tabs simples
    document.querySelectorAll('.spa-tabs').forEach((tabsEl) => {
        const tabs = tabsEl.querySelectorAll('.tab');
        const target = tabsEl.dataset.target;
        const panes = target ? document.querySelectorAll(`${target} .tab-pane`) : [];
        tabs.forEach((tab) => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                tabs.forEach((t) => t.classList.remove('active'));
                panes.forEach((p) => p.classList.remove('active'));
                tab.classList.add('active');
                const paneId = tab.dataset.pane;
                if (paneId) {
                    const pane = document.getElementById(paneId);
                    if (pane) pane.classList.add('active');
                }
            });
        });
    });

    // Auto-cerrar alertas
    document.querySelectorAll('.alert[data-autohide]').forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });

    // Preview de logo
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => { logoPreview.src = ev.target.result; };
            reader.readAsDataURL(file);
        });
    }
})();
