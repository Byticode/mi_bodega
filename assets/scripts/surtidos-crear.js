let rowCount = 0;

function agregarProducto() {
    const container = document.getElementById('productosContainer');
    const firstRow = container.querySelector('.producto-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelector('.producto-select').value = '';
    newRow.querySelector('.producto-cantidad').value = '';
    newRow.querySelector('.producto-precio').value = '';

    const select = newRow.querySelector('.producto-select');
    const cantidad = newRow.querySelector('.producto-cantidad');
    const precio = newRow.querySelector('.producto-precio');

    select.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        if (option && option.dataset.precio) {
            precio.placeholder = 'Precio: ' + option.dataset.precio;
        }
        calcularTotal();
    });

    cantidad.addEventListener('input', calcularTotal);
    precio.addEventListener('input', calcularTotal);

    const rows = container.querySelectorAll('.producto-row');
    if (rows.length >= 1) {
        container.querySelectorAll('.producto-row:last-child .text-rose-600').forEach(function (btn) {
            btn.style.display = 'block';
        });
    }

    container.appendChild(newRow);
    rowCount++;
    calcularTotal();
}

function eliminarProducto(btn) {
    const container = document.getElementById('productosContainer');
    const rows = container.querySelectorAll('.producto-row');
    if (rows.length <= 1) {
        alert('Debe haber al menos un producto');
        return;
    }
    btn.closest('.producto-row').remove();
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('.producto-row').forEach(function (row) {
        const cantidad = parseInt(row.querySelector('.producto-cantidad').value) || 0;
        const precio = parseFloat(row.querySelector('.producto-precio').value) || 0;
        total += cantidad * precio;
    });
    document.getElementById('totalCosto').textContent = 'Bs. ' + total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function () {
    const firstRow = document.querySelector('.producto-row');
    if (firstRow) {
        const select = firstRow.querySelector('.producto-select');
        const cantidad = firstRow.querySelector('.producto-cantidad');
        const precio = firstRow.querySelector('.producto-precio');

        select.addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            if (option && option.dataset.precio) {
                precio.placeholder = 'Precio: ' + option.dataset.precio;
            }
            calcularTotal();
        });

        cantidad.addEventListener('input', calcularTotal);
        precio.addEventListener('input', calcularTotal);

        const firstDeleteBtn = document.querySelector('.producto-row .text-rose-600');
        if (firstDeleteBtn) firstDeleteBtn.style.display = 'none';
    }

    const surtidoForm = document.getElementById('surtidoForm');
    if (surtidoForm) {
        surtidoForm.addEventListener('submit', function (e) {
            let valid = true;
            document.querySelectorAll('.producto-row').forEach(function (row) {
                const producto = row.querySelector('.producto-select').value;
                const cantidad = row.querySelector('.producto-cantidad').value;
                const precio = row.querySelector('.producto-precio').value;
                if (!producto || !cantidad || !precio) valid = false;
            });
            if (!valid) {
                e.preventDefault();
                alert('Complete todos los campos de los productos');
            }
        });
    }
});
