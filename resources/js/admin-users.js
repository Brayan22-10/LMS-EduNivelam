document.addEventListener("DOMContentLoaded", function() {
    console.log("Gestión de usuarios cargada correctamente.");
    const userModal = document.getElementById('userModal');

    if (userModal) {
        userModal.addEventListener('show.bs.modal', function(event) {
            const row = event.relatedTarget;

            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');

            document.getElementById('modal-username').textContent = name;
            document.getElementById('modal-useremail').textContent = email;
        });
    }
});