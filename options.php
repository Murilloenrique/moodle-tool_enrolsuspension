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
 * Suspension options page.
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
    $operation = \tool_enrolsuspension\local\operation_manager::get($token, $USER->id);
    $allcurrent = optional_param('allcurrentcourses', 0, PARAM_BOOL);
    $courseids = optional_param_array('selectedcourses', [], PARAM_INT);
    if ($allcurrent) {
        $map = \tool_enrolsuspension\local\manager::get_course_map(
            \tool_enrolsuspension\local\operation_manager::userids($operation),
            (int) $operation->forcedcourseid
        );
        $courseids = array_keys($map);
    }
    try {
        \tool_enrolsuspension\local\operation_manager::set_courses($token, $USER->id, $courseids);
    } catch (moodle_exception $exception) {
        redirect(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]),
            $exception->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
    }
    redirect(new moodle_url('/admin/tool/enrolsuspension/options.php', ['op' => $token]));
}

$token = required_param('op', PARAM_ALPHANUM);
$operation = \tool_enrolsuspension\local\operation_manager::get($token, $USER->id);
if (!\tool_enrolsuspension\local\operation_manager::courseids($operation)) {
    redirect(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]));
}

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/options.php', ['op' => $token]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('suspensiondetails', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('suspensiondetails', 'tool_enrolsuspension'));
echo html_writer::start_tag('form', ['method' => 'post', 'action' => new moodle_url('/admin/tool/enrolsuspension/review.php')]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'op', 'value' => $token]);
echo html_writer::tag('label', get_string('reason', 'tool_enrolsuspension'),
    ['for' => 'reason', 'class' => 'font-weight-bold']);
echo html_writer::tag('textarea', s($operation->reason ?? ''), [
    'id' => 'reason', 'name' => 'reason', 'rows' => 4, 'class' => 'form-control mb-3',
    'required' => 'required', 'maxlength' => 1000,
]);
echo html_writer::div(get_string('permanentsuspensionnotice', 'tool_enrolsuspension'), 'alert alert-warning');
echo html_writer::link(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]),
    get_string('back'), ['class' => 'btn btn-secondary', 'style' => 'margin-right: 12px;']);
echo html_writer::tag('button', get_string('review', 'tool_enrolsuspension'),
    ['type' => 'submit', 'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');
echo $OUTPUT->footer();
