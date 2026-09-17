document.addEventListener('DOMContentLoaded', function() {
    console.log("EduNivelam - Módulo About cargado correctamente.");

    const valueBoxes = document.querySelectorAll('.value-box');
    valueBoxes.forEach((box, index) => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px)';
        box.style.transition = `all 0.4s ease ${index * 0.1}🇸`;

        setTimeout(() => {
            box.style.opacity = '1';
            box.style.transform = 'translateY(0)';
        }, 150);
    });
});