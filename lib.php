<?php
/** Add a suspension shortcut to course navigation. */
function tool_enrolsuspension_extend_navigation_course($navigation, $course, $context): void {
    if (!has_capability('tool/enrolsuspension:manage', context_system::instance())) {
        return;
    }
    $url = new moodle_url('/admin/tool/enrolsuspension/index.php', ['courseid' => $course->id]);
    $navigation->add(get_string('coursesuspensions', 'tool_enrolsuspension'), $url,
        navigation_node::TYPE_SETTING, null, 'tool_enrolsuspension');
}
