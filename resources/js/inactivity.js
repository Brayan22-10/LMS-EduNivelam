document.addEventListener('DOMContentLoaded', function() {
    let inactivityTime = function() {
        let time;
        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onkeypress = resetTimer;
        window.onclick = resetTimer;
        window.onscroll = resetTimer;

        function logout() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');

            if (!csrfToken) return;

            fetch('/check-session-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            }).then(() => {
                window.location.href = '/login?expired=1';
            }).catch(() => {
                window.location.href = '/login?expired=1';
            });
        }

        function resetTimer() {
            clearTimeout(time);
            time = setTimeout(logout, 5 * 60 * 1000);
        }
    };

    inactivityTime();
});