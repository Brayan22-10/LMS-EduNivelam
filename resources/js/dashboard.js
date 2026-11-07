document.addEventListener("DOMContentLoaded", function() {
    console.log("Panel de administración cargado correctamente.");

    function isMobileView() {
        return window.innerWidth < 992;
    }

    function applySidebarState(isCollapsed) {
        const wrapper = document.querySelector('.dashboard-wrapper');
        const body = document.body;

        if (wrapper) wrapper.classList.toggle('sidebar-collapsed', isCollapsed);
        if (body) body.classList.toggle('sidebar-collapsed', isCollapsed);
    }

    if (!isMobileView() && localStorage.getItem('sidebar_collapsed') === 'true') {
        applySidebarState(true);
    }

    document.addEventListener('click', function(event) {
        const closeBtn = event.target.closest('#sidebarCloseBtn') || event.target.closest('#sidebarBackdrop');
        if (closeBtn) {
            event.preventDefault();
            document.body.classList.remove('sidebar-mobile-open');
            return;
        }

        const toggleBtn = event.target.closest('#sidebarToggle') || event.target.closest('#sidebarToggleFloat');
        if (toggleBtn) {
            event.preventDefault();

            if (isMobileView()) {
                document.body.classList.toggle('sidebar-mobile-open');
            } else {
                const wrapper = document.querySelector('.dashboard-wrapper');
                const body = document.body;

                const currentlyCollapsed = (wrapper && wrapper.classList.contains('sidebar-collapsed')) || body.classList.contains('sidebar-collapsed');
                const newState = !currentlyCollapsed;

                applySidebarState(newState);
                localStorage.setItem('sidebar_collapsed', newState);
            }
            return;
        }

        // Close mobile drawer when clicking a link inside sidebar menu
        if (isMobileView() && event.target.closest('.sidebar-menu a')) {
            document.body.classList.remove('sidebar-mobile-open');
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && document.body.classList.contains('sidebar-mobile-open')) {
            document.body.classList.remove('sidebar-mobile-open');
        }
    });

    window.addEventListener('resize', function() {
        if (!isMobileView() && document.body.classList.contains('sidebar-mobile-open')) {
            document.body.classList.remove('sidebar-mobile-open');
        }
    });

    const btnLight = document.getElementById('btn-light-mode');
    const btnDark = document.getElementById('btn-dark-mode');

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
                            document.body.classList.add('bg-dark-mode');
                            document.body.classList.remove('bg-light');
                            btnDark.classList.add('active');
                            btnLight.classList.remove('active');
                        } else {
                            document.body.classList.remove('bg-dark-mode');
                            document.body.classList.add('bg-light');
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