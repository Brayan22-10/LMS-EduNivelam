document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bannerForm');
    if (!form) return;

    const cfg = JSON.parse(form.dataset.config || '{}');
    const isEdit = cfg.mode === 'edit';
    const select = document.getElementById('page_identifier');
    const container = document.getElementById('dynamicFieldsContainer');
    const helper = document.getElementById('helperText');
    const actions = document.getElementById('formActions');
    const submitBtn = document.getElementById('submitBtn');
    const activeInput = document.getElementById('is_active');
    const wrappers = form.querySelectorAll('[data-field]');
    const textIds = ['badge_text', 'title', 'subtitle'];
    const fileInput = document.getElementById('image_path');

    let currentPage = cfg.current || '';
    let submitting = false;
    let snapshot = '';

    function val(id) {
        const el = document.getElementById(id);
        return el && el.value ? el.value.trim() : '';
    }

    function toggleField(wrapper, show) {
        wrapper.classList.toggle('d-none', !show);
        wrapper.querySelectorAll('input, textarea').forEach((el) => {
            el.disabled = !show;
        });
    }

    function applyPage(page) {
        const def = cfg.pages ? cfg.pages[page] : null;

        container.classList.toggle('is-disabled', !def);
        actions.classList.toggle('d-none', !def);

        if (!def) {
            helper.textContent = cfg.placeholderHelp || '';
            wrappers.forEach((w) => toggleField(w, false));
            return;
        }

        helper.textContent = def.help;
        wrappers.forEach((w) => toggleField(w, def.fields.includes(w.dataset.field)));
    }

    function hasData() {
        return textIds.some((id) => val(id) !== '') || (fileInput && fileInput.files.length > 0);
    }

    function resetFields() {
        textIds.forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        if (fileInput) fileInput.value = '';
    }

    function serialize() {
        return JSON.stringify({
            page: currentPage,
            texts: textIds.map(val),
            file: fileInput && fileInput.files.length ? fileInput.files[0].name : '',
            active: activeInput ? activeInput.checked : false,
        });
    }

    if (!isEdit && select) {
        select.addEventListener('change', () => {
            const next = select.value;

            if (currentPage && hasData()) {
                if (!window.confirm(cfg.messages.change)) {
                    select.value = currentPage;
                    return;
                }
                resetFields();
            }

            currentPage = next;
            applyPage(next);
            snapshot = serialize();
        });
    }

    window.addEventListener('beforeunload', (e) => {
        if (!submitting && snapshot !== serialize()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    form.addEventListener('submit', () => {
        submitting = true;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
        }
    });

    applyPage(currentPage);
    snapshot = serialize();
});