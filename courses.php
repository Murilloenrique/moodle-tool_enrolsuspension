<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Course selection page.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();

require_capability('tool/enrolsuspension:manage', $context);

$PAGE->set_url(
    new moodle_url('/admin/tool/enrolsuspension/courses.php')
);

$PAGE->set_context($context);

$PAGE->set_title(
    get_string('selectcourses', 'tool_enrolsuspension')
);

$PAGE->set_heading(
    get_string('pluginname', 'tool_enrolsuspension')
);

$selecteduserids =
    $SESSION->tool_enrolsuspension_selectedusers ?? [];

if (empty($selecteduserids)) {
    redirect(
        new moodle_url('/admin/tool/enrolsuspension/index.php'),
        get_string('selectusersfirst', 'tool_enrolsuspension'),
        null,
        \core\output\notification::NOTIFY_WARNING
    );
}

[$userssql, $usersparams] = $DB->get_in_or_equal(
    $selecteduserids,
    SQL_PARAMS_NAMED,
    'userid'
);

$users = $DB->get_records_select(
    'user',
    "id {$userssql} AND deleted = 0",
    $usersparams,
    'firstname ASC, lastname ASC',
    'id, firstname, lastname, email, username, idnumber'
);

$coursemap = [];
$forcedcourseid = $SESSION->tool_enrolsuspension_forcedcourse ?? 0;

foreach ($users as $user) {
    $usercourses = enrol_get_users_courses(
        $user->id,
        true,
        'id, fullname, shortname, visible'
    );

    foreach ($usercourses as $course) {
        if ((int) $course->id === SITEID || ($forcedcourseid && (int)$course->id !== (int)$forcedcourseid)) {
            continue;
        }

        if (!isset($coursemap[$course->id])) {
            $coursemap[$course->id] = [
                'course' => $course,
                'userids' => [],
            ];
        }

        $coursemap[$course->id]['userids'][] = (int) $user->id;
    }
}

uasort($coursemap, static function(array $first, array $second): int {
    return strcasecmp(
        $first['course']->fullname,
        $second['course']->fullname
    );
});

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('selectcourses', 'tool_enrolsuspension')
);

echo $OUTPUT->heading(
    get_string('selectedusers', 'tool_enrolsuspension'),
    3
);

$useritems = [];

foreach ($users as $user) {
    $useritems[] = fullname($user) . ' — ' . s($user->email);
}

echo html_writer::alist($useritems);

echo $OUTPUT->heading(
    get_string('availablecourses', 'tool_enrolsuspension'),
    3
);

if (empty($coursemap)) {
    echo $OUTPUT->notification(
        get_string('nocoursesfound', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_INFO
    );
} else {
    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => new moodle_url(
            '/admin/tool/enrolsuspension/options.php'
        ),
    ]);

    echo html_writer::empty_tag('input', [
        'type' => 'hidden',
        'name' => 'sesskey',
        'value' => sesskey(),
    ]);

    $table = new html_table();

    $table->head = [
        get_string('select'),
        get_string('course'),
        get_string('shortnamecourse'),
        get_string('enrolledselectedusers', 'tool_enrolsuspension'),
    ];

    foreach ($coursemap as $courseid => $courseinformation) {
        $course = $courseinformation['course'];
        $enrolleduserids = array_values(array_unique($courseinformation['userids']));
        $enrolledcount = count($enrolleduserids);

        $enrollednames = [];
        $notenrollednames = [];

        foreach ($users as $user) {
            $escapedname = s(fullname($user));

            if (in_array((int) $user->id, $enrolleduserids, true)) {
                $enrollednames[] = $escapedname;
            } else {
                $notenrollednames[] = $escapedname;
            }
        }

        $enrolmentdetails = html_writer::div(
            $enrolledcount . ' / ' . count($users),
            'font-weight-bold mb-1'
        );

        $enrolmentdetails .= html_writer::div(
            html_writer::span(
                get_string('enrolledlabel', 'tool_enrolsuspension') . ':',
                'font-weight-bold text-success'
            ) . ' ' . implode(', ', $enrollednames),
            'mb-1'
        );

        if (!empty($notenrollednames)) {
            $enrolmentdetails .= html_writer::div(
                html_writer::span(
                    get_string('notenrolledlabel', 'tool_enrolsuspension') . ':',
                    'font-weight-bold text-muted'
                ) . ' ' . implode(', ', $notenrollednames),
                'small text-muted'
            );
        }

        $checkbox = html_writer::checkbox(
            'selectedcourses[]',
            $courseid,
            false,
            '',
            ['id' => 'course_' . $courseid]
        );

        $table->data[] = [
            $checkbox,
            format_string($course->fullname),
            format_string($course->shortname),
            $enrolmentdetails,
        ];
    }

    echo html_writer::table($table);

    $allcurrentcoursescheckbox = html_writer::checkbox(
        'allcurrentcourses',
        1,
        false,
        '',
        ['id' => 'allcurrentcourses']
    );

    $allcurrentcourseslabel = html_writer::tag(
        'label',
        get_string(
            'suspendallcurrentcourses',
            'tool_enrolsuspension'
        ),
        [
            'for' => 'allcurrentcourses',
            'class' => 'mb-0',
            'style' => 'margin-left: 8px;',
        ]
    );

    echo html_writer::div(
        $allcurrentcoursescheckbox . $allcurrentcourseslabel,
        'mb-4 d-flex align-items-center'
    );

    echo html_writer::link(
        new moodle_url(
            '/admin/tool/enrolsuspension/index.php'
        ),
        get_string('back'),
        ['class' => 'btn btn-secondary mr-2']
    );

    echo html_writer::tag(
        'button',
        get_string('next', 'tool_enrolsuspension'),
        [
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]
    );

    echo html_writer::end_tag('form');
}

echo $OUTPUT->footer();