document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('lockout-modal');
    const closeBtn = document.getElementById('close-modal-btn');
    const loginContainer = document.getElementById('login-container');
    const loginForm = document.getElementById('login-form');
    const submitBtn = loginForm ? loginForm.querySelector('.auth-submit') : null;

    const isCurrentlyLocked = loginContainer ? loginContainer.getAttribute('data-is-locked') === 'true' : false;
    const secondsRemaining = loginContainer ? parseInt(loginContainer.getAttribute('data-seconds-remaining'), 10) : 0;

    function storeGet(key) {
        try { return sessionStorage.getItem(key); } catch (e) { return null; }
    }

    function storeSet(key, value) {
        try { sessionStorage.setItem(key, value); } catch (e) { /* se ignora */ }
    }

    function storeRemove(key) {
        try { sessionStorage.removeItem(key); } catch (e) { /* se ignora */ }
    }

    if (storeGet('closed_lockout_modal') === 'true' && modal) {
        modal.style.display = 'none';
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (modal) modal.style.display = 'none';
            storeSet('closed_lockout_modal', 'true');
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            modal.style.display = 'none';
            storeSet('closed_lockout_modal', 'true');
        }
    });

    if (isCurrentlyLocked && secondsRemaining > 0) {
        const timerElement = document.getElementById('countdown-timer');
        const endAt = Date.now() + secondsRemaining * 1000;

        function pad(n) {
            return (n < 10 ? '0' : '') + n;
        }

        function tick() {
            const left = Math.max(0, Math.ceil((endAt - Date.now()) / 1000));

            if (timerElement) {
                timerElement.textContent = pad(Math.floor(left / 60)) + ':' + pad(left % 60);
            }

            if (left <= 0) {
                clearInterval(countdown);
                storeRemove('closed_lockout_modal');
                window.location.reload();
            }
        }

        const countdown = setInterval(tick, 1000);
        tick();
    }

    const originalLabel = submitBtn && submitBtn.firstElementChild ?
        submitBtn.firstElementChild.textContent :
        '';

    function setLoading(on) {
        if (!submitBtn) return;
        const label = submitBtn.firstElementChild;
        submitBtn.disabled = on;
        submitBtn.setAttribute('aria-busy', on ? 'true' : 'false');
        if (label) label.textContent = on ? 'Ingresando…' : originalLabel;
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            if (isCurrentlyLocked) {
                event.preventDefault();
                if (modal) {
                    modal.style.display = 'flex';
                    storeRemove('closed_lockout_modal');
                }
                return;
            }

            if (submitBtn && submitBtn.disabled) {
                event.preventDefault();
                return;
            }
            setLoading(true);
        });
    }

    window.addEventListener('pageshow', function(e) {
        if (e.persisted) setLoading(false);
    });

    const togglePasswordBtn = document.getElementById('togglePassword');
    if (togglePasswordBtn) {
        togglePasswordBtn.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (!passwordInput || !toggleIcon) return;

            const showing = passwordInput.type === 'text';
            passwordInput.type = showing ? 'password' : 'text';
            toggleIcon.classList.toggle('bi-eye-slash', showing);
            toggleIcon.classList.toggle('bi-eye', !showing);
            togglePasswordBtn.setAttribute('aria-pressed', showing ? 'false' : 'true');
            passwordInput.focus();
        });
    }

    const googleBtn = document.querySelector('.auth-google');
    if (googleBtn) {
        googleBtn.addEventListener('click', function(e) {
            if (googleBtn.getAttribute('aria-busy') === 'true') {
                e.preventDefault();
                return;
            }
            googleBtn.setAttribute('aria-busy', 'true');
            googleBtn.style.opacity = '0.7';
            googleBtn.style.pointerEvents = 'none';
        });

        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                googleBtn.removeAttribute('aria-busy');
                googleBtn.style.opacity = '';
                googleBtn.style.pointerEvents = '';
            }
        });
    }

    if (loginContainer) {
        const nav = document.querySelector('.floating-navbar, nav.navbar, header.navbar');
        if (nav && window.getComputedStyle(nav).position === 'static') {
            nav.style.position = 'relative';
        }

        function fitLayout() {
            loginContainer.style.marginTop = '0px';
            const top = loginContainer.getBoundingClientRect().top + window.scrollY;
            loginContainer.style.marginTop = (-top) + 'px';
        }

        fitLayout();
        window.addEventListener('resize', fitLayout);
        window.addEventListener('load', fitLayout);
    }
});