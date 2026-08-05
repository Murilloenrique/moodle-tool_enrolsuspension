<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Main page for the Enrol Suspension plugin.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
$courseid = optional_param('courseid', 0, PARAM_INT);
if ($courseid) {
    $SESSION->tool_enrolsuspension_forcedcourse = $courseid;
} else {
    unset($SESSION->tool_enrolsuspension_forcedcourse);
}

require_capability('tool/enrolsuspension:manage', $context);

$PAGE->set_url(
    new moodle_url('/admin/tool/enrolsuspension/index.php')
);

$PAGE->set_context($context);

$PAGE->set_title(
    get_string('pluginname', 'tool_enrolsuspension')
);

$PAGE->set_heading(
    get_string('pluginname', 'tool_enrolsuspension')
);

$languagestrings = [
    'nousersfound' => get_string(
        'nousersfound',
        'tool_enrolsuspension'
    ),
    'nousersselected' => get_string(
        'nousersselected',
        'tool_enrolsuspension'
    ),
    'removeuser' => get_string(
        'removeuser',
        'tool_enrolsuspension'
    ),
    'searching' => get_string(
        'searching',
        'tool_enrolsuspension'
    ),
    'selectuser' => get_string(
        'selectuser',
        'tool_enrolsuspension'
    ),
    'idnumberlabel' => get_string(
        'idnumberlabel',
        'tool_enrolsuspension'
    ),
];

$PAGE->requires->js_call_amd(
    'tool_enrolsuspension/user_selector',
    'init',
    [$languagestrings]
);

$searchform = new \tool_enrolsuspension\form\search_users_form();

if ($data = $searchform->get_data()) {
    require_sesskey();

    $selecteduserids = array_filter(
        array_map(
            'intval',
            explode(',', $data->selecteduserids)
        )
    );

    if (empty($selecteduserids)) {
        redirect(
            new moodle_url('/admin/tool/enrolsuspension/index.php'),
            get_string(
                'selectatleastoneuser',
                'tool_enrolsuspension'
            ),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }

    $SESSION->tool_enrolsuspension_selectedusers = $selecteduserids;

    redirect(
        new moodle_url(
            '/admin/tool/enrolsuspension/courses.php'
        )
    );
}

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('dashboard', 'tool_enrolsuspension')
);

$searchform->display();

echo html_writer::div(
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/import.php'), get_string('importcsv', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-secondary mr-2']) .
    html_writer::link(new moodle_url('/admin/tool/enrolsuspension/history.php'), get_string('history', 'tool_enrolsuspension'), ['class' => 'btn btn-outline-secondary']),
    'mt-4'
);

echo $OUTPUT->footer();