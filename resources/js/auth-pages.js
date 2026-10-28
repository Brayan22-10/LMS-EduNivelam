document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('form.js-once').forEach(function(form) {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    });

    document.querySelectorAll('[data-digits-only]').forEach(function(input) {
        const max = parseInt(input.getAttribute('maxlength'), 10) || 6;
        input.addEventListener('input', function() {
            input.value = input.value.replace(/\D/g, '').slice(0, max);
        });
    });

    document.querySelectorAll('[data-toggle-pass]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const input = document.getElementById(btn.getAttribute('data-toggle-pass'));
            const icon = btn.querySelector('i');
            if (!input || !icon) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('bi-eye', show);
            icon.classList.toggle('bi-eye-slash', !show);
            input.focus();
        });
    });

    const pass = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    if (pass && confirmInput && document.getElementById('rule-len')) {
        const setRule = function(id, ok) {
            const li = document.getElementById(id);
            if (!li) return;
            const icon = li.querySelector('i');
            li.classList.toggle('is-ok', ok);
            if (icon) {
                icon.classList.toggle('bi-check-circle-fill', ok);
                icon.classList.toggle('bi-circle', !ok);
            }
        };

        const checkRules = function() {
            const v = pass.value;
            setRule('rule-len', v.length >= 8);
            setRule('rule-space', v.length > 0 && !/\s/.test(v));
            setRule('rule-match', v.length > 0 && v === confirmInput.value);
        };

        pass.addEventListener('input', checkRules);
        confirmInput.addEventListener('input', checkRules);
    }

    const verify = document.querySelector('[data-verify-email]');

    if (verify) {
        const statusUrl = verify.getAttribute('data-status-url');
        const homeUrl = verify.getAttribute('data-home-url');

        setInterval(function() {
            if (document.hidden) return;

            fetch(statusUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) {
                    if (response.ok) return response.json();
                    throw new Error('Pendiente');
                })
                .then(function(data) {
                    if (data.status === 'verified') {
                        window.location.href = homeUrl;
                    }
                })
                .catch(function() { /* sigue esperando */ });
        }, 3000);
    }
});