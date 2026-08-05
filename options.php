<?php
require_once(__DIR__ . '/../../../config.php');
require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);
require_sesskey();

$allcurrent = optional_param('allcurrentcourses', 0, PARAM_BOOL);
$courseids = optional_param_array('selectedcourses', [], PARAM_INT);
$userids = $SESSION->tool_enrolsuspension_selectedusers ?? [];
if (!$userids) {
    redirect(new moodle_url('/admin/tool/enrolsuspension/index.php'));
}

if ($allcurrent) {
    $courseids = [];
    foreach ($userids as $userid) {
        foreach (enrol_get_users_courses($userid, true, 'id') as $course) {
            if ((int)$course->id !== SITEID) {
                $courseids[] = (int)$course->id;
            }
        }
    }
}
$courseids = array_values(array_unique(array_filter(array_map('intval', $courseids))));
if (!$courseids) {
    redirect(new moodle_url('/admin/tool/enrolsuspension/courses.php'),
        get_string('selectatleastonecourse', 'tool_enrolsuspension'), null,
        \core\output\notification::NOTIFY_ERROR);
}
$SESSION->tool_enrolsuspension_selectedcourses = $courseids;

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/options.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('suspensiondetails', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('suspensiondetails', 'tool_enrolsuspension'));
echo html_writer::start_tag('form', ['method' => 'post', 'action' => new moodle_url('/admin/tool/enrolsuspension/review.php')]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::tag('label', get_string('reason', 'tool_enrolsuspension'), ['for' => 'reason', 'class' => 'font-weight-bold']);
echo html_writer::tag('textarea', '', ['id' => 'reason', 'name' => 'reason', 'rows' => 4, 'class' => 'form-control mb-3', 'required' => 'required', 'maxlength' => 1000]);
echo html_writer::div(get_string('permanentsuspensionnotice', 'tool_enrolsuspension'), 'alert alert-warning');
echo html_writer::link(new moodle_url('/admin/tool/enrolsuspension/courses.php'), get_string('back'), ['class' => 'btn btn-secondary mr-2']);
echo html_writer::tag('button', get_string('review', 'tool_enrolsuspension'), ['type' => 'submit', 'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');
echo $OUTPUT->footer();
