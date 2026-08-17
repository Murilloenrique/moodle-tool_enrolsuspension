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
 * Suspension review page.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:suspend', $context);

if (data_submitted()) {
    require_sesskey();
    $token = required_param('op', PARAM_ALPHANUM);
    $reason = trim(required_param('reason', PARAM_TEXT));
    try {
        \tool_enrolsuspension\local\operation_manager::freeze($token, $USER->id, $reason);
    } catch (moodle_exception $exception) {
        redirect(new moodle_url('/admin/tool/enrolsuspension/options.php', ['op' => $token]),
            $exception->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
    redirect(new moodle_url('/admin/tool/enrolsuspension/review.php', ['op' => $token]));
}

$token = required_param('op', PARAM_ALPHANUM);
$operation = \tool_enrolsuspension\local\operation_manager::get($token, $USER->id);
if (!in_array((int) $operation->status, [
        \tool_enrolsuspension\local\operation_manager::STATUS_READY,
        \tool_enrolsuspension\local\operation_manager::STATUS_BLOCKED,
    ], true)) {
    redirect(new moodle_url('/admin/tool/enrolsuspension/options.php', ['op' => $token]));
}
$userids = \tool_enrolsuspension\local\operation_manager::userids($operation);
$courseids = \tool_enrolsuspension\local\operation_manager::courseids($operation);
$items = \tool_enrolsuspension\local\operation_manager::items($operation->id);

[$userssql, $usersparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
$users = $DB->get_records_select('user', "id {$userssql}", $usersparams, '', 'id,firstname,lastname,email');
[$coursessql, $coursesparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
$courses = $DB->get_records_select('course', "id {$coursessql}", $coursesparams, '', 'id,fullname,shortname');

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/review.php', ['op' => $token]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('review', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('review', 'tool_enrolsuspension'));

$pairs = [];
foreach ($items as $item) {
    $pairs[$item->userid . ':' . $item->courseid] = true;
}
$summary = new html_table();
$summary->head = [get_string('summaryitem', 'tool_enrolsuspension'), get_string('details', 'tool_enrolsuspension')];
$summary->data[] = [get_string('selectedusers', 'tool_enrolsuspension'), count($users)];
$summary->data[] = [get_string('selectcourses', 'tool_enrolsuspension'), count($courses)];
$summary->data[] = [get_string('usercoursepairstoaffect', 'tool_enrolsuspension'), count($pairs)];
$summary->data[] = [get_string('enrolmentlinkstoaffect', 'tool_enrolsuspension'), count($items)];
$summary->data[] = [get_string('reason', 'tool_enrolsuspension'), s($operation->reason)];
$summary->data[] = [get_string('suspensiontype', 'tool_enrolsuspension'), get_string('permanent', 'tool_enrolsuspension')];
echo html_writer::table($summary);

if ($items) {
    echo $OUTPUT->heading(get_string('enrolmentdetails', 'tool_enrolsuspension'), 3);
    $details = new html_table();
    $details->head = [get_string('user'), get_string('course'), get_string('enrolmentmethod', 'tool_enrolsuspension'),
        get_string('supportstatus', 'tool_enrolsuspension')];
    $stringmanager = get_string_manager();
    foreach ($items as $item) {
        $userlabel = isset($users[$item->userid]) ? fullname($users[$item->userid]) : '#' . $item->userid;
        $courselabel = isset($courses[$item->courseid])
            ? format_string($courses[$item->courseid]->fullname)
            : '#' . $item->courseid;
        $component = 'enrol_' . $item->enroltype;
        $methodlabel = $stringmanager->string_exists('pluginname', $component)
            ? get_string('pluginname', $component) : s($item->enroltype);
        $reasonkey = 'supportreason_' . $item->supportreason;
        $reasonlabel = get_string_manager()->string_exists($reasonkey, 'tool_enrolsuspension')
            ? get_string($reasonkey, 'tool_enrolsuspension')
            : $item->supportreason;
        $supportlabel = (int) $item->supported
            ? html_writer::span(get_string('supported', 'tool_enrolsuspension'), 'text-success font-weight-bold')
            : html_writer::span(
                get_string('unsupportedmethodreason', 'tool_enrolsuspension', $reasonlabel),
                'text-danger font-weight-bold'
            );
        $details->data[] = [$userlabel, $courselabel, $methodlabel, $supportlabel];
    }
    echo html_writer::table($details);
}

if ((int) $operation->status === \tool_enrolsuspension\local\operation_manager::STATUS_BLOCKED) {
    echo $OUTPUT->notification(get_string('unsupportedoperationblocked', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_ERROR);
    echo html_writer::link(
        new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]),
        get_string('back'),
        ['class' => 'btn btn-secondary']
    );
} else {
    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => new moodle_url('/admin/tool/enrolsuspension/execute.php'),
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'op', 'value' => $token]);
    echo html_writer::link(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]),
        get_string('back'), ['class' => 'btn btn-secondary', 'style' => 'margin-right: 12px;']);
    echo html_writer::tag('button', get_string('confirmsuspension', 'tool_enrolsuspension'),
        ['type' => 'submit', 'class' => 'btn btn-danger']);
    echo html_writer::end_tag('form');
}
echo $OUTPUT->footer();
