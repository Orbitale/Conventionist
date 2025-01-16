(async function() {
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('[data-confirm]');

        if (!elements.length) {
            return;
        }

        elements.forEach((link) => {
            link.addEventListener('click', function (event) {
                const message = link.getAttribute('data-confirm');
                if (confirm(message)) {
                    return;
                }

                event.stopPropagation();
                event.preventDefault();
                return false;
            });
        })
    });
})();
