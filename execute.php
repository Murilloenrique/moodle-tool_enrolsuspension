<?php
require_once(__DIR__ . '/../../../config.php');
require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);
require_sesskey();

$userids = $SESSION->tool_enrolsuspension_selectedusers ?? [];
$courseids = $SESSION->tool_enrolsuspension_selectedcourses ?? [];
$reason = $SESSION->tool_enrolsuspension_reason ?? '';
if (!$userids || !$courseids || $reason === '') {
    redirect(new moodle_url('/admin/tool/enrolsuspension/index.php'));
}
$result = \tool_enrolsuspension\local\manager::suspend($userids, $courseids, $reason, $USER->id);
unset($SESSION->tool_enrolsuspension_selectedusers, $SESSION->tool_enrolsuspension_selectedcourses, $SESSION->tool_enrolsuspension_reason, $SESSION->tool_enrolsuspension_forcedcourse);

$message = get_string('suspensionsuccess', 'tool_enrolsuspension', $result['suspended']);
if ($result['skipped']) {
    $message .= ' ' . get_string('suspensionsskipped', 'tool_enrolsuspension', $result['skipped']);
}
redirect(new moodle_url('/admin/tool/enrolsuspension/history.php'), $message, null,
    $result['suspended'] ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_WARNING);
