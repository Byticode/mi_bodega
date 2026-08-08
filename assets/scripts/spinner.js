function showSpinner() {
    const spinner = document.getElementById('globalSpinner');
    if (spinner) {
        spinner.classList.remove('opacity-0', 'pointer-events-none', 'scale-95');
        spinner.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
    }
}

function hideSpinner() {
    const spinner = document.getElementById('globalSpinner');
    if (spinner) {
        spinner.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
        spinner.classList.add('opacity-0', 'pointer-events-none', 'scale-95');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }
            showSpinner();
        });
    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            hideSpinner();
        }
    });
});