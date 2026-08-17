// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Multiple user selector for the enrolment suspension tool.
 *
 * @module     tool_enrolsuspension/user_selector
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

const selectors = {
    search: '#tool-enrolsuspension-user-search',
    results: '#tool-enrolsuspension-search-results',
    selected: '#tool-enrolsuspension-selected-users',
    hidden: 'input[name="selecteduserids"]',
    nextButton: 'input[type="submit"], button[type="submit"]',
};

const selectedUsers = new Map();

let searchTimer = null;
let strings = {};

/**
 * Escapes text before inserting it into HTML.
 *
 * @param {String} value
 * @returns {String}
 */
const escapeHtml = (value) => {
    const element = document.createElement('div');

    element.textContent = value ?? '';

    return element.innerHTML;
};

/**
 * Updates the hidden field containing selected user IDs.
 */
const updateHiddenField = () => {
    const hiddenField = document.querySelector(selectors.hidden);

    if (!hiddenField) {
        return;
    }

    hiddenField.value = Array.from(selectedUsers.keys()).join(',');
};

/**
 * Enables the next button only when at least one user is selected.
 */
const updateNextButton = () => {
    const button = document.querySelector(selectors.nextButton);

    if (!button) {
        return;
    }

    button.disabled = selectedUsers.size === 0;
};

/**
 * Renders all selected users.
 */
const renderSelectedUsers = () => {
    const container = document.querySelector(selectors.selected);

    if (!container) {
        return;
    }

    container.innerHTML = '';

    if (selectedUsers.size === 0) {
        const message = document.createElement('p');

        message.className = 'text-muted';
        message.textContent = strings.nousersselected;

        container.appendChild(message);
    }

    selectedUsers.forEach((user) => {
        const item = document.createElement('div');

        item.className =
            'd-inline-flex align-items-center border rounded px-3 py-2 mr-2 mb-2 bg-light';

        item.innerHTML = `
            <div>
                <strong>${escapeHtml(user.fullname)}</strong>
                <div class="small text-muted">
                    ${escapeHtml(user.email)}
                </div>
            </div>

            <button
                type="button"
                class="btn btn-link text-danger ml-3 p-0"
                data-action="remove-user"
                data-userid="${user.id}"
                title="${escapeHtml(strings.removeuser)}"
                aria-label="${escapeHtml(strings.removeuser)}"
            >
                &times;
            </button>
        `;

        container.appendChild(item);
    });

    updateHiddenField();
    updateNextButton();
};

/**
 * Selects a user.
 *
 * @param {Object} user
 */
const selectUser = (user) => {
    selectedUsers.set(String(user.id), user);

    renderSelectedUsers();

    const resultsContainer = document.querySelector(selectors.results);

    if (resultsContainer) {
        resultsContainer.innerHTML = '';
    }

    const searchField = document.querySelector(selectors.search);

    if (searchField) {
        searchField.value = '';
        searchField.focus();
    }
};

/**
 * Renders search results.
 *
 * @param {Array} users
 */
const renderResults = (users) => {
    const container = document.querySelector(selectors.results);

    if (!container) {
        return;
    }

    container.innerHTML = '';

    const availableUsers = users.filter(
        (user) => !selectedUsers.has(String(user.id))
    );

    if (availableUsers.length === 0) {
        const message = document.createElement('div');

        message.className = 'alert alert-info mt-2';
        message.textContent = strings.nousersfound;

        container.appendChild(message);

        return;
    }

    availableUsers.forEach((user) => {
        const button = document.createElement('button');

        button.type = 'button';
        button.className =
            'list-group-item list-group-item-action d-flex justify-content-between align-items-center';

        const identification = [];

        if (user.email) {
            identification.push(escapeHtml(user.email));
        }

        if (user.username) {
            identification.push(escapeHtml(user.username));
        }

        if (user.idnumber) {
            identification.push(
                `${escapeHtml(strings.idnumberlabel)}: ${escapeHtml(user.idnumber)}`
            );
        }

        button.innerHTML = `
            <div class="text-left">
                <strong>${escapeHtml(user.fullname)}</strong>
                <div class="small text-muted">
                    ${identification.join(' | ')}
                </div>
            </div>

            <span class="badge badge-primary">
                ${escapeHtml(strings.selectuser)}
            </span>
        `;

        button.addEventListener('click', () => selectUser(user));

        container.appendChild(button);
    });
};

/**
 * Searches users through Moodle AJAX.
 *
 * @param {String} query
 */
const searchUsers = async(query) => {
    const container = document.querySelector(selectors.results);

    if (!container) {
        return;
    }

    if (query.length < 2) {
        container.innerHTML = '';

        return;
    }

    container.innerHTML = `
        <div class="alert alert-secondary mt-2">
            ${escapeHtml(strings.searching)}
        </div>
    `;

    try {
        const requests = Ajax.call([{
            methodname: 'tool_enrolsuspension_search_users',
            args: {
                query,
            },
        }]);

        const users = await requests[0];

        renderResults(users);
    } catch (error) {
        container.innerHTML = '';

        Notification.exception(error);
    }
};

/**
 * Initialises the multiple user selector.
 *
 * @param {Object} languageStrings Translated strings provided by PHP.
 */
export const init = (languageStrings) => {
    const searchField = document.querySelector(selectors.search);

    if (!searchField) {
        return;
    }

    strings = languageStrings;

    renderSelectedUsers();

    searchField.addEventListener('input', (event) => {
        const query = event.target.value.trim();

        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(() => {
            searchUsers(query);
        }, 400);
    });

    document.addEventListener('click', (event) => {
        const removeButton = event.target.closest(
            '[data-action="remove-user"]'
        );

        if (!removeButton) {
            return;
        }

        selectedUsers.delete(removeButton.dataset.userid);

        renderSelectedUsers();
    });
};