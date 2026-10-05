document.addEventListener("DOMContentLoaded", function() {
    const removeBtn = document.getElementById('removeAvatarBtn');
    const removeInput = document.getElementById('remove_avatar');
    const profileForm = document.getElementById('profileForm');

    if (removeBtn && removeInput && profileForm) {
        removeBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas eliminar tu foto de perfil actual?')) {
                removeInput.value = '1';
                profileForm.submit();
            }
        });
    }
});