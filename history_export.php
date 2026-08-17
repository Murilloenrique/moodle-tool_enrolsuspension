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
 * Export suspension history as CSV.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:view', $context);
$status = optional_param('status', \tool_enrolsuspension\local\manager::STATUS_SUSPENDED, PARAM_INT);
$allowedstatuses = [
    \tool_enrolsuspension\local\manager::STATUS_SUSPENDED,
    \tool_enrolsuspension\local\manager::STATUS_REACTIVATED,
    \tool_enrolsuspension\local\manager::STATUS_STALE,
];
if (!in_array($status, $allowedstatuses, true)) {
    $status = \tool_enrolsuspension\local\manager::STATUS_SUSPENDED;
}

$sql = "SELECT s.*, u.firstname, u.lastname, u.email, c.fullname AS coursename,
               creator.firstname AS creatorfirstname, creator.lastname AS creatorlastname,
               reactivator.firstname AS reactivatorfirstname, reactivator.lastname AS reactivatorlastname
          FROM {tool_enrolsuspension_log} s
     LEFT JOIN {user} u ON u.id = s.userid
     LEFT JOIN {course} c ON c.id = s.courseid
     LEFT JOIN {user} creator ON creator.id = s.createdby
     LEFT JOIN {user} reactivator ON reactivator.id = s.reactivatedby
         WHERE s.status = :status
      ORDER BY s.timecreated DESC, s.id DESC";
$records = $DB->get_records_sql($sql, ['status' => $status]);

$filename = 'enrolsuspension-history-' . gmdate('Ymd-His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

echo "\xEF\xBB\xBF";
$handle = fopen('php://output', 'wb');
fputcsv($handle, [
    'id',
    'status',
    'userid',
    'user',
    'email',
    'courseid',
    'course',
    'enrolid',
    'userenrolmentid',
    'reason',
    'createdby',
    'createdby_name',
    'timecreated',
    'reactivatedby',
    'reactivatedby_name',
    'timereactivated',
]);
foreach ($records as $record) {
    $username = '';
    if ($record->firstname !== null) {
        $username = fullname((object) ['firstname' => $record->firstname, 'lastname' => $record->lastname]);
    }
    $creatorname = '';
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
    fputcsv($handle, [
        $record->id,
        $record->status,
        $record->userid,
        $username,
        $record->email ?? '',
        $record->courseid,
        $record->coursename ?? '',
        $record->enrolid,
        $record->userenrolmentid,
        $record->reason,
        $record->createdby,
        $creatorname,
        $record->timecreated ? gmdate('c', $record->timecreated) : '',
        $record->reactivatedby,
        $reactivatorname,
        $record->timereactivated ? gmdate('c', $record->timereactivated) : '',
    ]);
}
fclose($handle);
exit;
