document.addEventListener("DOMContentLoaded", function() {
    console.log("Panel de administración de usuarios y configuración cargado correctamente.");

    const btnLight = document.getElementById('btn-light-mode');
    const btnDark = document.getElementById('btn-dark-mode');
    const body = document.body;

    if (btnLight && btnDark) {
        function updateThemeServer(isDark) {
            fetch('/settings/toggle-theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ high_contrast: isDark })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.high_contrast) {
                            body.classList.add('bg-dark-mode');
                            body.classList.remove('bg-light');
                            btnDark.classList.add('active');
                            btnLight.classList.remove('active');
                        } else {
                            body.classList.remove('bg-dark-mode');
                            body.classList.add('bg-light');
                            btnLight.classList.add('active');
                            btnDark.classList.remove('active');
                        }
                    }
                })
                .catch(error => console.error('Error al actualizar el tema:', error));
        }

        btnLight.addEventListener('click', function() {
            updateThemeServer(false);
        });

        btnDark.addEventListener('click', function() {
            updateThemeServer(true);
        });
    }
});