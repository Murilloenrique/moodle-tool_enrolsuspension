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
 * Export suspension history as an Excel workbook.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:view', $context);

$status = optional_param(
    'status',
    \tool_enrolsuspension\local\history_manager::STATUS_ALL,
    PARAM_INT
);
$status = \tool_enrolsuspension\local\history_manager::normalise_status($status);
$records = \tool_enrolsuspension\local\history_manager::get_records($status);
$operationsequencemap = \tool_enrolsuspension\local\history_manager::operation_sequence_map();

$columns = [
    'id' => get_string('historyrecordid', 'tool_enrolsuspension'),
    'operation' => get_string('operation', 'tool_enrolsuspension'),
    'operationid' => get_string('operationinternalid', 'tool_enrolsuspension'),
    'status' => get_string('status'),
    'user' => get_string('user'),
    'email' => get_string('email'),
    'course' => get_string('course'),
    'courseshortname' => get_string('shortnamecourse'),
    'enrolmentmethod' => get_string('enrolmentmethod', 'tool_enrolsuspension'),
    'enroltype' => get_string('enrolmenttechnicaltype', 'tool_enrolsuspension'),
    'enrolid' => get_string('enrolinstanceid', 'tool_enrolsuspension'),
    'userenrolmentid' => get_string('userenrolmentid', 'tool_enrolsuspension'),
    'reason' => get_string('reason', 'tool_enrolsuspension'),
    'createdby' => get_string('performedby', 'tool_enrolsuspension'),
    'timecreated' => get_string('suspendedon', 'tool_enrolsuspension'),
    'reactivatedby' => get_string('reactivatedby', 'tool_enrolsuspension'),
    'timereactivated' => get_string('reactivationdate', 'tool_enrolsuspension'),
];

$callback = static function(\stdClass $record, bool $supportshtml) use ($operationsequencemap): array {
    unset($supportshtml);

    if ($record->firstname !== null) {
        $username = fullname((object) [
            'firstname' => $record->firstname,
            'lastname' => $record->lastname,
        ]);
    } else {
        $username = get_string('deleteduserfallback', 'tool_enrolsuspension', $record->userid);
    }

    $creatorname = get_string('unknownoperator', 'tool_enrolsuspension');
    if ($record->creatorfirstname !== null) {
        $creatorname = fullname((object) [
            'firstname' => $record->creatorfirstname,
            'lastname' => $record->creatorlastname,
        ]);
    }

    $reactivatorname = '';
    if ($record->reactivatorfirstname !== null) {
        $reactivatorname = fullname((object) [
            'firstname' => $record->reactivatorfirstname,
            'lastname' => $record->reactivatorlastname,
        ]);
    }

    $operation = \tool_enrolsuspension\local\history_manager::operation_label(
        (int) $record->operationid,
        $operationsequencemap
    );

    return [
        'id' => (int) $record->id,
        'operation' => $operation,
        'operationid' => (int) $record->operationid,
        'status' => \tool_enrolsuspension\local\history_manager::status_label((int) $record->status),
        'user' => $username,
        'email' => (string) ($record->email ?? ''),
        'course' => $record->coursename !== null
            ? (string) $record->coursename
            : get_string('deletedcoursefallback', 'tool_enrolsuspension', $record->courseid),
        'courseshortname' => (string) ($record->courseshortname ?? ''),
        'enrolmentmethod' => \tool_enrolsuspension\local\history_manager::enrolment_method_label(
            $record->historyenroltype
        ),
        'enroltype' => (string) $record->historyenroltype,
        'enrolid' => (int) $record->enrolid,
        'userenrolmentid' => (int) $record->userenrolmentid,
        'reason' => (string) $record->reason,
        'createdby' => $creatorname,
        'timecreated' => $record->timecreated ? userdate($record->timecreated) : '',
        'reactivatedby' => $reactivatorname,
        'timereactivated' => $record->timereactivated ? userdate($record->timereactivated) : '',
    ];
};

\core\dataformat::download_data(
    'historico-suspensoes-' . gmdate('Ymd-His'),
    'excel',
    $columns,
    $records,
    $callback
);
exit;
