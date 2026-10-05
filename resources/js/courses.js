document.addEventListener('DOMContentLoaded', function() {
    console.log("EduNivelam - Catálogo de Cursos cargado correctamente.");

    const courseCards = document.querySelectorAll('.course-item-card');
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.backgroundColor = '#fcfdff';
        });
        card.addEventListener('mouseleave', () => {
            card.style.backgroundColor = '#ffffff';
        });
    });
});