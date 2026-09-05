document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('lockout-modal');
    const closeBtn = document.getElementById('close-modal-btn');
    const loginContainer = document.getElementById('login-container');

    const isCurrentlyLocked = loginContainer ? loginContainer.getAttribute('data-is-locked') === 'true' : false;
    let timeLeft = loginContainer ? parseInt(loginContainer.getAttribute('data-seconds-remaining'), 10) : 0;

    if (sessionStorage.getItem('closed_lockout_modal') === 'true') {
        if (modal) modal.style.display = 'none';
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (modal) {
                modal.style.display = 'none';
            }
            sessionStorage.setItem('closed_lockout_modal', 'true');
        });
    }

    if (isCurrentlyLocked && timeLeft > 0) {
        const timerElement = document.getElementById('countdown-timer');

        const countdown = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                sessionStorage.removeItem('closed_lockout_modal');
                window.location.reload();
                return;
            }

            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;

            if (timerElement) {
                timerElement.textContent =
                    (minutes < 10 ? "0" : "") + minutes + ":" +
                    (seconds < 10 ? "0" : "") + seconds;
            }

            timeLeft--;
        }, 1000);
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            if (isCurrentlyLocked) {
                if (modal) {
                    modal.style.display = 'flex';
                    sessionStorage.removeItem('closed_lockout_modal');
                }
                event.preventDefault();
            }
        });
    }
});