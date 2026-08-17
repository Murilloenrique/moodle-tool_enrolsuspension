<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * User selection form.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form used to select multiple users.
 */
class search_users_form extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement(
            'text',
            'usersearch',
            get_string('searchusers', 'tool_enrolsuspension'),
            [
                'id' => 'tool-enrolsuspension-user-search',
                'autocomplete' => 'off',
                'placeholder' => get_string(
                    'searchusersplaceholder',
                    'tool_enrolsuspension'
                ),
            ]
        );

        $mform->setType('usersearch', PARAM_TEXT);

        $mform->addElement(
            'html',
            '<div id="tool-enrolsuspension-search-results"
                  class="list-group mt-2"></div>'
        );

        $mform->addElement(
            'html',
            '<div class="mt-4">
                <h4>' .
                    get_string(
                        'selectedusers',
                        'tool_enrolsuspension'
                    ) .
                '</h4>
                <div id="tool-enrolsuspension-selected-users"
                     class="mb-3"></div>
            </div>'
        );

        $mform->addElement(
            'hidden',
            'selecteduserids',
            ''
        );

        $mform->setType(
            'selecteduserids',
            PARAM_SEQUENCE
        );

        $this->add_action_buttons(
            false,
            get_string('next', 'tool_enrolsuspension')
        );
    }
}