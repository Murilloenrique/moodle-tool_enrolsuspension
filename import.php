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
 * Deterministic CSV/TXT user import.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');

require_login();
$context = context_system::instance();
require_capability('tool/enrolsuspension:import', $context);
require_capability('tool/enrolsuspension:suspend', $context);

$PAGE->set_url(new moodle_url('/admin/tool/enrolsuspension/import.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('importcsv', 'tool_enrolsuspension'));
$PAGE->set_heading(get_string('pluginname', 'tool_enrolsuspension'));

$confirmids = optional_param('confirmeduserids', '', PARAM_SEQUENCE);
if (data_submitted() && $confirmids !== '') {
    require_sesskey();
    $userids = array_values(array_unique(array_filter(array_map('intval', explode(',', $confirmids)))));
    if (!$userids) {
        redirect($PAGE->url, get_string('csvnousers', 'tool_enrolsuspension'), null,
            \core\output\notification::NOTIFY_ERROR);
    }
    if (count($userids) > 500) {
        redirect($PAGE->url, get_string('toomanyusers', 'tool_enrolsuspension'), null,
            \core\output\notification::NOTIFY_ERROR);
    }
    [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'confirmed');
    $params['guestid'] = guest_user()->id;
    $validids = array_keys($DB->get_records_select(
        'user',
        "id {$insql} AND deleted = 0 AND id <> :guestid",
        $params,
        '',
        'id'
    ));
    $operation = \tool_enrolsuspension\local\operation_manager::create($validids, $USER->id);
    redirect(new moodle_url('/admin/tool/enrolsuspension/courses.php', ['op' => $operation->token]));
}

$form = new \tool_enrolsuspension\form\import_form();
$report = null;
if ($data = $form->get_data()) {
    require_sesskey();
    $content = (string) $form->get_file_content('csvfile');
    $iid = csv_import_reader::get_new_iid('tool_enrolsuspension');
    $reader = new csv_import_reader($iid, 'tool_enrolsuspension');
    $count = $reader->load_csv_content($content, 'UTF-8', $data->delimiter);
    if ($count === false) {
        $report = ['error' => $reader->get_error(), 'found' => [], 'missing' => [], 'ambiguous' => []];
    } else {
        $columns = array_map(static fn($column) => core_text::strtolower(trim($column)), $reader->get_columns());
        $allowed = ['username', 'email', 'idnumber'];
        $field = count($columns) === 1 && in_array($columns[0], $allowed, true) ? $columns[0] : null;
        if (!$field) {
            $report = ['error' => get_string('csvinvalidheader', 'tool_enrolsuspension'),
                'found' => [], 'missing' => [], 'ambiguous' => []];
        } else {
            $values = [];
            $reader->init();
            while (($line = $reader->next()) !== false) {
                if (count($values) >= 1000) {
                    break;
                }
                $value = trim((string) ($line[0] ?? ''));
                if ($value !== '') {
                    $values[$value] = $value;
                }
            }
            $reader->close();
            $values = array_values($values);
            $foundmap = [];
            if ($values) {
                foreach (array_chunk($values, 200) as $chunkno => $chunk) {
                    [$insql, $params] = $DB->get_in_or_equal($chunk, SQL_PARAMS_NAMED, 'csv' . $chunkno . '_');
                    $guestid = guest_user()->id;
                    $params['guestid'] = $guestid;
                    $sql = "SELECT id, username, email, idnumber
                              FROM {user}
                             WHERE deleted = 0 AND id <> :guestid AND {$field} {$insql}";
                    foreach ($DB->get_records_sql($sql, $params) as $user) {
                        $key = (string) $user->{$field};
                        $foundmap[$key][] = (int) $user->id;
                    }
                }
            }
            $found = [];
            $missing = [];
            $ambiguous = [];
            foreach ($values as $value) {
                $matches = array_values(array_unique($foundmap[$value] ?? []));
                if (count($matches) === 1) {
                    $found[$matches[0]] = $matches[0];
                } else if (count($matches) > 1) {
                    $ambiguous[] = $value;
                } else {
                    $missing[] = $value;
                }
            }
            $report = ['error' => '', 'found' => array_values($found), 'missing' => $missing, 'ambiguous' => $ambiguous];
        }
    }
    $reader->cleanup();
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importcsv', 'tool_enrolsuspension'));
echo $OUTPUT->notification(get_string('csvinstructions', 'tool_enrolsuspension'),
    \core\output\notification::NOTIFY_INFO);
$form->display();

if ($report !== null) {
    if ($report['error']) {
        echo $OUTPUT->notification(s($report['error']), \core\output\notification::NOTIFY_ERROR);
    } else {
        echo $OUTPUT->heading(get_string('csvpreview', 'tool_enrolsuspension'), 3);
        echo html_writer::tag('p', get_string('csvfoundcount', 'tool_enrolsuspension', count($report['found'])));
        echo html_writer::tag('p', get_string('csvmissingcount', 'tool_enrolsuspension', count($report['missing'])));
        echo html_writer::tag('p', get_string('csvambiguouscount', 'tool_enrolsuspension', count($report['ambiguous'])));
        if ($report['missing']) {
            echo html_writer::div('<strong>' . get_string('csvmissing', 'tool_enrolsuspension') . ':</strong> ' .
                s(implode(', ', array_slice($report['missing'], 0, 50))), 'alert alert-warning');
        }
        if ($report['ambiguous']) {
            echo html_writer::div('<strong>' . get_string('csvambiguous', 'tool_enrolsuspension') . ':</strong> ' .
                s(implode(', ', array_slice($report['ambiguous'], 0, 50))), 'alert alert-danger');
        }
        if (count($report['found']) > 500) {
            echo $OUTPUT->notification(
                get_string('csvtoomanymatched', 'tool_enrolsuspension', count($report['found'])),
                \core\output\notification::NOTIFY_ERROR
            );
        } else if ($report['found']) {
            echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            echo html_writer::empty_tag('input', [
                'type' => 'hidden', 'name' => 'confirmeduserids', 'value' => implode(',', $report['found']),
            ]);
            echo html_writer::tag('button', get_string('continuewithfound', 'tool_enrolsuspension'),
                ['type' => 'submit', 'class' => 'btn btn-primary']);
            echo html_writer::end_tag('form');
        }
    }
}
echo $OUTPUT->footer();
