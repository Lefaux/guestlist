import bootstrap from "bootstrap/dist/js/bootstrap.bundle";

const SELECTOR_GUEST_LIST_TABLE = '#guest-list-table';
const SELECTOR_CHECKIN_BUTTON = '[data-action="check-in"]';
const SELECTOR_CHECKOUT_BUTTON = '[data-action="check-out"]';
const SELECTOR_CHECKIN_MODAL_TEMPLATE = '#check-in-modal';
const SELECTOR_CHECKIN_MODAL_PLUS_BUTTON_TEMPLATE = '#modal-plus-button';
const SELECTOR_CHECKOUT_MODAL_TEMPLATE = '#check-out-modal';
const SELECTOR_BUTTON_IN_PROGRESS = '#button-in-progress';
const SELECTOR_GUEST_NOT_CHECKED_IN_TEMPLATE = '#guest-list-row-not-checked-in';
const SELECTOR_GUEST_CHECKED_IN_TEMPLATE = '#guest-list-row-checked-in';

document.addEventListener('DOMContentLoaded', function () {
    const guestListTable = document.querySelector(SELECTOR_GUEST_LIST_TABLE);
    const guestListTableBody = document.querySelector(SELECTOR_GUEST_LIST_TABLE + ' tbody');
    const eventId = parseInt(guestListTable.dataset.eventId, 10);

    fetch('/json/' + eventId + '/guests', {
        credentials: 'same-origin'
    }).then(async function (response) {
        response.json().then(function (data) {
            // For performance reasons, all generated rows are stored in a document fragment before rendering in client
            const rows = document.createDocumentFragment();
            for (const guest of data) {
                const guestRow = composeGuestRow(guest);
                rows.appendChild(guestRow);
            }
            guestListTableBody.appendChild(rows);

            document.dispatchEvent(new CustomEvent('guestlist:list-loaded'));
        })
    });

    guestListTable.addEventListener('guestlist:record-updated', function (event) {
        // Redraw row in table
        const computedGuestRow = composeGuestRow(event.detail.data);
        const currentGuestRow = event.target.querySelector('[data-guest-id="' + event.detail.guestId + '"]');

        currentGuestRow.replaceWith(computedGuestRow);
    });
});

document.addEventListener('guestlist:list-loaded', function () {
    const guestListTable = document.querySelector(SELECTOR_GUEST_LIST_TABLE);
    guestListTable.addEventListener('click', function (event) {
        if (event.target.matches(SELECTOR_CHECKIN_BUTTON)) {
            const row = event.target.closest('tr');
            const guestId = row.dataset.guestId;
            const pluses = row.dataset.pluses;
            const checkinModal = getFromTemplate(SELECTOR_CHECKIN_MODAL_TEMPLATE)

            injectData(row.dataset, checkinModal);
            document.body.appendChild(checkinModal);

            const plusButtonContainer = document.querySelector('form');
            if (pluses > 0) {
                plusButtonContainer.removeAttribute('hidden');
                const plusButtons = document.getElementById('plusesButtons');

                for (let i = 0; i <= pluses; ++i) {
                    const plusButton = getFromTemplate(SELECTOR_CHECKIN_MODAL_PLUS_BUTTON_TEMPLATE);
                    const input = plusButton.querySelector('input');
                    const label = plusButton.querySelector('label');
                    input.id += i.toString();
                    input.value = i.toString();
                    label.setAttribute('for', label.getAttribute('for') + i.toString());
                    injectData({value: i}, plusButton);

                    plusButtons.append(plusButton);
                }
            }

            const modal = new bootstrap.Modal(checkinModal, {
                backdrop: 'static'
            });
            disposeModalOnClose(checkinModal);
            modal.show();

            plusButtonContainer.addEventListener('submit', async function (submitEvent) {
                submitEvent.preventDefault();

                event.target.disabled = true;
                event.target.innerHTML = '';
                event.target.appendChild(getFromTemplate(SELECTOR_BUTTON_IN_PROGRESS));

                let checkedInPluses = null;
                const plusButtonContainer = document.querySelector('form');
                if (plusButtonContainer !== null) {
                    let selectedCheckedInPluses;
                    if ((selectedCheckedInPluses = document.querySelector('input[name="checkedInPluses"]:checked')) !== null) {
                        checkedInPluses = selectedCheckedInPluses.value;
                    }
                }
                await fetch('/json/checkin/' + guestId, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        checkedInPluses: checkedInPluses
                    }),
                }).then(async function (response) {
                    response.json().then(function (data) {
                        modal.hide();

                        if (!response.ok) {
                            handleAjaxError(data);
                            return;
                        }

                        document.querySelector(SELECTOR_GUEST_LIST_TABLE).dispatchEvent(new CustomEvent('guestlist:record-updated', {
                            detail: {
                                guestId: guestId,
                                data: data
                            }
                        }));
                    });
                });
            });
        }

        if (event.target.matches(SELECTOR_CHECKOUT_BUTTON)) {
            const row = event.target.closest('tr');
            const guestId = row.dataset.guestId;
            const checkoutModal = getFromTemplate(SELECTOR_CHECKOUT_MODAL_TEMPLATE)

            injectData(row.dataset, checkoutModal);
            document.body.appendChild(checkoutModal);

            const modal = new bootstrap.Modal(checkoutModal, {
                backdrop: 'static'
            });
            disposeModalOnClose(checkoutModal);
            modal.show();

            checkoutModal.querySelector('[data-action="perform-check-out"]').addEventListener('click', async function (event) {
                event.target.disabled = true;
                event.target.innerHTML = '';
                event.target.appendChild(getFromTemplate(SELECTOR_BUTTON_IN_PROGRESS));

                await fetch('/json/checkout/' + guestId, {
                    method: 'POST',
                    credentials: 'same-origin'
                }).then(async function (response) {
                    response.json().then(function (data) {
                        modal.hide();

                        if (!response.ok) {
                            handleAjaxError(data);
                            return;
                        }

                        document.querySelector(SELECTOR_GUEST_LIST_TABLE).dispatchEvent(new CustomEvent('guestlist:record-updated', {
                            detail: {
                                guestId: guestId,
                                data: data
                            }
                        }));
                    });
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
    // Fill data attributes with dotted values
    const rootNode = domNode.getRootNode();
    for (const attribute of rootNode.getAttributeNames()) {
        if (!attribute.startsWith('data-')) {
            continue;
        }
        const requestedDataKey = rootNode.getAttribute(attribute).substring(1);
        if (dataValues.hasOwnProperty(requestedDataKey)) {
            rootNode.setAttribute(attribute, dataValues[requestedDataKey]);
        }
    }

    // Now fill all DOM nodes expecting a value
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

function getFromTemplate(id) {
    const template = document.querySelector(id);
    return template.content.firstElementChild.cloneNode(true);
}

function composeGuestRow(data) {
    const template = data.checkInTime === null
        ? SELECTOR_GUEST_NOT_CHECKED_IN_TEMPLATE
        : SELECTOR_GUEST_CHECKED_IN_TEMPLATE;
    const rowFromTemplate = getFromTemplate(template);
    injectData(data, rowFromTemplate);

    if (data.pluses) {
        let plusesColText = '+';
        if (data.checkInTime !== null) {
            plusesColText += data.checkedInPluses + ' / ' + data.pluses;
        } else {
            plusesColText += data.pluses;
        }
        rowFromTemplate.querySelector('[data-col="pluses"]').textContent = plusesColText;
    }

    return rowFromTemplate;
}