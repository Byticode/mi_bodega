<script>
    const configBtn = document.getElementById('configBtn');
    const configMenu = document.getElementById('configMenu');
    const configArrow = document.getElementById('configArrow');

    configBtn.addEventListener('click', () => {
      configMenu.classList.toggle('hidden');
      configArrow.classList.toggle('rotate-180');
    });
</script>