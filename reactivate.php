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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Reactivate selected suspension records.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:reactivate', $context);
if (!data_submitted()) {
    throw new moodle_exception('postrequired', 'tool_enrolsuspension');
}
require_sesskey();
$ids = optional_param_array('suspensionids', [], PARAM_INT);
$result = \tool_enrolsuspension\local\manager::reactivate($ids, $USER->id);
$message = get_string('reactivationsuccess', 'tool_enrolsuspension', $result['reactivated']);
$type = \core\output\notification::NOTIFY_SUCCESS;
if ($result['skipped']) {
    $message .= ' ' . get_string('reactivationsskipped', 'tool_enrolsuspension', $result['skipped']);
    if ($result['errors']) {
        $message .= ' ' . implode(' ', array_slice($result['errors'], 0, 10));
    }
    $type = $result['reactivated'] ? \core\output\notification::NOTIFY_WARNING
        : \core\output\notification::NOTIFY_ERROR;
}
redirect(new moodle_url('/admin/tool/enrolsuspension/history.php'), $message, null, $type);
