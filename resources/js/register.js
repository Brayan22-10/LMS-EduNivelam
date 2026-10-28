document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('error-modal');
    const closeBtn = document.getElementById('close-error-modal');

    function closeModal() {
        if (modal) modal.style.display = 'none';
    }

    if (modal) {
        if (closeBtn) closeBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });
    }

    document.querySelectorAll('[data-digits-only]').forEach(function(input) {
        input.addEventListener('input', function() {
            input.value = input.value.replace(/\D/g, '');
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

    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const nameInput = document.getElementById('name');
    const submitBtn = document.getElementById('submit-btn');
    const meter = document.getElementById('pw-meter');
    const strengthLabel = document.getElementById('strength-label');
    const matchHint = document.getElementById('pw-match');

    if (passwordInput && meter && submitBtn) {
        const bars = meter.querySelectorAll('.pw-bars span');
        const criteria = {};
        meter.querySelectorAll('[data-crit]').forEach(function(li) {
            criteria[li.getAttribute('data-crit')] = li;
        });

        function setCriterion(key, ok) {
            const li = criteria[key];
            if (!li) return;
            const icon = li.querySelector('i');
            li.classList.toggle('is-ok', ok);
            if (icon) {
                icon.classList.toggle('bi-check-circle-fill', ok);
                icon.classList.toggle('bi-x-circle', !ok);
            }
        }

        function validatePassword() {
            const val = passwordInput.value;
            const nameVal = nameInput ? nameInput.value.trim().toLowerCase() : '';

            const hasLength = val.length >= 8;
            const hasUppercase = /[A-Z]/.test(val);
            const hasSymbol = /[^A-Za-z0-9]/.test(val);
            const hasNumber = /[0-9]/.test(val);

            let notRelatedToName = true;
            if (nameVal.length > 0 && val.toLowerCase().includes(nameVal)) {
                notRelatedToName = false;
            }
            const isValidName = notRelatedToName && val.length > 0 && nameVal.length > 0;

            setCriterion('length', hasLength);
            setCriterion('uppercase', hasUppercase);
            setCriterion('symbol', hasSymbol);
            setCriterion('number', hasNumber);
            setCriterion('name', isValidName);

            const passed = [hasLength, hasUppercase, hasSymbol, hasNumber, isValidName].filter(Boolean).length;

            let level = 'weak';
            let text = 'Débil';
            if (val.length > 0 && passed >= 5) {
                level = 'strong';
                text = 'Fuerte';
            } else if (val.length > 0 && passed >= 3) {
                level = 'medium';
                text = 'Media';
            }

            meter.setAttribute('data-level', level);
            if (strengthLabel) strengthLabel.textContent = text;
            bars.forEach(function(bar, i) {
                bar.classList.toggle('on', val.length > 0 && i < passed);
            });

            let matches = false;
            if (confirmInput) {
                const c = confirmInput.value;
                matches = c.length > 0 && c === val;

                if (matchHint) {
                    matchHint.classList.toggle('show', c.length > 0);
                    matchHint.classList.toggle('ok', matches);
                    matchHint.classList.toggle('bad', !matches);

                    const icon = matchHint.querySelector('i');
                    const label = matchHint.querySelector('span');
                    if (icon) {
                        icon.classList.toggle('bi-check-circle-fill', matches);
                        icon.classList.toggle('bi-x-circle-fill', !matches);
                    }
                    if (label) {
                        label.textContent = matches ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
                    }
                }
            }

            const allValid = hasLength && hasUppercase && hasSymbol && hasNumber && isValidName && matches;
            if (allValid) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }
        }

        passwordInput.addEventListener('input', validatePassword);
        if (nameInput) nameInput.addEventListener('input', validatePassword);
        if (confirmInput) confirmInput.addEventListener('input', validatePassword);

        validatePassword();
    }

    const phoneInput = document.getElementById('phone');
    const countryCodeInput = document.getElementById('country_code');
    const teacherForm = document.getElementById('teacher-register-form');

    if (phoneInput && countryCodeInput && teacherForm && typeof window.intlTelInput === 'function') {
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: 'co',
            separateDialCode: true,
            nationalMode: true,
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
        });

        const previous = (countryCodeInput.value || '').replace('+', '');
        if (previous && previous !== '57' && window.intlTelInputGlobals) {
            const match = window.intlTelInputGlobals.getCountryData().find(function(c) {
                return c.dialCode === previous && c.priority === 0;
            });
            if (match) iti.setCountry(match.iso2);
        }

        teacherForm.addEventListener('submit', function() {
            const data = iti.getSelectedCountryData();
            if (data && data.dialCode) {
                countryCodeInput.value = '+' + data.dialCode;
            }
        });
    }
});