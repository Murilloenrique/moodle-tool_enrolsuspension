<?php
require_once(__DIR__ . '/../../../config.php');
require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);
$status = optional_param('status', 1, PARAM_INT);

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $status]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('history', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

$params = ['status' => $status];
$sql = "SELECT s.*, u.firstname, u.lastname, u.email, c.fullname AS coursename,
               creator.firstname AS creatorfirstname, creator.lastname AS creatorlastname
          FROM {tool_enrolsuspension} s
          JOIN {user} u ON u.id = s.userid
          JOIN {course} c ON c.id = s.courseid
          JOIN {user} creator ON creator.id = s.createdby
         WHERE s.status = :status
      ORDER BY s.timecreated DESC";
$records = $DB->get_records_sql($sql, $params, 0, 500);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history', 'tool_enrolsuspension'));
echo html_writer::div(
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => 1]), get_string('activesuspensions', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-primary mr-2']) .
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => 0]), get_string('reactivatedsuspensions', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-secondary']),
    'mb-3'
);

if (!$records) {
    echo $OUTPUT->notification(get_string('nohistoryrecords', 'tool_enrolsuspension'), \core\output\notification::NOTIFY_INFO);
} else {
    if ($status === 1) {
        echo html_writer::start_tag('form', ['method' => 'post', 'action' => new moodle_url('/admin/tool/enrolsuspension/reactivate.php')]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    }
    $table = new html_table();
    $table->head = array_filter([
        $status === 1 ? get_string('select') : null,
        get_string('user'), get_string('course'), get_string('reason', 'tool_enrolsuspension'),
        get_string('suspendedon', 'tool_enrolsuspension'), get_string('performedby', 'tool_enrolsuspension'),
    ]);
    foreach ($records as $record) {
        $row = [];
        if ($status === 1) {
            $row[] = html_writer::checkbox('suspensionids[]', $record->id, false, '');
        }
        $row[] = fullname($record) . '<br><small>' . s($record->email) . '</small>';
        $row[] = format_string($record->coursename);
        $row[] = format_text($record->reason, FORMAT_PLAIN);
        $row[] = userdate($record->timecreated);
        $creator = (object)['firstname' => $record->creatorfirstname, 'lastname' => $record->creatorlastname];
        $row[] = fullname($creator);
        $table->data[] = $row;
    }
    echo html_writer::table($table);
    if ($status === 1) {
        echo html_writer::tag('button', get_string('reactivateselected', 'tool_enrolsuspension'), ['type' => 'submit', 'class' => 'btn btn-success']);
        echo html_writer::end_tag('form');
    }
}
echo $OUTPUT->footer();
