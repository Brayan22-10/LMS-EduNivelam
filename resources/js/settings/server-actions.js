document.addEventListener("DOMContentLoaded", function() {
    const hasServerActivity = document.body.dataset.serverActive === "true";

    if (hasServerActivity) {
        const serverTabBtn = document.getElementById('server-tab');
        if (serverTabBtn) {
            const tab = new bootstrap.Tab(serverTabBtn);
            tab.show();
        }
    }
});