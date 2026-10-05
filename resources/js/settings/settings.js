document.addEventListener("DOMContentLoaded", function() {
    const fontSizeSelector = document.getElementById('fontSizeSelector');
    const highContrastToggle = document.getElementById('highContrastToggle');
    const saveAppearanceBtn = document.getElementById('saveAppearanceBtn');

    if (!fontSizeSelector || !highContrastToggle || !saveAppearanceBtn) return;

    if (localStorage.getItem('user_font_size')) {
        document.body.style.fontSize = localStorage.getItem('user_font_size');
        fontSizeSelector.value = localStorage.getItem('user_font_size');
    }

    if (localStorage.getItem('user_high_contrast') === 'true') {
        document.body.classList.add('bg-black', 'text-white');
        highContrastToggle.checked = true;
    }

    saveAppearanceBtn.addEventListener('click', function() {
        const selectedSize = fontSizeSelector.value;
        const isContrastActive = highContrastToggle.checked;

        document.body.style.fontSize = selectedSize;
        localStorage.setItem('user_font_size', selectedSize);

        if (isContrastActive) {
            document.body.classList.add('bg-black', 'text-white');
            localStorage.setItem('user_high_contrast', 'true');
        } else {
            document.body.classList.remove('bg-black', 'text-white');
            localStorage.setItem('user_high_contrast', 'false');
        }

        alert('¡Preferencias visuales aplicadas correctamente!');
    });
});