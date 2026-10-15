document.addEventListener('DOMContentLoaded', function() {
    let inactivityTime = function() {
        let time;

        function logout() {
            window.location.href = '/session-expired';
        }

        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(logout, 300000);
        }

        window.addEventListener('mousemove', resetTimer);
        window.addEventListener('keypress', resetTimer);
        window.addEventListener('click', resetTimer);
        window.addEventListener('scroll', resetTimer, true);
        window.addEventListener('touchstart', resetTimer);

        resetTimer();
    };

    inactivityTime();
});