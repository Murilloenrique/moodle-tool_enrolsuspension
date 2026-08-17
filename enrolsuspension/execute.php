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
 * Execute a reviewed suspension operation.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:suspend', $context);
if (!data_submitted()) {
    throw new moodle_exception('postrequired', 'tool_enrolsuspension');
}
require_sesskey();
$token = required_param('op', PARAM_ALPHANUM);

try {
    $result = \tool_enrolsuspension\local\manager::suspend_operation($token, $USER->id);
    $message = get_string('suspensionsuccess', 'tool_enrolsuspension', $result['suspended']);
    $destination = has_capability('tool/enrolsuspension:view', $context)
        ? new moodle_url('/admin/tool/enrolsuspension/history.php')
        : new moodle_url('/admin/tool/enrolsuspension/index.php');
    redirect($destination, $message, null, \core\output\notification::NOTIFY_SUCCESS);
} catch (moodle_exception $exception) {
    $terminalerrors = ['operationnotready', 'operationalreadyused'];
    if (in_array($exception->errorcode, $terminalerrors, true)) {
        $destination = has_capability('tool/enrolsuspension:view', $context)
            ? new moodle_url('/admin/tool/enrolsuspension/history.php')
            : new moodle_url('/admin/tool/enrolsuspension/index.php');
        redirect($destination, $exception->getMessage(), null, \core\output\notification::NOTIFY_WARNING);
    }

    $reviewerrors = [
        'operationstatechanged',
        'operationcontainsunsupported',
        'alreadymanagedsuspension',
        'enrolmentstateunchanged',
    ];
    if (in_array($exception->errorcode, $reviewerrors, true)) {
        \tool_enrolsuspension\local\operation_manager::reset_review($token, $USER->id);
        redirect(new moodle_url('/admin/tool/enrolsuspension/options.php', ['op' => $token]),
            $exception->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }

    redirect(new moodle_url('/admin/tool/enrolsuspension/review.php', ['op' => $token]),
        $exception->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
} catch (Throwable $exception) {
    debugging($exception->getMessage(), DEBUG_DEVELOPER);
    redirect(new moodle_url('/admin/tool/enrolsuspension/review.php', ['op' => $token]),
        get_string('operationgenericerror', 'tool_enrolsuspension'), null,
        \core\output\notification::NOTIFY_ERROR);
}
