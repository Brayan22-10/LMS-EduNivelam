document.addEventListener('DOMContentLoaded', function() {
    console.log("EduNivelam - Módulo de Instructores cargado correctamente.");

    const cards = document.querySelectorAll('.instructor-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.borderColor = '#0d6efd';
        });
        card.addEventListener('mouseleave', () => {
            card.style.borderColor = 'transparent';
        });
    });
});