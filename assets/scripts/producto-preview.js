document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.querySelector('input[name="nombre"]');
    const pesoInput = document.querySelector('input[name="peso"]');
    const unidadSelect = document.querySelector('select[name="unidad"]');
    const previewElement = document.getElementById('previewNombre');

    if (!nombreInput || !pesoInput || !unidadSelect || !previewElement) return;

    function updatePreview() {
        let nombre = nombreInput.value.trim() || 'Nombre';
        const peso = pesoInput.value.trim();
        const unidadText = unidadSelect.options[unidadSelect.selectedIndex]?.text || '';
        const abreviatura = unidadText.match(/\(([^)]+)\)/);
        const unidadAbrev = abreviatura ? abreviatura[1] : '';

        if (nombre) {
            nombre = nombre.charAt(0).toUpperCase() + nombre.slice(1);
        }

        let preview = nombre;
        if (peso && unidadAbrev) {
            const pesoNum = parseFloat(peso);
            const pesoFormateado = Number.isInteger(pesoNum) ? pesoNum : pesoNum.toFixed(2);
            preview = nombre + ' ' + pesoFormateado + unidadAbrev;
        } else if (peso) {
            preview = nombre + ' ' + peso;
        }

        previewElement.textContent = preview || 'Nombre del producto';
    }

    nombreInput.addEventListener('input', updatePreview);
    pesoInput.addEventListener('input', updatePreview);
    unidadSelect.addEventListener('change', updatePreview);
    updatePreview();
});
