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
 * Business logic for enrolment suspension and reactivation.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\local;

defined('MOODLE_INTERNAL') || die();

/** Business logic for suspending and reactivating exact enrolment links. */
class manager {
    /** Enrolment methods explicitly supported for administrative state changes. */
    private const SUPPORTED_ENROL_METHODS = ['manual', 'self'];

    public const STATUS_REACTIVATED = 0;
    public const STATUS_SUSPENDED = 1;
    public const STATUS_STALE = 2;

    /**
     * Return effective active enrolments for selected users and optionally courses.
     *
     * "Active" means the user enrolment is active, its enrol instance is enabled,
     * its date range currently applies, and the enrol plugin itself is enabled.
     *
     * @param int[] $userids User ids.
     * @param int[] $courseids Optional course ids.
     * @return stdClass[]
     */
    public static function get_effective_active_enrolments(array $userids, array $courseids = []): array {
        global $DB;

        $userids = self::normalise_ids($userids);
        $courseids = self::normalise_ids($courseids);
        if (!$userids) {
            return [];
        }

        [$usersql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uid');
        $params = $userparams + [
            'ueactive' => ENROL_USER_ACTIVE,
            'eactive' => ENROL_INSTANCE_ENABLED,
            'nowstart' => time(),
            'nowend' => time(),
        ];
        $coursewhere = '';
        if ($courseids) {
            [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
            $coursewhere = " AND e.courseid {$coursesql}";
            $params += $courseparams;
        }

        $sql = "SELECT ue.id AS userenrolmentid, ue.userid, ue.status AS enrolmentstatus,
                       ue.timestart, ue.timeend, e.id AS enrolid, e.enrol,
                       e.name AS enrolinstancename, e.status AS enrolinstancestatus,
                       e.courseid, c.fullname, c.shortname
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {course} c ON c.id = e.courseid
                 WHERE ue.userid {$usersql}
                   {$coursewhere}
                   AND ue.status = :ueactive
                   AND e.status = :eactive
                   AND (ue.timestart = 0 OR ue.timestart <= :nowstart)
                   AND (ue.timeend = 0 OR ue.timeend > :nowend)
              ORDER BY c.fullname, ue.userid, ue.id";

        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $key => $record) {
            if (!enrol_is_enabled($record->enrol)) {
                unset($records[$key]);
            }
        }
        return $records;
    }

    /**
     * Assess whether Moodle says this enrolment instance may be managed manually by this operator.
     *
     * @param stdClass $enrolment Row returned by get_effective_active_enrolments().
     * @return array{0: bool, 1: string}
     */
    public static function assess_manageability(\stdClass $enrolment): array {
        global $DB;

        if (!in_array($enrolment->enrol, self::SUPPORTED_ENROL_METHODS, true)) {
            return [false, 'methodnotallowlisted'];
        }

        $plugin = enrol_get_plugin($enrolment->enrol);
        if (!$plugin || !enrol_is_enabled($enrolment->enrol)) {
            return [false, 'pluginunavailable'];
        }
        $instance = $DB->get_record('enrol', ['id' => $enrolment->enrolid]);
        if (!$instance || (int) $instance->status !== ENROL_INSTANCE_ENABLED) {
            return [false, 'instancedisabled'];
        }
        if (!$plugin->allow_manage($instance)) {
            return [false, 'methodprotected'];
        }

        $managecapability = 'enrol/' . $instance->enrol . ':manage';
        if (get_capability_info($managecapability)) {
            $coursecontext = \context_course::instance($instance->courseid);
            if (!has_capability($managecapability, $coursecontext)) {
                return [false, 'missingmethodcapability'];
            }
        }
        return [true, ''];
    }

    /**
     * Atomically suspend the exact links frozen in a reviewed operation.
     *
     * @param string $token Operation token.
     * @param int $actorid Operator id.
     * @return array Result counts.
     */
    public static function suspend_operation(string $token, int $actorid): array {
        global $DB;

        $result = ['suspended' => 0, 'skipped' => 0, 'errors' => []];
        $operation = operation_manager::get($token, $actorid);
        if ((int) $operation->status !== operation_manager::STATUS_READY) {
            throw new \moodle_exception('operationnotready', 'tool_enrolsuspension_log');
        }

        $claim = bin2hex(random_bytes(32));
        $transaction = $DB->start_delegated_transaction();
        try {
            // Atomic claim: only one submit can change READY to PROCESSING.
            $DB->execute(
                "UPDATE {tool_enrolsuspension_op}
                    SET status = :processing, claimtoken = :claim, timemodified = :now
                  WHERE id = :id AND status = :ready AND expiresat >= :nowexpiry",
                [
                    'processing' => operation_manager::STATUS_PROCESSING,
                    'claim' => $claim,
                    'now' => time(),
                    'id' => $operation->id,
                    'ready' => operation_manager::STATUS_READY,
                    'nowexpiry' => time(),
                ]
            );
            $claimed = $DB->get_record('tool_enrolsuspension_op', ['id' => $operation->id], '*', MUST_EXIST);
            if ((string) $claimed->claimtoken !== $claim || (int) $claimed->status !== operation_manager::STATUS_PROCESSING) {
                throw new \moodle_exception('operationalreadyused', 'tool_enrolsuspension_log');
            }

            $items = operation_manager::items($operation->id);
            if (!$items) {
                throw new \moodle_exception('operationstatechanged', 'tool_enrolsuspension_log');
            }

            foreach ($items as $item) {
                if (!(int) $item->supported) {
                    throw new \moodle_exception('operationcontainsunsupported', 'tool_enrolsuspension_log');
                }

                $enrolment = self::load_exact_effective_enrolment($item->userenrolmentid);
                if (!$enrolment || (int) $enrolment->userid !== (int) $item->userid
                        || (int) $enrolment->enrolid !== (int) $item->enrolid
                        || (int) $enrolment->courseid !== (int) $item->courseid) {
                    throw new \moodle_exception('operationstatechanged', 'tool_enrolsuspension_log');
                }

                [$supported] = self::assess_manageability($enrolment);
                if (!$supported) {
                    throw new \moodle_exception('operationstatechanged', 'tool_enrolsuspension_log');
                }

                $activekey = 'ue:' . $item->userenrolmentid;
                if ($DB->record_exists('tool_enrolsuspension_log', ['activekey' => $activekey])) {
                    throw new \moodle_exception('alreadymanagedsuspension', 'tool_enrolsuspension_log');
                }

                $instance = $DB->get_record('enrol', ['id' => $item->enrolid], '*', MUST_EXIST);
                $plugin = enrol_get_plugin($instance->enrol);
                $plugin->update_user_enrol($instance, $item->userid, ENROL_USER_SUSPENDED);

                $after = $DB->get_record('user_enrolments', ['id' => $item->userenrolmentid], 'id,status', MUST_EXIST);
                if ((int) $after->status !== ENROL_USER_SUSPENDED) {
                    throw new \moodle_exception('enrolmentstateunchanged', 'tool_enrolsuspension_log');
                }

                $DB->insert_record('tool_enrolsuspension_log', (object) [
                    'operationid' => $operation->id,
                    'userid' => $item->userid,
                    'courseid' => $item->courseid,
                    'enrolid' => $item->enrolid,
                    'userenrolmentid' => $item->userenrolmentid,
                    'activekey' => $activekey,
                    'reason' => $operation->reason,
                    'status' => self::STATUS_SUSPENDED,
                    'createdby' => $actorid,
                    'timecreated' => time(),
                    'reactivatedby' => 0,
                    'timereactivated' => 0,
                ]);
                $result['suspended']++;
            }

            $DB->update_record('tool_enrolsuspension_op', (object) [
                'id' => $operation->id,
                'status' => operation_manager::STATUS_CONSUMED,
                'claimtoken' => null,
                'timemodified' => time(),
                'consumedat' => time(),
            ]);
            $transaction->allow_commit();
        } catch (\Throwable $exception) {
            $transaction->rollback($exception);
        }
        return $result;
    }

    /**
     * Reactivate exact historical links. Each link is handled in its own transaction.
     *
     * @param int[] $recordids Audit record ids.
     * @param int $actorid Operator id.
     * @return array Result counts and safe messages.
     */
    public static function reactivate(array $recordids, int $actorid): array {
        global $DB;

        $recordids = self::normalise_ids($recordids);
        $result = ['reactivated' => 0, 'skipped' => 0, 'errors' => []];
        foreach ($recordids as $recordid) {
            $transaction = $DB->start_delegated_transaction();
            try {
                $record = $DB->get_record('tool_enrolsuspension_log', [
                    'id' => $recordid,
                    'status' => self::STATUS_SUSPENDED,
                ]);
                if (!$record) {
                    $result['skipped']++;
                    $transaction->allow_commit();
                    continue;
                }

                $ue = $DB->get_record('user_enrolments', ['id' => $record->userenrolmentid]);
                if (!$ue || (int) $ue->userid !== (int) $record->userid
                        || (int) $ue->enrolid !== (int) $record->enrolid) {
                    self::mark_stale($record, $actorid);
                    $result['skipped']++;
                    $result['errors'][] = get_string('reactivationstale', 'tool_enrolsuspension', $recordid);
                    $transaction->allow_commit();
                    continue;
                }

                if ((int) $ue->status !== ENROL_USER_SUSPENDED) {
                    self::mark_stale($record, $actorid);
                    $result['skipped']++;
                    $result['errors'][] = get_string('reactivationalreadychanged', 'tool_enrolsuspension_log', $recordid);
                    $transaction->allow_commit();
                    continue;
                }

                $instance = $DB->get_record('enrol', ['id' => $record->enrolid]);
                if (!$instance || (int) $instance->courseid !== (int) $record->courseid) {
                    self::mark_stale($record, $actorid);
                    $result['skipped']++;
                    $result['errors'][] = get_string('reactivationstale', 'tool_enrolsuspension', $recordid);
                    $transaction->allow_commit();
                    continue;
                }

                $enrolment = (object) [
                    'userenrolmentid' => $ue->id,
                    'userid' => $ue->userid,
                    'enrolid' => $instance->id,
                    'enrol' => $instance->enrol,
                    'courseid' => $instance->courseid,
                ];
                [$supported, $reason] = self::assess_manageability($enrolment);
                if (!$supported) {
                    $reasonkey = 'supportreason_' . $reason;
                    $reasonlabel = get_string_manager()->string_exists($reasonkey, 'tool_enrolsuspension_log')
                        ? get_string($reasonkey, 'tool_enrolsuspension_log')
                        : $reason;
                    $result['skipped']++;
                    $result['errors'][] = get_string(
                        'unsupportedmethodreason',
                        'tool_enrolsuspension_log',
                        $reasonlabel
                    );
                    $transaction->allow_commit();
                    continue;
                }

                $plugin = enrol_get_plugin($instance->enrol);
                $plugin->update_user_enrol($instance, $record->userid, ENROL_USER_ACTIVE);
                $after = $DB->get_record('user_enrolments', ['id' => $record->userenrolmentid], 'id,status', MUST_EXIST);
                if ((int) $after->status !== ENROL_USER_ACTIVE) {
                    throw new \moodle_exception('enrolmentstateunchanged', 'tool_enrolsuspension_log');
                }

                $record->status = self::STATUS_REACTIVATED;
                $record->activekey = 'history:' . $record->id;
                $record->reactivatedby = $actorid;
                $record->timereactivated = time();
                $DB->update_record('tool_enrolsuspension_log', $record);
                $transaction->allow_commit();
                $result['reactivated']++;
            } catch (\Throwable $exception) {
                try {
                    $transaction->rollback($exception);
                } catch (\Throwable $rolledback) {
                    debugging($rolledback->getMessage(), DEBUG_DEVELOPER);
                }
                $result['skipped']++;
                $result['errors'][] = get_string('reactivationgenericerror', 'tool_enrolsuspension_log', $recordid);
            }
        }
        return $result;
    }

    /**
     * Return a course map based on the same active-enrolment semantics used at execution.
     *
     * @param int[] $userids User IDs.
     * @param int $forcedcourseid Optional forced course ID.
     * @return array
     */
    public static function get_course_map(array $userids, int $forcedcourseid = 0): array {
        $courseids = $forcedcourseid ? [$forcedcourseid] : [];
        $enrolments = self::get_effective_active_enrolments($userids, $courseids);
        $map = [];
        foreach ($enrolments as $enrolment) {
            if (!isset($map[$enrolment->courseid])) {
                $map[$enrolment->courseid] = [
                    'course' => (object) [
                        'id' => $enrolment->courseid,
                        'fullname' => $enrolment->fullname,
                        'shortname' => $enrolment->shortname,
                    ],
                    'userids' => [],
                ];
            }
            $map[$enrolment->courseid]['userids'][$enrolment->userid] = $enrolment->userid;
        }
        uasort($map, static fn(array $a, array $b): int => strcasecmp($a['course']->fullname, $b['course']->fullname));
        return $map;
    }

    /**
     * Load one exact enrolment if it is still effectively active.
     *
     * @param int $userenrolmentid User enrolment ID.
     * @return \stdClass|null
     */
    private static function load_exact_effective_enrolment(int $userenrolmentid): ?\stdClass {
        global $DB;

        $now = time();
        $sql = "SELECT ue.id AS userenrolmentid, ue.userid, ue.status AS enrolmentstatus,
                       ue.timestart, ue.timeend, e.id AS enrolid, e.enrol,
                       e.name AS enrolinstancename, e.status AS enrolinstancestatus,
                       e.courseid, c.fullname, c.shortname
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {course} c ON c.id = e.courseid
                 WHERE ue.id = :ueid
                   AND ue.status = :ueactive
                   AND e.status = :eactive
                   AND (ue.timestart = 0 OR ue.timestart <= :nowstart)
                   AND (ue.timeend = 0 OR ue.timeend > :nowend)";
        $record = $DB->get_record_sql($sql, [
            'ueid' => $userenrolmentid,
            'ueactive' => ENROL_USER_ACTIVE,
            'eactive' => ENROL_INSTANCE_ENABLED,
            'nowstart' => $now,
            'nowend' => $now,
        ]);
        if (!$record || !enrol_is_enabled($record->enrol)) {
            return null;
        }
        return $record;
    }

    /**
     * Mark a historical suspension as stale.
     *
     * @param \stdClass $record Suspension record.
     * @param int $actorid Operator ID.
     */
    private static function mark_stale(\stdClass $record, int $actorid): void {
        global $DB;
        $record->status = self::STATUS_STALE;
        $record->activekey = 'history:' . $record->id;
        $record->reactivatedby = $actorid;
        $record->timereactivated = time();
        $DB->update_record('tool_enrolsuspension_log', $record);
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
