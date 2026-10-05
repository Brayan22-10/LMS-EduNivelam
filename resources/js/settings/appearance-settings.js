document.addEventListener("DOMContentLoaded", function() {
    const fontSizeSelector = document.getElementById('font_size');
    const highContrastToggle = document.getElementById('high_contrast');
    if (fontSizeSelector) {
        fontSizeSelector.addEventListener('change', function() {
            document.body.style.fontSize = fontSizeSelector.value;
        });
    }

    if (highContrastToggle) {
        highContrastToggle.addEventListener('change', function() {
            if (highContrastToggle.checked) {
                document.body.classList.add('bg-dark-mode', 'text-light');
            } else {
                document.body.classList.remove('bg-dark-mode', 'text-light');
            }
        });
    }
});