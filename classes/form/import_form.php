<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * CSV import form.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Upload form for deterministic user imports.
 */
class import_form extends \moodleform {
    /**
     * Define the import form fields.
     */
    public function definition(): void {
        $mform = $this->_form;
        $mform->addElement('filepicker', 'csvfile', get_string('csvfile', 'tool_enrolsuspension'), null, [
            'accepted_types' => ['.csv', '.txt'],
            'maxbytes' => 1048576,
        ]);
        $mform->addRule('csvfile', get_string('required'), 'required');
        $mform->addElement('select', 'delimiter', get_string('csvdelimiter', 'tool_enrolsuspension'), [
            'comma' => get_string('csvdelimitercomma', 'tool_enrolsuspension'),
            'semicolon' => get_string('csvdelimitersemicolon', 'tool_enrolsuspension'),
            'tab' => get_string('csvdelimitertab', 'tool_enrolsuspension'),
        ]);
        $mform->setDefault('delimiter', 'comma');
        $this->add_action_buttons(false, get_string('importcsv', 'tool_enrolsuspension'));
    }
}
