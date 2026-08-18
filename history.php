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
 * Paginated suspension history.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:view', $context);

$status = optional_param(
    'status',
    \tool_enrolsuspension\local\history_manager::STATUS_ALL,
    PARAM_INT
);
$status = \tool_enrolsuspension\local\history_manager::normalise_status($status);
$page = max(0, optional_param('page', 0, PARAM_INT));
$perpage = 50;

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $status, 'page' => $page]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('history', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

$total = \tool_enrolsuspension\local\history_manager::count_records($status);
$records = \tool_enrolsuspension\local\history_manager::get_records($status, $page * $perpage, $perpage);
$allcount = \tool_enrolsuspension\local\history_manager::count_records(
    \tool_enrolsuspension\local\history_manager::STATUS_ALL
);
$activecount = \tool_enrolsuspension\local\history_manager::count_records(
    \tool_enrolsuspension\local\manager::STATUS_SUSPENDED
);
$reactivatedcount = \tool_enrolsuspension\local\history_manager::count_records(
    \tool_enrolsuspension\local\manager::STATUS_REACTIVATED
);
$stalecount = \tool_enrolsuspension\local\history_manager::count_records(
    \tool_enrolsuspension\local\manager::STATUS_STALE
);
$operationsequencemap = \tool_enrolsuspension\local\history_manager::operation_sequence_map();

$filterbutton = static function(int $filterstatus, string $label, int $count) use ($status): string {
    $class = $status === $filterstatus ? 'btn btn-primary mr-2' : 'btn btn-outline-secondary mr-2';
    return html_writer::link(
        new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $filterstatus]),
        $label . ' (' . $count . ')',
        ['class' => $class]
    );
};

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history', 'tool_enrolsuspension'));

echo html_writer::div(
    get_string('historyoperationexplanation', 'tool_enrolsuspension'),
    'mb-3 text-muted'
);

echo html_writer::div(
    html_writer::link(
        new moodle_url('/admin/tool/enrolsuspension/history_export.php', ['status' => $status]),
        get_string('exporthistoryexcel', 'tool_enrolsuspension'),
        ['class' => 'btn btn-success']
    ),
    'mb-3'
);

echo html_writer::div(
    $filterbutton(
        \tool_enrolsuspension\local\history_manager::STATUS_ALL,
        get_string('allhistoryrecords', 'tool_enrolsuspension'),
        $allcount
    )
    . $filterbutton(
        \tool_enrolsuspension\local\manager::STATUS_SUSPENDED,
        get_string('activesuspensions', 'tool_enrolsuspension'),
        $activecount
    )
    . $filterbutton(
        \tool_enrolsuspension\local\manager::STATUS_REACTIVATED,
        get_string('reactivatedsuspensions', 'tool_enrolsuspension'),
        $reactivatedcount
    )
    . $filterbutton(
        \tool_enrolsuspension\local\manager::STATUS_STALE,
        get_string('stalesuspensions', 'tool_enrolsuspension'),
        $stalecount
    ),
    'mb-3'
);

if (
    $status === \tool_enrolsuspension\local\history_manager::STATUS_ALL
    || $status === \tool_enrolsuspension\local\manager::STATUS_STALE
) {
    echo $OUTPUT->notification(
        get_string('staleexplanation', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_INFO
    );
}

if (!$records) {
    echo $OUTPUT->notification(
        get_string('nohistoryrecords', 'tool_enrolsuspension'),
        \core\output\notification::NOTIFY_INFO
    );
} else {
    $canreactivate = has_capability('tool/enrolsuspension:reactivate', $context)
        && in_array(
            $status,
            [
                \tool_enrolsuspension\local\history_manager::STATUS_ALL,
                \tool_enrolsuspension\local\manager::STATUS_SUSPENDED,
            ],
            true
        );

    if ($canreactivate) {
        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => new moodle_url('/admin/tool/enrolsuspension/reactivate.php'),
        ]);
        echo html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => 'sesskey',
            'value' => sesskey(),
        ]);
    }

    $table = new html_table();
    $table->head = [];
    if ($canreactivate) {
        $table->head[] = get_string('select');
    }
    $table->head[] = get_string('operation', 'tool_enrolsuspension');
    $table->head[] = get_string('status');
    $table->head[] = get_string('user');
    $table->head[] = get_string('course');
    $table->head[] = get_string('enrolmentmethod', 'tool_enrolsuspension');
    $table->head[] = get_string('reason', 'tool_enrolsuspension');
    $table->head[] = get_string('suspendedon', 'tool_enrolsuspension');
    $table->head[] = get_string('performedby', 'tool_enrolsuspension');
    $table->head[] = get_string('reactivationinfo', 'tool_enrolsuspension');

    foreach ($records as $record) {
        $row = [];
        if ($canreactivate) {
            if ((int) $record->status === \tool_enrolsuspension\local\manager::STATUS_SUSPENDED) {
                $row[] = html_writer::checkbox('suspensionids[]', $record->id, false, '');
            } else {
                $row[] = '';
            }
        }

        $row[] = \tool_enrolsuspension\local\history_manager::operation_label(
            (int) $record->operationid,
            $operationsequencemap
        );

        $statuslabel = \tool_enrolsuspension\local\history_manager::status_label((int) $record->status);
        if ((int) $record->status === \tool_enrolsuspension\local\manager::STATUS_SUSPENDED) {
            $row[] = html_writer::span($statuslabel, 'text-danger font-weight-bold');
        } elseif ((int) $record->status === \tool_enrolsuspension\local\manager::STATUS_REACTIVATED) {
            $row[] = html_writer::span($statuslabel, 'text-success font-weight-bold');
        } else {
            $row[] = html_writer::span($statuslabel, 'text-muted font-weight-bold');
        }

        if ($record->firstname !== null) {
            $user = (object) [
                'firstname' => $record->firstname,
                'lastname' => $record->lastname,
            ];
            $userlabel = fullname($user) . '<br><small>' . s($record->email) . '</small>';
        } else {
            $userlabel = get_string('deleteduserfallback', 'tool_enrolsuspension', $record->userid);
        }
        $row[] = $userlabel;

        if ($record->coursename !== null) {
            $courselabel = format_string($record->coursename);
            if (!empty($record->courseshortname)) {
                $courselabel .= '<br><small>' . s($record->courseshortname) . '</small>';
            }
            $row[] = $courselabel;
        } else {
            $row[] = get_string('deletedcoursefallback', 'tool_enrolsuspension', $record->courseid);
        }

        $row[] = s(
            \tool_enrolsuspension\local\history_manager::enrolment_method_label($record->historyenroltype)
        );
        $row[] = format_text($record->reason, FORMAT_PLAIN);
        $row[] = userdate($record->timecreated);

        if ($record->creatorfirstname !== null) {
            $creator = (object) [
                'firstname' => $record->creatorfirstname,
                'lastname' => $record->creatorlastname,
            ];
            $row[] = fullname($creator);
        } else {
            $row[] = get_string('unknownoperator', 'tool_enrolsuspension');
        }

        if ($record->timereactivated) {
            $finallabel = userdate($record->timereactivated);
            if ($record->reactivatorfirstname !== null) {
                $reactivator = (object) [
                    'firstname' => $record->reactivatorfirstname,
                    'lastname' => $record->reactivatorlastname,
                ];
                $finallabel .= '<br><small>' . s(fullname($reactivator)) . '</small>';
            }
            if ((int) $record->status === \tool_enrolsuspension\local\manager::STATUS_STALE) {
                $finallabel .= '<br><small>' . s(get_string('staleshortexplanation', 'tool_enrolsuspension')) . '</small>';
            }
            $row[] = $finallabel;
        } else {
            $row[] = '-';
        }

        $table->data[] = $row;
    }

    echo html_writer::table($table);
    if ($canreactivate) {
        echo html_writer::tag(
            'button',
            get_string('reactivateselected', 'tool_enrolsuspension'),
            ['type' => 'submit', 'class' => 'btn btn-success']
        );
        echo html_writer::end_tag('form');
    }

    echo $OUTPUT->paging_bar(
        $total,
        $page,
        $perpage,
        new moodle_url('/admin/tool/enrolsuspension/history.php', ['status' => $status])
    );
}

echo $OUTPUT->footer();
