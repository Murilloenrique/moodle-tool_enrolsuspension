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
 * Paginated suspension history.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $status, 'page' => $page]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('history', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

$total = $DB->count_records('tool_enrolsuspension', ['status' => $status]);
$sql = "SELECT s.*, u.firstname, u.lastname, u.email, c.fullname AS coursename,
               creator.firstname AS creatorfirstname, creator.lastname AS creatorlastname,
               reactivator.firstname AS reactivatorfirstname, reactivator.lastname AS reactivatorlastname
          FROM {tool_enrolsuspension} s
     LEFT JOIN {user} u ON u.id = s.userid
     LEFT JOIN {course} c ON c.id = s.courseid
     LEFT JOIN {user} creator ON creator.id = s.createdby
     LEFT JOIN {user} reactivator ON reactivator.id = s.reactivatedby
         WHERE s.status = :status
      ORDER BY s.timecreated DESC, s.id DESC";
$records = $DB->get_records_sql($sql, ['status' => $status], $page * $perpage, $perpage);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history', 'tool_enrolsuspension'));
echo html_writer::div(
    html_writer::link(
        new moodle_url('/admin/tool/enrolsuspension/history_export.php', ['status' => $status]),
        get_string('exporthistory', 'tool_enrolsuspension'),
        ['class' => 'btn btn-outline-secondary']
    ),
    'mb-3'
);
echo html_writer::div(
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => 1]),
        get_string('activesuspensions', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-primary mr-2']) .
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => 0]),
        get_string('reactivatedsuspensions', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-secondary mr-2']) .
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => 2]),
        get_string('stalesuspensions', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-secondary']),
    'mb-3'
);

if (!$records) {
    echo $OUTPUT->notification(get_string('nohistoryrecords', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_INFO);
} else {
    $canreactivate = $status === \tool_enrolsuspension\local\manager::STATUS_SUSPENDED
        && has_capability('tool/enrolsuspension:reactivate', $context);
    if ($canreactivate) {
        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => new moodle_url('/admin/tool/enrolsuspension/reactivate.php'),
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    }
    $table = new html_table();
    $table->head = array_filter([
        $canreactivate ? get_string('select') : null,
        get_string('user'),
        get_string('course'),
        get_string('reason', 'tool_enrolsuspension'),
        get_string('suspendedon', 'tool_enrolsuspension'),
        get_string('performedby', 'tool_enrolsuspension'),
        $status !== 1 ? get_string('reactivationinfo', 'tool_enrolsuspension') : null,
    ]);
    foreach ($records as $record) {
        $row = [];
        if ($canreactivate) {
            $row[] = html_writer::checkbox('suspensionids[]', $record->id, false, '');
        }
        if ($record->firstname !== null) {
            $user = (object) ['firstname' => $record->firstname, 'lastname' => $record->lastname];
            $userlabel = fullname($user) . '<br><small>' . s($record->email) . '</small>';
        } else {
            $userlabel = get_string('deleteduserfallback', 'tool_enrolsuspension', $record->userid);
        }
        $row[] = $userlabel;
        $row[] = $record->coursename !== null ? format_string($record->coursename)
            : get_string('deletedcoursefallback', 'tool_enrolsuspension', $record->courseid);
        $row[] = format_text($record->reason, FORMAT_PLAIN);
        $row[] = userdate($record->timecreated);
        if ($record->creatorfirstname !== null) {
            $creator = (object) ['firstname' => $record->creatorfirstname, 'lastname' => $record->creatorlastname];
            $row[] = fullname($creator);
        } else {
            $row[] = get_string('unknownoperator', 'tool_enrolsuspension');
        }
        if ($status !== 1) {
            if ($record->timereactivated) {
                $label = userdate($record->timereactivated);
                if ($record->reactivatorfirstname !== null) {
                    $reactivator = (object) [
                        'firstname' => $record->reactivatorfirstname,
                        'lastname' => $record->reactivatorlastname,
                    ];
                    $label .= '<br><small>' . s(fullname($reactivator)) . '</small>';
                }
                $row[] = $label;
            } else {
                $row[] = '-';
            }
        }
        $table->data[] = $row;
    }
    echo html_writer::table($table);
    if ($canreactivate) {
        echo html_writer::tag('button', get_string('reactivateselected', 'tool_enrolsuspension'),
            ['type' => 'submit', 'class' => 'btn btn-success']);
        echo html_writer::end_tag('form');
    }
    echo $OUTPUT->paging_bar($total, $page, $perpage,
        new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $status]));
}
echo $OUTPUT->footer();
