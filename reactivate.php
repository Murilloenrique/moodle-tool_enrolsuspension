<?php
require_once(__DIR__ . '/../../../config.php');
require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);
require_sesskey();
$ids = optional_param_array('suspensionids', [], PARAM_INT);
$result = \tool_enrolsuspension\local\manager::reactivate($ids, $USER->id);
redirect(new moodle_url('/admin/tool/enrolsuspension/history.php'),
    get_string('reactivationsuccess', 'tool_enrolsuspension', $result['reactivated']), null,
    \core\output\notification::NOTIFY_SUCCESS);
