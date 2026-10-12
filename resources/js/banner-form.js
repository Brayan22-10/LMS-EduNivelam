document.addEventListener('DOMContentLoaded', function() {
    const pageSelect = document.getElementById('page_identifier');
    const container = document.getElementById('dynamicFieldsContainer');
    const helperText = document.getElementById('helperText');

    const fieldBadge = document.getElementById('fieldBadgeWrapper');
    const fieldTitle = document.getElementById('fieldTitleWrapper');
    const fieldSubtitle = document.getElementById('fieldSubtitleWrapper');
    const fieldImage = document.getElementById('fieldImageWrapper');
    const formActions = document.getElementById('formActions');

    if (!pageSelect) return;

    let lastSelectedPage = pageSelect.value;

    function updateFormFields(page) {
        if (!page) {
            container.style.opacity = '0.4';
            container.style.pointerEvents = 'none';
            if (fieldBadge) fieldBadge.style.display = 'none';
            if (fieldTitle) fieldTitle.style.display = 'none';
            if (fieldSubtitle) fieldSubtitle.style.display = 'none';
            if (fieldImage) fieldImage.style.display = 'none';
            if (formActions) formActions.style.display = 'none';
            return;
        }

        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
        if (formActions) formActions.style.display = 'flex';

        if (page === 'home') {
            helperText.textContent = 'Configurando la sección Hero del Home (Título de bienvenida y tarjeta interactiva/imagen).';
            if (fieldBadge) fieldBadge.style.display = 'block';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'flex';
        } else if (page === 'about') {
            helperText.textContent = 'Configurando la sección Acerca de (Etiqueta superior, Título principal y Párrafo de propósito).';
            if (fieldBadge) fieldBadge.style.display = 'block';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'none';
        } else if (page === 'news') {
            helperText.textContent = 'Configurando el encabezado del feed de Avisos y Novedades Institucionales.';
            if (fieldBadge) fieldBadge.style.display = 'none';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'none';
        } else if (page === 'courses') {
            helperText.textContent = 'Configurando el encabezado del Catálogo de Cursos (Etiqueta, Título y Descripción).';
            if (fieldBadge) fieldBadge.style.display = 'block';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'none';
        } else if (page === 'instructors') {
            helperText.textContent = 'Configurando el encabezado del Cuerpo Docente (Etiqueta, Título y Descripción).';
            if (fieldBadge) fieldBadge.style.display = 'block';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'none';
        } else if (page === 'contact') {
            helperText.textContent = 'Configurando el encabezado de Ayuda y Soporte (Título de pregunta y subtítulo de asistencia).';
            if (fieldBadge) fieldBadge.style.display = 'none';
            if (fieldTitle) fieldTitle.style.display = 'block';
            if (fieldSubtitle) fieldSubtitle.style.display = 'block';
            if (fieldImage) fieldImage.style.display = 'none';
        }
    }

    pageSelect.addEventListener('change', function(e) {
        const titleInput = document.getElementById('title') ? document.getElementById('title').value : '';
        const subtitleInput = document.getElementById('subtitle') ? document.getElementById('subtitle').value : '';

        if ((titleInput || subtitleInput) && lastSelectedPage !== "") {
            const confirmChange = confirm('¿Estás seguro de cambiar de página? Los datos escritos en los campos se limpiarán para evitar inconsistencias.');
            if (!confirmChange) {
                pageSelect.value = lastSelectedPage;
                return;
            } else {
                if (document.getElementById('title')) document.getElementById('title').value = '';
                if (document.getElementById('subtitle')) document.getElementById('subtitle').value = '';
                if (document.getElementById('badge_text')) document.getElementById('badge_text').value = '';
            }
        }

        lastSelectedPage = pageSelect.value;
        updateFormFields(pageSelect.value);
    });

    if (pageSelect.value) {
        updateFormFields(pageSelect.value);
    }
});