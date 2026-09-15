document.addEventListener('DOMContentLoaded', function() {
    const dynamicMsg = document.getElementById('welcome-dynamic-msg');

    if (dynamicMsg) {
        const messages = [
            "¡Prepárate para el éxito académico!",
            "Nivelación experta en matemáticas y lectura",
            "Desarrolla tus habilidades blandas hoy ",
            "¡Conéctate con tu futuro universitario! "
        ];

        let index = 0;

        setInterval(() => {
            index = (index + 1) % messages.length;
            dynamicMsg.style.opacity = 0;
            setTimeout(() => {
                dynamicMsg.textContent = messages[index];
                dynamicMsg.style.opacity = 1;
            }, 250);
        }, 4000);

        dynamicMsg.style.transition = 'opacity 0.25s ease-in-out';
    }
});