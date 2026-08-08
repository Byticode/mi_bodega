document.addEventListener('DOMContentLoaded', function () {
    const configBtn = document.getElementById('configBtn');
    const configMenu = document.getElementById('configMenu');
    const configArrow = document.getElementById('configArrow');

    if (configBtn && configMenu && configArrow) {
        configBtn.addEventListener('click', () => {
            const open = configMenu.classList.toggle('hidden');
            configArrow.classList.toggle('rotate-180');
            configBtn.setAttribute('aria-expanded', String(!open));
        });
    }

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('sidebarOpenBtn');

    function setSidebar(open) {
        if (sidebar) sidebar.classList.toggle('-translate-x-full', !open);
        if (overlay) overlay.classList.toggle('hidden', !open);
        if (openBtn) openBtn.classList.toggle('hidden', open);
    }

    if (openBtn) openBtn.addEventListener('click', () => setSidebar(true));
    if (overlay) overlay.addEventListener('click', () => setSidebar(false));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setSidebar(false);
    });
});
