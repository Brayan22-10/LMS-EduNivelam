document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const imageToCrop = document.getElementById('imageToCrop');
    const cropperWrapper = document.getElementById('cropperWrapper');
    const cropButton = document.getElementById('cropButton');
    const cancelCropButton = document.getElementById('cancelCropButton');
    const croppedImageOutput = document.getElementById('croppedImageOutput');
    const croppedPreview = document.getElementById('croppedPreview');
    const previewContainer = document.getElementById('previewContainer');
    const reajustBtn = document.getElementById('reajustBtn');
    const existingImageSrc = document.getElementById('existingImageSrc');

    let cropper = null;

    function initCropper(imgSrc) {
        if (!imageToCrop) return;

        imageToCrop.src = imgSrc;
        if (cropperWrapper) cropperWrapper.classList.remove('d-none');
        if (previewContainer) previewContainer.classList.add('d-none');

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        setTimeout(() => {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 16 / 9,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                zoomable: true,
                scalable: true,
                movable: true,
            });
        }, 150);
    }

    if (reajustBtn && existingImageSrc) {
        reajustBtn.addEventListener('click', function(e) {
            e.preventDefault();
            initCropper(existingImageSrc.src);
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    initCropper(event.target.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    if (cropButton) {
        cropButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (!cropper) {
                alert('Por favor, selecciona o ajusta una imagen primero.');
                return;
            }

            const canvas = cropper.getCroppedCanvas({
                width: 1200,
                height: 675,
                imageSmoothingQuality: 'high',
            });

            if (canvas) {
                const base64Image = canvas.toDataURL('image/jpeg', 0.9);

                if (croppedImageOutput) croppedImageOutput.value = base64Image;
                if (croppedPreview) croppedPreview.src = base64Image;
                if (previewContainer) previewContainer.classList.remove('d-none');
                if (cropperWrapper) cropperWrapper.classList.add('d-none');

                cropper.destroy();
                cropper = null;
            }
        });
    }

    if (cancelCropButton) {
        cancelCropButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (cropperWrapper) cropperWrapper.classList.add('d-none');
            if (imageInput) imageInput.value = '';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });
    }
});