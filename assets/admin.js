(async function() {
    document.addEventListener('DOMContentLoaded', function() {
        enableConfirmElements();
        enableMapPointerFileUploadInputs();
    });

    function enableConfirmElements() {
        const confirmElements = document.querySelectorAll('[data-confirm]');

        if (!confirmElements.length) {
            return;
        }

        confirmElements.forEach((link) => {
            link.addEventListener('click', function (event) {
                const message = link.getAttribute('data-confirm');
                if (confirm(message)) {
                    return;
                }

                event.stopPropagation();
                event.preventDefault();
                return false;
            });
        });
    }

    function enableMapPointerFileUploadInputs() {
        if (!document.querySelectorAll('.pointer-image-container').length) {
            return;
        }

        const fileInput = document.querySelector('.ea-edit-form[method="post"] input[type="file"]');
        const storedUrls = {};
        document.querySelectorAll('.pointer-image-container').forEach(image => {
            image.addEventListener('click', async function() {
                const imageUrl = this.getAttribute('data-image-src');
                const imageName = this.getAttribute('data-image-name');

                try {
                    // Fetch the image from the URL
                    if (!storedUrls[imageUrl]) {
                        const response = await fetch(imageUrl);
                        storedUrls[imageUrl] = await response.blob();
                    }

                    const blob = storedUrls[imageUrl];

                    // Create a File object from the blob
                    const file = new File([blob], `pointer_${imageName}.png`, { type: blob.type });

                    // Create a DataTransfer object to set the file input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    // Set the files to the file input
                    fileInput.files = dataTransfer.files;

                    // Trigger change event to notify any listeners
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                } catch (error) {
                    console.error('Error loading image:', error);
                }
            })
        })
    }
})();
