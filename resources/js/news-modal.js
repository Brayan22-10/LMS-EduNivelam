document.addEventListener('DOMContentLoaded', function() {
    const newsModal = document.getElementById('newsModal');
    if (newsModal) {
        newsModal.addEventListener('show.bs.modal', function(event) {
            const card = event.relatedTarget;

            const title = card.getAttribute('data-title');
            const category = card.getAttribute('data-category');
            const date = card.getAttribute('data-date');
            const author = card.getAttribute('data-author');
            const content = card.getAttribute('data-content');
            const image = card.getAttribute('data-image');

            document.getElementById('newsModalLabel').textContent = title;
            document.getElementById('modalCategory').textContent = category;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalAuthor').textContent = author;
            document.getElementById('modalContent').textContent = content;

            const imgContainer = document.getElementById('modalImageContainer');
            const imgElement = document.getElementById('modalImage');
            if (image) {
                imgElement.src = image;
                imgContainer.classList.remove('d-none');
            } else {
                imgElement.src = '';
                imgContainer.classList.add('d-none');
            }
        });
    }
});