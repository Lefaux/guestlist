import Modal from 'bootstrap/js/dist/modal';

const SELECTOR_GUEST_LIST_TABLE = '#guest-list-table';
const SELECTOR_EVENT_STATISTICS_CONTAINER = '#event-statistics';
const SELECTOR_CHECKIN_BUTTON = '[data-action="check-in"]';
const SELECTOR_CHECKOUT_BUTTON = '[data-action="check-out"]';
const SELECTOR_CHECKIN_MODAL_TEMPLATE = '#check-in-modal';
const SELECTOR_CHECKIN_MODAL_PLUS_BUTTON_TEMPLATE = '#modal-plus-button';
const SELECTOR_CHECKOUT_MODAL_TEMPLATE = '#check-out-modal';
const SELECTOR_BUTTON_IN_PROGRESS = '#button-in-progress';
const SELECTOR_GUEST_NOT_CHECKED_IN_TEMPLATE = '#guest-list-row-not-checked-in';
const SELECTOR_GUEST_CHECKED_IN_TEMPLATE = '#guest-list-row-checked-in';
const SELECTOR_SEARCH_INPUT = '#searchbox';

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector(SELECTOR_SEARCH_INPUT);
    const guestListTable = document.querySelector(SELECTOR_GUEST_LIST_TABLE);
    const guestListTableBody = document.querySelector(SELECTOR_GUEST_LIST_TABLE + ' tbody');
    const eventId = parseInt(guestListTable.dataset.eventId, 10);

    guestListTable.addEventListener('guestlist:list-loaded', handleLoadedGuestList);

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
            guestListTable.dispatchEvent(new CustomEvent('guestlist:list-loaded'));
        })
    });

    guestListTable.addEventListener('guestlist:record-updated', function (event) {
        // Redraw row in table
        const computedGuestRow = composeGuestRow(event.detail.data);
        const currentGuestRow = event.target.querySelector('[data-guest-id="' + event.detail.guestId + '"]');

        currentGuestRow.replaceWith(computedGuestRow);
        loadStats(event.detail.data.event);
        sortRows();
        showHideCheckedInRow();
    });

    searchInput.addEventListener('input', debounce(function(event) {
        const normalizedSearch = event.target.value.toLowerCase();

        const rows = Array.from(guestListTable.querySelectorAll('tr[data-first-name][data-last-name]'));
        rows.forEach(function (row) {
            const normalizedFirstName = row.dataset.firstName.toLowerCase();
            const normalizedLastName = row.dataset.lastName.toLowerCase();

            if (normalizedFirstName.startsWith(normalizedSearch) || normalizedLastName.startsWith(normalizedSearch)) {
                row.hidden = false;
            } else {
                row.hidden = true;
            }
        });
    }, 150));
});

function handleLoadedGuestList() {
    const searchInput = document.querySelector(SELECTOR_SEARCH_INPUT);
    searchInput.disabled = false;

    const hiddenToggle = document.querySelector('#showCheckedInGuests');
    hiddenToggle.addEventListener('click', function (){
        showHideCheckedInRow();
    })

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

            const modal = new Modal(checkinModal, {
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

            const modal = new Modal(checkoutModal, {
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
}

function loadStats(event) {
    fetch('/json/stats/' + event, {
        method: 'POST',
        credentials: 'same-origin'
    }).then(async function (response) {
        response.json().then(function (data) {
            const container = document.querySelector(SELECTOR_EVENT_STATISTICS_CONTAINER);
            const percentageProgress = container.querySelector('[data-progress="percentage"]');
            percentageProgress.style.width = data.percentages.percentage + '%';
            percentageProgress.setAttribute('aria-valuenow', data.percentages.percentage);
            percentageProgress.textContent = data.percentages.percentage + '%';

            const noShowPercentageProgress = container.querySelector('[data-progress="no-show-percentage"]');
            noShowPercentageProgress.style.width = data.percentages.noShowPercentage + '%';
            noShowPercentageProgress.setAttribute('aria-valuenow', data.percentages.noShowPercentage);
            noShowPercentageProgress.textContent = data.percentages.noShowPercentage + '%';

            injectData(data.counters, container)
        });
    });
}

function handleAjaxError(data) {
    const errorModal = document.querySelector('#error-modal');
    injectData(data, errorModal);

    const modal = new Modal(errorModal, {
        backdrop: 'static'
    });
    modal.show();

    errorModal.querySelector('pre').textContent = data.stack.map(item => item.file + ':' + item.line).join("\n");
}

function injectData(dataValues, domNode) {
    // Fill data attributes with dotted values
    const rootNode = domNode.getRootNode();
    if (rootNode !== document) {
        for (const attribute of rootNode.getAttributeNames()) {
            if (!attribute.startsWith('data-')) {
                continue;
            }
            const requestedDataKey = rootNode.getAttribute(attribute).substring(1);
            if (dataValues.hasOwnProperty(requestedDataKey)) {
                if (null !== dataValues[requestedDataKey]) {
                    rootNode.setAttribute(attribute, dataValues[requestedDataKey]);
                } else {
                    rootNode.setAttribute(attribute, '');
                }
            }
        }
    }

    // Now fill all DOM nodes expecting a value
    for (const [key, value] of Object.entries(dataValues)) {
        Array.from(domNode.querySelectorAll('[data-value="' + key + '"]')).forEach(function (receiver) {
            if (null !== value) {
                receiver.textContent = value;
            } else {
                receiver.textContent = '';
            }
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

    // Change color based on VIP status
    if (data.vip) {
        rowFromTemplate.classList.replace('text-white', 'text-warning')
        rowFromTemplate.classList.add('bg-vip');
    }

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

function sortRows() {
    const rowSorter = function (row1, row2) {
        const checkInTimestamp1 = row1.dataset.checkIn;
        const checkInTimestamp2 = row2.dataset.checkIn;
        const firstName1 = row1.dataset.firstName;
        const firstName2 = row2.dataset.firstName;
        const lastName1 = row1.dataset.lastName;
        const lastName2 = row2.dataset.lastName;

        let result = checkInTimestamp1 - checkInTimestamp2;
        if (result === 0) {
            result = firstName1.localeCompare(firstName2);
            if (result === 0) {
                result = lastName1.localeCompare(lastName2);
            }
        }

        return result;
    };
    const guestListTableBody = document.querySelector(SELECTOR_GUEST_LIST_TABLE + ' tbody');
    const rows = Array.from(guestListTableBody.querySelectorAll('tr[data-first-name][data-last-name]'));
    rows.sort(rowSorter);

    const intermediateTableContents = document.createDocumentFragment();
    const intermediateTableBody = document.createElement('tbody');
    intermediateTableContents.appendChild(intermediateTableBody);
    intermediateTableBody.append(...rows);
    guestListTableBody.replaceWith(intermediateTableContents);
}

function showHideCheckedInRow() {
    const showHiddenGuests = document.getElementById('showCheckedInGuests').checked;
    const collection = document.getElementsByClassName("row-checked-in");
    for (let i = 0; i < collection.length; i++) {
        if (showHiddenGuests) {
            collection[i].style.display = "table-row";
        } else {
            collection[i].style.display = "none";
        }
    }
}

/**
 * Debounces event handler execution
 */
function debounce(func, wait) {
    let timeout;

    return function executedFunction() {
        let context = this;
        let args = arguments;

        clearTimeout(timeout);
        timeout = setTimeout(function() {
            timeout = null;
            func.apply(context, args);
        }, wait);
    };
};