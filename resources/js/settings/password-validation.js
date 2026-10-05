document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const progressBar = document.getElementById('password-strength-bar');
    const progressText = document.getElementById('password-strength-text');
    const matchStatus = document.getElementById('match-status');

    if (!passwordInput) return;

    const formElement = document.getElementById('passwordUpdateForm');
    const userName = formElement ? formElement.getAttribute('data-username').toLowerCase() : '';

    const reqLength = document.getElementById('req-length');
    const reqSpace = document.getElementById('req-space');
    const reqLetter = document.getElementById('req-letter');
    const reqNumber = document.getElementById('req-number');
    const reqSymbol = document.getElementById('req-symbol');
    const reqName = document.getElementById('req-name');

    const texts = {
        length: "Mínimo 8 caracteres",
        space: "Sin espacios en blanco",
        letter: "Al menos una letra (mayúscula o minúscula)",
        number: "Al menos un número",
        symbol: "Al menos un símbolo especial (@$!%*?&)",
        name: "No debe contener tu nombre de usuario (" + formElement.getAttribute('data-username') + ")"
    };

    function updateRequirement(element, condition, textKey) {
        if (!element) return;
        const iconClass = condition ? "bi-check-circle-fill text-success" : "bi-x-circle-fill text-danger";
        const textColor = condition ? "text-success mb-1" : "text-danger mb-1";

        element.className = textColor;
        element.innerHTML = `<i class="bi ${iconClass} me-1"></i> ${texts[textKey]}`;
    }

    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        let score = 0;

        const hasLength = val.length >= 8;
        const hasNoSpace = !/\s/.test(val) && val.length > 0;
        const hasLetter = /[a-zA-Z]/.test(val);
        const hasNumber = /[0-9]/.test(val);
        const hasSymbol = /[\W_]/.test(val);
        const notContainName = val.length === 0 || !val.toLowerCase().includes(userName);

        updateRequirement(reqLength, hasLength, 'length');
        updateRequirement(reqSpace, hasNoSpace, 'space');
        updateRequirement(reqLetter, hasLetter, 'letter');
        updateRequirement(reqNumber, hasNumber, 'number');
        updateRequirement(reqSymbol, hasSymbol, 'symbol');
        updateRequirement(reqName, notContainName, 'name');

        if (hasLength) score++;
        if (hasNoSpace) score++;
        if (hasLetter) score++;
        if (hasNumber) score++;
        if (hasSymbol) score++;
        if (notContainName) score++;

        if (progressBar) {
            const percentage = (score / 6) * 100;
            progressBar.style.width = percentage + '%';

            if (score <= 2) {
                progressBar.className = "progress-bar bg-danger";
                progressText.textContent = "Seguridad: Débil";
            } else if (score <= 4) {
                progressBar.className = "progress-bar bg-warning";
                progressText.textContent = "Seguridad: Media";
            } else {
                progressBar.className = "progress-bar bg-success";
                progressText.textContent = "Seguridad: Alta y Robusta";
            }
        }
    });

    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            if (confirmInput.value === passwordInput.value && passwordInput.value.length > 0) {
                matchStatus.className = "text-success fw-bold";
                matchStatus.textContent = "¡Las contraseñas coinciden perfectamente!";
            } else {
                matchStatus.className = "text-danger";
                matchStatus.textContent = "Las contraseñas no coinciden.";
            }
        });
    }
});