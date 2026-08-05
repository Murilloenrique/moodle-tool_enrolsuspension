<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Suspension review page.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_capability('tool/enrolsuspension:manage', $context);
require_sesskey();

$userids = $SESSION->tool_enrolsuspension_selectedusers ?? [];
$courseids = $SESSION->tool_enrolsuspension_selectedcourses ?? [];
$reason = trim(required_param('reason', PARAM_TEXT));

if (empty($userids) || empty($courseids) || $reason === '') {
    redirect(new moodle_url('/admin/tool/enrolsuspension/index.php'));
}

$SESSION->tool_enrolsuspension_reason = $reason;

[$userssql, $usersparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
$users = $DB->get_records_select(
    'user',
    "id {$userssql} AND deleted = 0",
    $usersparams,
    'firstname ASC, lastname ASC',
    'id, firstname, lastname, email'
);

[$coursessql, $coursesparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
$courses = $DB->get_records_select(
    'course',
    "id {$coursessql}",
    $coursesparams,
    'fullname ASC',
    'id, fullname, shortname'
);

$enrolments = \tool_enrolsuspension\local\manager::get_active_enrolments($userids, $courseids);

// A user can have more than one active enrolment record in the same course,
// for example through manual enrolment and cohort synchronisation.
$usercoursepairs = [];
foreach ($enrolments as $enrolment) {
    $usercoursepairs[$enrolment->userid . ':' . $enrolment->courseid] = true;
}

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/review.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('review', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('review', 'tool_enrolsuspension'));

$summarytable = new html_table();
$summarytable->head = [
    get_string('summaryitem', 'tool_enrolsuspension'),
    get_string('details', 'tool_enrolsuspension'),
];
$summarytable->data[] = [get_string('selectedusers', 'tool_enrolsuspension'), count($users)];
$summarytable->data[] = [get_string('selectcourses', 'tool_enrolsuspension'), count($courses)];
$summarytable->data[] = [
    get_string('usercoursepairstoaffect', 'tool_enrolsuspension'),
    count($usercoursepairs),
];
$summarytable->data[] = [
    get_string('enrolmentlinkstoaffect', 'tool_enrolsuspension'),
    count($enrolments),
];
$summarytable->data[] = [get_string('reason', 'tool_enrolsuspension'), s($reason)];
$summarytable->data[] = [
    get_string('suspensiontype', 'tool_enrolsuspension'),
    get_string('permanent', 'tool_enrolsuspension'),
];
echo html_writer::table($summarytable);

if (count($enrolments) > count($usercoursepairs)) {
    $notice = (object) [
        'links' => count($enrolments),
        'pairs' => count($usercoursepairs),
    ];

    echo $OUTPUT->notification(
        get_string('overlappingenrolmentsnotice', 'tool_enrolsuspension', $notice),
        \core\output\notification::NOTIFY_INFO
    );
}

if (!empty($enrolments)) {
    echo $OUTPUT->heading(get_string('enrolmentdetails', 'tool_enrolsuspension'), 3);

    $detailtable = new html_table();
    $detailtable->head = [
        get_string('user'),
        get_string('course'),
        get_string('enrolmentmethod', 'tool_enrolsuspension'),
    ];

    $stringmanager = get_string_manager();

    foreach ($enrolments as $enrolment) {
        $userlabel = isset($users[$enrolment->userid])
            ? fullname($users[$enrolment->userid])
            : (string) $enrolment->userid;

        $courselabel = isset($courses[$enrolment->courseid])
            ? format_string($courses[$enrolment->courseid]->fullname)
            : format_string($enrolment->fullname);

        $component = 'enrol_' . $enrolment->enrol;
        $methodlabel = $stringmanager->string_exists('pluginname', $component)
            ? get_string('pluginname', $component)
            : s($enrolment->enrol);

        if (!empty($enrolment->enrolinstancename)) {
            $methodlabel .= ' — ' . format_string($enrolment->enrolinstancename);
        }

        $detailtable->data[] = [
            $userlabel,
            $courselabel,
            $methodlabel,
        ];
    }

    echo html_writer::table($detailtable);
}

echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => new moodle_url('/admin/tool/enrolsuspension/execute.php'),
]);
echo html_writer::empty_tag('input', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey(),
]);
echo html_writer::link(
    new moodle_url('/admin/tool/enrolsuspension/courses.php'),
    get_string('back'),
    ['class' => 'btn btn-secondary mr-2']
);
echo html_writer::tag(
    'button',
    get_string('confirmsuspension', 'tool_enrolsuspension'),
    ['type' => 'submit', 'class' => 'btn btn-danger']
);
echo html_writer::end_tag('form');

echo $OUTPUT->footer();
