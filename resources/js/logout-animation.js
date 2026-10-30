document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('logoutScene');
    if (!overlay) return;

    let timers = [];
    let pending = null;

    function later(fn, ms) {
        timers.push(setTimeout(fn, ms));
    }

    function clearTimers() {
        timers.forEach(clearTimeout);
        timers = [];
    }

    function stage(name) {
        overlay.setAttribute('data-stage', name);
    }

    function reset() {
        clearTimers();
        pending = null;
        overlay.classList.remove('is-visible');
        overlay.setAttribute('aria-hidden', 'true');
        stage('idle');
    }

    function finish() {
        if (!pending) return;
        const form = pending;
        pending = null;
        clearTimers();
        form.submit();
    }

    function play(form) {
        const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce) {
            form.submit();
            return;
        }

        pending = form;
        stage('idle');
        overlay.classList.add('is-visible');
        overlay.setAttribute('aria-hidden', 'false');
        void overlay.offsetWidth;

        later(() => stage('walk'), 80);
        later(() => stage('open'), 1250);
        later(() => stage('exit'), 1900);
        later(() => stage('bye'), 2900);
        later(finish, 3500);
    }

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.matches || !form.matches('form[action$="logout"]')) return;
        e.preventDefault();
        if (pending) return;
        play(form);
    });

    overlay.addEventListener('click', finish);

    window.addEventListener('pageshow', function(e) {
        if (e.persisted) reset();
    });
});