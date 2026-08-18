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
 * History query and presentation helpers.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\local;

/**
 * Centralises history filtering so the page and exports use the same records.
 */
class history_manager {
    /** Special filter value used to show every audit status. */
    public const STATUS_ALL = -1;

    /**
     * Normalise a requested history status.
     *
     * @param int $status Requested status.
     * @return int Normalised status.
     */
    public static function normalise_status(int $status): int {
        $allowed = [
            self::STATUS_ALL,
            manager::STATUS_SUSPENDED,
            manager::STATUS_REACTIVATED,
            manager::STATUS_STALE,
        ];
        return in_array($status, $allowed, true) ? $status : self::STATUS_ALL;
    }

    /**
     * Count history records for one filter.
     *
     * @param int $status History status, or STATUS_ALL.
     * @return int Number of records.
     */
    public static function count_records(int $status): int {
        global $DB;

        $status = self::normalise_status($status);
        if ($status === self::STATUS_ALL) {
            return $DB->count_records('tool_enrolsuspension_log');
        }
        return $DB->count_records('tool_enrolsuspension_log', ['status' => $status]);
    }

    /**
     * Load history records with stable user, course and enrolment information.
     *
     * @param int $status History status, or STATUS_ALL.
     * @param int $offset Result offset.
     * @param int $limit Maximum results, zero for no limit.
     * @return \stdClass[] Audit records.
     */
    public static function get_records(int $status, int $offset = 0, int $limit = 0): array {
        global $DB;

        $status = self::normalise_status($status);
        $where = '';
        $params = [];
        if ($status !== self::STATUS_ALL) {
            $where = 'WHERE s.status = :status';
            $params['status'] = $status;
        }

        $sql = "SELECT s.*, u.firstname, u.lastname, u.email,
                       c.fullname AS coursename, c.shortname AS courseshortname,
                       e.enrol AS currentenroltype,
                       creator.firstname AS creatorfirstname, creator.lastname AS creatorlastname,
                       reactivator.firstname AS reactivatorfirstname, reactivator.lastname AS reactivatorlastname
                  FROM {tool_enrolsuspension_log} s
             LEFT JOIN {user} u ON u.id = s.userid
             LEFT JOIN {course} c ON c.id = s.courseid
             LEFT JOIN {enrol} e ON e.id = s.enrolid
             LEFT JOIN {user} creator ON creator.id = s.createdby
             LEFT JOIN {user} reactivator ON reactivator.id = s.reactivatedby
                       {$where}
              ORDER BY s.timecreated DESC, s.id DESC";

        $records = $DB->get_records_sql($sql, $params, $offset, $limit);
        foreach ($records as $record) {
            $storedtype = trim((string) ($record->enroltype ?? ''));
            $currenttype = trim((string) ($record->currentenroltype ?? ''));
            if ($storedtype !== '' && $storedtype !== 'unknown') {
                $record->historyenroltype = $storedtype;
            } elseif ($currenttype !== '') {
                $record->historyenroltype = $currenttype;
            } else {
                $record->historyenroltype = 'unknown';
            }
        }
        return $records;
    }

    /**
     * Build stable sequential numbers for operations that generated audit records.
     *
     * Workflow operation IDs may contain gaps because drafts and expired operations are
     * also created in the workflow table. The history uses this map so operators see
     * consecutive operation numbers based only on permanent audit records.
     *
     * @return array<int, int> Map of internal operation ID to sequential history number.
     */
    public static function operation_sequence_map(): array {
        global $DB;

        $sql = "SELECT operationid,
                       MIN(timecreated) AS firsttime,
                       MIN(id) AS firstid
                  FROM {tool_enrolsuspension_log}
                 WHERE operationid > 0
              GROUP BY operationid
              ORDER BY firsttime ASC, firstid ASC, operationid ASC";
        $operations = $DB->get_records_sql($sql);

        $map = [];
        $sequence = 1;
        foreach ($operations as $operation) {
            $map[(int) $operation->operationid] = $sequence;
            $sequence++;
        }
        return $map;
    }

    /**
     * Return a human-readable, stable operation code for the history.
     *
     * @param int $operationid Internal workflow operation ID.
     * @param array<int, int> $sequencemap Stable operation sequence map.
     * @return string Human-readable operation code.
     */
    public static function operation_label(int $operationid, array $sequencemap): string {
        if ($operationid <= 0) {
            return get_string('legacyrecord', 'tool_enrolsuspension');
        }

        $sequence = $sequencemap[$operationid] ?? null;
        if ($sequence === null) {
            return 'OP-' . str_pad((string) $operationid, 4, '0', STR_PAD_LEFT);
        }
        return 'OP-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Return a translated status label.
     *
     * @param int $status Audit status.
     * @return string Status label.
     */
    public static function status_label(int $status): string {
        if ($status === manager::STATUS_SUSPENDED) {
            return get_string('historystatussuspended', 'tool_enrolsuspension');
        }
        if ($status === manager::STATUS_REACTIVATED) {
            return get_string('historystatusreactivated', 'tool_enrolsuspension');
        }
        if ($status === manager::STATUS_STALE) {
            return get_string('historystatusstale', 'tool_enrolsuspension');
        }
        return get_string('unknownstatus', 'tool_enrolsuspension');
    }

    /**
     * Return a human-readable enrolment method label while retaining its technical name.
     *
     * @param string|null $enroltype Enrolment plugin type.
     * @return string Enrolment method label.
     */
    public static function enrolment_method_label(?string $enroltype): string {
        $enroltype = trim((string) $enroltype);
        if ($enroltype === '' || $enroltype === 'unknown') {
            return get_string('unknownenrolmentmethod', 'tool_enrolsuspension');
        }

        $component = 'enrol_' . $enroltype;
        $stringmanager = get_string_manager();
        if ($stringmanager->string_exists('pluginname', $component)) {
            return get_string('enrolmentmethodwithtype', 'tool_enrolsuspension', (object) [
                'name' => get_string('pluginname', $component),
                'type' => $enroltype,
            ]);
        }
        return $enroltype;
    }
}
