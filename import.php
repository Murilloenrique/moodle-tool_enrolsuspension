<?php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);

class tool_enrolsuspension_csv_form extends moodleform {
    public function definition(): void {
        $mform = $this->_form;
        $mform->addElement('filepicker', 'csvfile', get_string('csvfile', 'tool_enrolsuspension'), null,
            ['accepted_types' => ['.csv', '.txt'], 'maxbytes' => 1048576]);
        $mform->addRule('csvfile', get_string('required'), 'required');
        $this->add_action_buttons(false, get_string('importcsv', 'tool_enrolsuspension'));
    }
}

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/import.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('importcsv', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));
$form = new tool_enrolsuspension_csv_form();
if ($data = $form->get_data()) {
    $content = $form->get_file_content('csvfile');
    $values = preg_split('/[\r\n,;]+/', (string)$content, -1, PREG_SPLIT_NO_EMPTY);
    $values = array_values(array_unique(array_filter(array_map('trim', $values))));
    $userids = [];
    foreach ($values as $value) {
        if (in_array(core_text::strtolower($value), ['username', 'email', 'idnumber', 'matricula', 'matrícula'], true)) {
            continue;
        }
        $user = $DB->get_record_sql(
            "SELECT id FROM {user} WHERE deleted = 0 AND (username = :v1 OR email = :v2 OR idnumber = :v3)",
            ['v1' => $value, 'v2' => $value, 'v3' => $value]
        );
        if ($user) {
            $userids[] = (int)$user->id;
        }
    }
    $userids = array_values(array_unique($userids));
    if ($userids) {
        $SESSION->tool_enrolsuspension_selectedusers = $userids;
        redirect(new moodle_url('/admin/tool/enrolsuspension/courses.php'),
            get_string('csvusersfound', 'tool_enrolsuspension', count($userids)), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }
    redirect(new moodle_url('/admin/tool/enrolsuspension/import.php'),
        get_string('csvnousers', 'tool_enrolsuspension'), null,
        \core\output\notification::NOTIFY_ERROR);
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importcsv', 'tool_enrolsuspension'));
echo $OUTPUT->notification(get_string('csvinstructions', 'tool_enrolsuspension'), \core\output\notification::NOTIFY_INFO);
$form->display();
echo $OUTPUT->footer();
