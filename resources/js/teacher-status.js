document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('statusForm');
    if (!form) return;

    const dni = document.getElementById('dni');
    const submit = document.getElementById('statusSubmit');

    if (dni) {
        dni.addEventListener('input', function() {
            dni.value = dni.value.replace(/\D+/g, '');
        });
    }

    form.addEventListener('submit', function(e) {
        if (submit && submit.disabled) {
            e.preventDefault();
            return;
        }
        if (submit) {
            submit.disabled = true;
            submit.firstElementChild.textContent = 'Consultando…';
        }
    });

    window.addEventListener('pageshow', function(e) {
        if (e.persisted && submit) {
            submit.disabled = false;
            submit.firstElementChild.textContent = 'Consultar';
        }
    });
});