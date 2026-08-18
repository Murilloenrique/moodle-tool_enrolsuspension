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
 * Course selection page.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:suspend', $context);

$token = required_param('op', PARAM_ALPHANUM);
$operation = \tool_enrolsuspension\local\operation_manager::get($token, $USER->id);
$userids = \tool_enrolsuspension\local\operation_manager::userids($operation);
$coursemap = \tool_enrolsuspension\local\manager::get_course_map(
    $userids,
    (int) $operation->forcedcourseid
);

[$userssql, $usersparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid');
$users = $DB->get_records_select(
    'user',
    "id {$userssql} AND deleted = 0",
    $usersparams,
    'firstname ASC, lastname ASC',
    'id, firstname, lastname, email, username, idnumber'
);

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $token]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('selectcourses', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));
$PAGE->requires->js_call_amd('tool_enrolsuspension/course_selector', 'init');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('selectcourses', 'tool_enrolsuspension'));
echo $OUTPUT->heading(get_string('selectedusers', 'tool_enrolsuspension'), 3);

$useritems = [];
foreach ($users as $user) {
    $useritems[] = fullname($user) . ' — ' . s($user->email);
}

echo html_writer::alist($useritems);
echo $OUTPUT->heading(get_string('availablecourses', 'tool_enrolsuspension'), 3);

if (!$coursemap) {
    echo $OUTPUT->notification(
        get_string('nocoursesfound', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_INFO
    );
} else {
    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => new moodle_url('/admin/tool/enrolsuspension/options.php'),
    ]);
    echo html_writer::empty_tag('input', [
        'type' => 'hidden',
        'name' => 'sesskey',
        'value' => sesskey(),
    ]);
    echo html_writer::empty_tag('input', [
        'type' => 'hidden',
        'name' => 'op',
        'value' => $token,
    ]);

    $table = new html_table();
    $table->head = [
        get_string('select'),
        get_string('course'),
        get_string('shortnamecourse', 'tool_enrolsuspension'),
        get_string('enrolledselectedusers', 'tool_enrolsuspension'),
    ];

    foreach ($coursemap as $courseid => $info) {
        $enrolleduserids = array_map('intval', array_values($info['userids']));
        $enrollednames = [];
        $notenrollednames = [];

        foreach ($users as $user) {
            if (in_array((int) $user->id, $enrolleduserids, true)) {
                $enrollednames[] = s(fullname($user));
            } else {
                $notenrollednames[] = s(fullname($user));
            }
        }

        $details = html_writer::div(
            count($enrolleduserids) . ' / ' . count($users),
            'font-weight-bold mb-1'
        );
        $details .= html_writer::div(
            html_writer::span(
                get_string('enrolledlabel', 'tool_enrolsuspension') . ':',
                'font-weight-bold text-success'
            ) . ' ' . implode(', ', $enrollednames),
            'mb-1'
        );

        if ($notenrollednames) {
            $details .= html_writer::div(
                html_writer::span(
                    get_string('notenrolledlabel', 'tool_enrolsuspension') . ':',
                    'font-weight-bold text-muted'
                ) . ' ' . implode(', ', $notenrollednames),
                'small text-muted'
            );
        }

        $table->data[] = [
            html_writer::checkbox(
                'selectedcourses[]',
                $courseid,
                false,
                '',
                ['id' => 'course_' . $courseid]
            ),
            format_string($info['course']->fullname),
            format_string($info['course']->shortname),
            $details,
        ];
    }

    echo html_writer::table($table);

    $allcheckbox = html_writer::checkbox(
        'allcurrentcourses',
        1,
        false,
        '',
        ['id' => 'allcurrentcourses']
    );
    $alllabel = html_writer::tag(
        'label',
        get_string('suspendallcurrentcourses', 'tool_enrolsuspension'),
        [
            'for' => 'allcurrentcourses',
            'class' => 'mb-0',
            'style' => 'margin-left: 8px;',
        ]
    );
    echo html_writer::div($allcheckbox . $alllabel, 'mb-4 d-flex align-items-center');

    echo html_writer::link(
        new moodle_url('/admin/tool/enrolsuspension/index.php'),
        get_string('back'),
        [
            'class' => 'btn btn-secondary',
            'style' => 'margin-right: 12px;',
        ]
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
