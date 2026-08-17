<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Immutable workflow operation manager.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Stores each suspension workflow independently so parallel tabs cannot overwrite each other.
 */
class operation_manager {
    public const STATUS_DRAFT = 0;
    public const STATUS_READY = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_CONSUMED = 3;
    public const STATUS_CANCELLED = 4;
    public const STATUS_FAILED = 5;
    public const STATUS_BLOCKED = 6;

    /** Operation lifetime in seconds. */
    private const TTL = 7200;

    /**
     * Create a workflow operation.
     *
     * @param int[] $userids Selected users.
     * @param int $actorid Operator user id.
     * @param int $forcedcourseid Optional course shortcut scope.
     * @return stdClass
     */
    public static function create(array $userids, int $actorid, int $forcedcourseid = 0): \stdClass {
        global $DB;

        $userids = self::normalise_ids($userids);
        if (!$userids) {
            throw new \moodle_exception('selectatleastoneuser', 'tool_enrolsuspension_log');
        }
        if (count($userids) > 500) {
            throw new \moodle_exception('toomanyusers', 'tool_enrolsuspension_log');
        }

        $now = time();
        $record = (object) [
            'token' => bin2hex(random_bytes(32)),
            'courseids' => '',
            'forcedcourseid' => max(0, $forcedcourseid),
            'reason' => '',
            'status' => self::STATUS_DRAFT,
            'claimtoken' => null,
            'createdby' => $actorid,
            'timecreated' => $now,
            'timemodified' => $now,
            'expiresat' => $now + self::TTL,
            'consumedat' => 0,
        ];
        self::cleanup_expired();
        $record->id = $DB->insert_record('tool_enrolsuspension_op', $record);
        foreach ($userids as $userid) {
            $DB->insert_record('tool_enrolsuspension_opusr', (object) [
                'operationid' => $record->id,
                'userid' => $userid,
            ]);
        }
        return $record;
    }

    /**
     * Load an operation owned by the current operator.
     *
     * @param string $token Operation token.
     * @param int $actorid Operator id.
     * @param bool $allowexpired Whether expired operations may be returned.
     * @return stdClass
     */
    public static function get(string $token, int $actorid, bool $allowexpired = false): \stdClass {
        global $DB;

        $operation = $DB->get_record('tool_enrolsuspension_op', [
            'token' => $token,
            'createdby' => $actorid,
        ], '*', MUST_EXIST);

        if (!$allowexpired && (int) $operation->expiresat < time()) {
            throw new \moodle_exception('operationexpired', 'tool_enrolsuspension_log');
        }
        return $operation;
    }

    /**
     * Get selected user IDs.
     *
     * @param \stdClass $operation Operation record.
     * @return int[]
     */
    public static function userids(\stdClass $operation): array {
        global $DB;
        return array_map('intval', array_keys($DB->get_records('tool_enrolsuspension_opusr',
            ['operationid' => $operation->id], 'userid ASC', 'userid')));
    }

    /**
     * Get selected course IDs.
     *
     * @param \stdClass $operation Operation record.
     * @return int[]
     */
    public static function courseids(\stdClass $operation): array {
        return self::sequence_to_ids($operation->courseids ?? '');
    }

    /**
     * Store the selected courses and invalidate any previous frozen review.
     *
     * @param string $token Operation token.
     * @param int $actorid Operator id.
     * @param int[] $courseids Selected courses.
     */
    public static function set_courses(string $token, int $actorid, array $courseids): void {
        global $DB;

        $operation = self::get($token, $actorid);
        if (in_array((int) $operation->status, [self::STATUS_PROCESSING, self::STATUS_CONSUMED], true)) {
            throw new \moodle_exception('operationlocked', 'tool_enrolsuspension_log');
        }
        $courseids = self::normalise_ids($courseids);
        if (!$courseids) {
            throw new \moodle_exception('selectatleastonecourse', 'tool_enrolsuspension_log');
        }
        if (count($courseids) > 500) {
            throw new \moodle_exception('toomanycourses', 'tool_enrolsuspension_log');
        }

        $DB->delete_records('tool_enrolsuspension_opitm', ['operationid' => $operation->id]);
        $DB->update_record('tool_enrolsuspension_op', (object) [
            'id' => $operation->id,
            'courseids' => implode(',', $courseids),
            'reason' => '',
            'status' => self::STATUS_DRAFT,
            'claimtoken' => null,
            'timemodified' => time(),
        ]);
    }

    /**
     * Freeze the exact enrolment ids which the operator will review.
     *
     * @param string $token Operation token.
     * @param int $actorid Operator id.
     * @param string $reason Suspension reason.
     * @return stdClass Updated operation.
     */
    public static function freeze(string $token, int $actorid, string $reason): \stdClass {
        global $DB;

        $operation = self::get($token, $actorid);
        if (in_array((int) $operation->status, [self::STATUS_PROCESSING, self::STATUS_CONSUMED], true)) {
            throw new \moodle_exception('operationlocked', 'tool_enrolsuspension_log');
        }

        $reason = trim($reason);
        if ($reason === '') {
            throw new \moodle_exception('reasonrequired', 'tool_enrolsuspension_log');
        }
        if (\core_text::strlen($reason) > 1000) {
            throw new \moodle_exception('reasontoolong', 'tool_enrolsuspension_log');
        }

        $userids = self::userids($operation);
        $courseids = self::courseids($operation);
        $enrolments = manager::get_effective_active_enrolments($userids, $courseids);
        if (!$enrolments) {
            throw new \moodle_exception('noactiveenrolments', 'tool_enrolsuspension_log');
        }

        $transaction = $DB->start_delegated_transaction();
        try {
            $DB->delete_records('tool_enrolsuspension_opitm', ['operationid' => $operation->id]);
            $blocked = false;
            foreach ($enrolments as $enrolment) {
                [$supported, $supportreason] = manager::assess_manageability($enrolment);
                if (!$supported) {
                    $blocked = true;
                }
                $DB->insert_record('tool_enrolsuspension_opitm', (object) [
                    'operationid' => $operation->id,
                    'userenrolmentid' => $enrolment->userenrolmentid,
                    'userid' => $enrolment->userid,
                    'courseid' => $enrolment->courseid,
                    'enrolid' => $enrolment->enrolid,
                    'enroltype' => $enrolment->enrol,
                    'supported' => $supported ? 1 : 0,
                    'supportreason' => $supportreason,
                ]);
            }
            $DB->update_record('tool_enrolsuspension_op', (object) [
                'id' => $operation->id,
                'reason' => $reason,
                'status' => $blocked ? self::STATUS_BLOCKED : self::STATUS_READY,
                'claimtoken' => null,
                'timemodified' => time(),
                'expiresat' => time() + self::TTL,
            ]);
            $transaction->allow_commit();
        } catch (\Throwable $exception) {
            $transaction->rollback($exception);
        }

        return self::get($token, $actorid);
    }

    /**
     * Get frozen operation items.
     *
     * @param int $operationid Operation ID.
     * @return \stdClass[]
     */
    public static function items(int $operationid): array {
        global $DB;
        return $DB->get_records('tool_enrolsuspension_opitm', ['operationid' => $operationid], 'id ASC');
    }

    /**
     * Invalidate a frozen review so the operator must review current enrolment state again.
     *
     * @param string $token Operation token.
     * @param int $actorid Operator id.
     */
    public static function reset_review(string $token, int $actorid): void {
        global $DB;

        $operation = self::get($token, $actorid);
        if (in_array((int) $operation->status, [self::STATUS_PROCESSING, self::STATUS_CONSUMED], true)) {
            return;
        }
        $DB->delete_records('tool_enrolsuspension_opitm', ['operationid' => $operation->id]);
        $DB->update_record('tool_enrolsuspension_op', (object) [
            'id' => $operation->id,
            'status' => self::STATUS_DRAFT,
            'claimtoken' => null,
            'timemodified' => time(),
            'expiresat' => time() + self::TTL,
        ]);
    }

    /** Delete expired, unconsumed workflow state. */
    public static function cleanup_expired(): void {
        global $DB;

        [$statussql, $statusparams] = $DB->get_in_or_equal(
            [self::STATUS_DRAFT, self::STATUS_READY, self::STATUS_BLOCKED],
            SQL_PARAMS_NAMED,
            'opstatus'
        );
        $params = ['now' => time()] + $statusparams;
        $expired = $DB->get_records_select(
            'tool_enrolsuspension_op',
            "expiresat < :now AND status {$statussql}",
            $params,
            '',
            'id'
        );
        $ids = array_keys($expired);

        [$terminalsql, $terminalparams] = $DB->get_in_or_equal(
            [self::STATUS_CONSUMED, self::STATUS_CANCELLED, self::STATUS_FAILED],
            SQL_PARAMS_NAMED,
            'terminalstatus'
        );
        $terminalparams['terminalbefore'] = time() - DAYSECS;
        $terminal = $DB->get_records_select(
            'tool_enrolsuspension_op',
            "timemodified < :terminalbefore AND status {$terminalsql}",
            $terminalparams,
            '',
            'id'
        );
        $ids = array_values(array_unique(array_merge($ids, array_keys($terminal))));
        if (!$ids) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'expired');
        $DB->delete_records_select('tool_enrolsuspension_opitm', "operationid {$insql}", $params);
        $DB->delete_records_select('tool_enrolsuspension_opusr', "operationid {$insql}", $params);
        $DB->delete_records_select('tool_enrolsuspension_op', "id {$insql}", $params);
    }

    /**
     * Convert a comma-separated sequence to IDs.
     *
     * @param string|null $sequence Comma-separated IDs.
     * @return int[]
     */
    private static function sequence_to_ids(?string $sequence): array {
        if (!$sequence) {
            return [];
        }
        return self::normalise_ids(explode(',', $sequence));
    }

    /**
     * Normalise a list of IDs.
     *
     * @param int[] $ids IDs to normalise.
     * @return int[]
     */
    private static function normalise_ids(array $ids): array {
        return array_values(array_unique(array_filter(array_map('intval', $ids), static fn(int $id): bool => $id > 0)));
    }
}
