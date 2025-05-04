
(async function() {
    document.addEventListener('DOMContentLoaded', function() {
        enableConfirmElements();

        enableEditInPlaceElements();
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

    function enableEditInPlaceElements() {
        const toggle_attr = 'data-edit-in-place-toggle';
        const toggles = document.querySelectorAll('['+toggle_attr+']');

        if (!toggles.length) {
            return;
        }

        toggles.forEach((toggle) => {
            const unique_id = toggle.getAttribute(toggle_attr);
            if (!unique_id) {
                console.error('There is an element with attribute "'+toggle_attr+'", but the attribute has no value.');
                return;
            }
            const field_container = document.querySelector(`[data-edit-field="${unique_id}"]`)
            if (!field_container) {
                console.error('There is an element with attribute "'+toggle_attr+'", but there is no field value associated with it.');
                return;
            }
            const form_container = document.querySelector(`[data-edit-form="${unique_id}"]`)
            if (!form_container) {
                console.error('There is an element with attribute "'+toggle_attr+'", but there is no edit form container associated with it.');
                return;
            }
            const message_container = document.querySelector(`[data-edit-message="${unique_id}"]`)
            if (!message_container) {
                console.error('There is an element with attribute "'+toggle_attr+'", but there is no message container associated with it.');
                return;
            }
            const form = form_container.querySelector('form');
            if (!form) {
                console.error('There is an element with attribute "'+toggle_attr+'", but there is no edit form associated with it.');
                return;
            }

            field_container.style.display = 'block';
            form_container.style.display = 'none';
            let show_form = false;
            toggle.addEventListener('click', () => {
                show_form = !show_form;
                form_container.style.display = show_form ? 'block' : 'none';
                field_container.style.display = show_form ? 'none' : 'block';
            });

            form_container.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();
                const url = form.action;
                const method = form.method.toLowerCase();
                const data = Object.fromEntries(new FormData(form));

                message_container.className = '';
                message_container.innerHTML = '';

                fetch(url, {
                    method,
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'text/html',
                    }
                }).catch((e) => {
                    message('error', e, `HTTP error.`);
                }).then((res) => {
                    if (!res) {
                        message('error', res, `Empty response.`);
                        return;
                    }
                    if (res.status !== 200) {
                        message('error', res, `Form submit error.`);
                        return;
                    }
                    return [res, res.text()];
                }).then((res, html) => {
                    if (!res || !html) {
                        console.error('Nothing returned !?');
                        return;
                    }
                    console.info('Successful edit', {res, html});
                    field_container.style.display = 'block';
                    form_container.style.display = 'none';
                    show_form = false;
                    field_container.innerHTML = html;
                });
            });

            function message(type, object, message) {
                let className = 'alert ';
                if (type === 'success') {
                    className += 'alert-success'
                } else if (type === 'error') {
                    className += 'alert-danger'
                } else {
                    className += 'alert-info';
                }

                message = message || '';

                if (message) {
                    message_container.className = className;
                    message_container.innerHTML = message;
                }

                if (type === 'error') {
                    console.error(message);
                    console.error(object);
                } else {
                    console.info(message, object);
                }
            }
        });
    }
})();
