(async function() {
    document.addEventListener('DOMContentLoaded', function() {
        enableConfirmElements();
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
})();
