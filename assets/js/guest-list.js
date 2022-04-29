import bootstrap from "bootstrap/dist/js/bootstrap.bundle";

document.addEventListener('DOMContentLoaded', function () {
    const SELECTOR_CHECKOUT_BUTTON = '[data-action="check-out"]';
    const SELECTOR_CHECKOUT_MODAL_TEMPLATE = '#check-out-modal';

    const guestListTable = document.getElementById('guest-list');
    guestListTable.addEventListener('click', function (event) {
        if (event.target.matches(SELECTOR_CHECKOUT_BUTTON)) {
            const row = event.target.closest('tr');
            const guestId = row.dataset.guestId;
            const checkoutModalTemplate = document.querySelector(SELECTOR_CHECKOUT_MODAL_TEMPLATE);
            const checkoutModal = checkoutModalTemplate.content.firstElementChild.cloneNode(true);

            injectData(row.dataset, checkoutModal)
            document.body.appendChild(checkoutModal);

            const modal = new bootstrap.Modal(checkoutModal, {
                backdrop: 'static'
            });
            disposeModalOnClose(checkoutModal);
            modal.show();

            checkoutModal.querySelector('[data-action="perform-check-out"]').addEventListener('click', async function (clickEvent) {
                await fetch('/json/checkout/' + guestId, {
                    method: 'POST',
                    credentials: 'same-origin'
                }).then(async function (response) {
                    if (!response.ok) {
                        response.json().then(function (data) {
                            modal.hide();
                            handleAjaxError(data);
                        })
                    } else {
                        modal.hide();

                        // TODO: Reload guest list
                    }
                });
            });
        }
    });
});

function handleAjaxError(data) {
    const errorModal = document.querySelector('#error-modal');
    injectData(data, errorModal);

    const modal = new bootstrap.Modal(errorModal, {
        backdrop: 'static'
    });
    modal.show();

    errorModal.querySelector('pre').textContent = data.stack.map(item => item.file + ':' + item.line).join("\n");
}

function injectData(dataValues, domNode) {
    for (const [key, value] of Object.entries(dataValues)) {
        Array.from(domNode.querySelectorAll('[data-value="' + key + '"]')).forEach(function (receiver) {
            receiver.textContent = value;
        });
    }
}

function disposeModalOnClose(modal) {
    modal.addEventListener('hidden.bs.modal', function (modalEvent) {
        modalEvent.target.remove();
    });
}