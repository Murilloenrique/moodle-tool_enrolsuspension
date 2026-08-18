// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Synchronises the "all current courses" option with individual course checkboxes.
 *
 * @module     tool_enrolsuspension/course_selector
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const selectors = {
    allCourses: '#allcurrentcourses',
    course: 'input[name="selectedcourses[]"]',
};

/**
 * Applies the state of the general option to every course checkbox.
 *
 * @param {HTMLInputElement} allCoursesCheckbox General suspension checkbox.
 * @param {HTMLInputElement[]} courseCheckboxes Course checkboxes.
 */
const synchroniseCourses = (allCoursesCheckbox, courseCheckboxes) => {
    courseCheckboxes.forEach((checkbox) => {
        checkbox.checked = allCoursesCheckbox.checked;
        checkbox.disabled = allCoursesCheckbox.checked;
    });
};

/**
 * Initialises the course selector behaviour.
 */
export const init = () => {
    const allCoursesCheckbox = document.querySelector(selectors.allCourses);
    const courseCheckboxes = Array.from(document.querySelectorAll(selectors.course));

    if (!allCoursesCheckbox || courseCheckboxes.length === 0) {
        return;
    }

    allCoursesCheckbox.addEventListener('change', () => {
        synchroniseCourses(allCoursesCheckbox, courseCheckboxes);
    });

    synchroniseCourses(allCoursesCheckbox, courseCheckboxes);
};
