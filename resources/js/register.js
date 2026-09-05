document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('error-modal');
    const closeBtn = document.getElementById('close-error-modal');

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    const passwordInput = document.getElementById('password');
    const nameInput = document.getElementById('name');
    const submitBtn = document.getElementById('submit-btn');

    const critLength = document.getElementById('crit-length');
    const critUppercase = document.getElementById('crit-uppercase');
    const critSymbol = document.getElementById('crit-symbol');
    const critNumber = document.getElementById('crit-number');
    const critName = document.getElementById('crit-name');
    const strengthLabel = document.getElementById('strength-label');

    function validatePassword() {
        const val = passwordInput.value;
        const nameVal = nameInput ? nameInput.value.trim().toLowerCase() : '';

        const hasLength = val.length >= 8;
        updateItem(critLength, hasLength, 'Mínimo 8 caracteres');

        const hasUppercase = /[A-Z]/.test(val);
        updateItem(critUppercase, hasUppercase, 'Al menos una mayúscula');

        const hasSymbol = /[^A-Za-z0-9]/.test(val);
        updateItem(critSymbol, hasSymbol, 'Al menos un símbolo (ej: @, #, $, !)');

        const hasNumber = /[0-9]/.test(val);
        updateItem(critNumber, hasNumber, 'Al menos un número');

        let notRelatedToName = true;
        if (nameVal.length > 0 && val.toLowerCase().includes(nameVal)) {
            notRelatedToName = false;
        }
        const isValidNameCheck = notRelatedToName && val.length > 0 && nameVal.length > 0;
        updateItem(critName, isValidNameCheck, 'No puede contener el nombre de la cuenta');

        let passedCount = [hasLength, hasUppercase, hasSymbol, hasNumber, isValidNameCheck].filter(Boolean).length;

        if (val.length === 0 || passedCount <= 2) {
            strengthLabel.textContent = 'Débil';
            strengthLabel.className = 'text-danger fw-bold';
        } else if (passedCount === 3 || passedCount === 4) {
            strengthLabel.textContent = 'Media';
            strengthLabel.className = 'text-warning fw-bold';
        } else if (passedCount === 5) {
            strengthLabel.textContent = 'Fuerte';
            strengthLabel.className = 'text-success fw-bold';
        }

        if (hasLength && hasUppercase && hasSymbol && hasNumber && isValidNameCheck) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'disabled');
        }
    }

    function updateItem(element, isValid, text) {
        if (isValid) {
            element.textContent = '✓ ' + text;
            element.className = 'text-success';
        } else {
            element.textContent = '✗ ' + text;
            element.className = 'text-danger';
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', validatePassword);
        if (nameInput) {
            nameInput.addEventListener('input', validatePassword);
        }
    }
});