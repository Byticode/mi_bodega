<script>
    // Submenú de configuración
    const configBtn = document.getElementById('configBtn');
    const configMenu = document.getElementById('configMenu');
    const configArrow = document.getElementById('configArrow');

    configBtn.addEventListener('click', () => {
      const open = configMenu.classList.toggle('hidden');
      configArrow.classList.toggle('rotate-180');
      configBtn.setAttribute('aria-expanded', String(!open));
    });

    // Sidebar off-canvas en móvil
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('sidebarOpenBtn');

    function setSidebar(open) {
      sidebar.classList.toggle('-translate-x-full', !open);
      overlay.classList.toggle('hidden', !open);
      openBtn.classList.toggle('hidden', open);
    }

    openBtn.addEventListener('click', () => setSidebar(true));
    overlay.addEventListener('click', () => setSidebar(false));
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') setSidebar(false);
    });
</script>
