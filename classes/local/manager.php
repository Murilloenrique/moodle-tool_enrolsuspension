<?php
namespace tool_enrolsuspension\local;

defined('MOODLE_INTERNAL') || die();

/** Business logic for suspending and reactivating enrolments. */
class manager {
    public const STATUS_REACTIVATED = 0;
    public const STATUS_SUSPENDED = 1;

    /**
     * Return active enrolments matching users and courses.
     *
     * @param int[] $userids
     * @param int[] $courseids
     * @return array
     */
    public static function get_active_enrolments(array $userids, array $courseids): array {
        global $DB;

        $userids = array_values(array_unique(array_filter(array_map('intval', $userids))));
        $courseids = array_values(array_unique(array_filter(array_map('intval', $courseids))));
        if (!$userids || !$courseids) {
            return [];
        }

        [$usersql, $userparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'uid');
        [$coursesql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'cid');
        $params = array_merge($userparams, $courseparams, ['active' => ENROL_USER_ACTIVE]);

        $sql = "SELECT ue.id AS userenrolmentid, ue.userid, ue.status AS enrolmentstatus,
                       e.id AS enrolid, e.enrol, e.name AS enrolinstancename, e.courseid, c.fullname, c.shortname
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {course} c ON c.id = e.courseid
                 WHERE ue.userid {$usersql}
                   AND e.courseid {$coursesql}
                   AND ue.status = :active
              ORDER BY c.fullname, ue.userid";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Suspend matching enrolments and record audit rows.
     *
     * @param int[] $userids
     * @param int[] $courseids
     * @param string $reason
     * @param int $actorid
     * @return array Counts and messages.
     */
    public static function suspend(array $userids, array $courseids, string $reason, int $actorid): array {
        global $DB;

        $result = ['suspended' => 0, 'skipped' => 0, 'errors' => []];
        $enrolments = self::get_active_enrolments($userids, $courseids);
        $transaction = $DB->start_delegated_transaction();

        foreach ($enrolments as $enrolment) {
            $plugin = enrol_get_plugin($enrolment->enrol);
            $instance = $DB->get_record('enrol', ['id' => $enrolment->enrolid], '*', MUST_EXIST);
            if (!$plugin) {
                $result['skipped']++;
                $result['errors'][] = get_string('enrolpluginunavailable', 'tool_enrolsuspension', $enrolment->enrol);
                continue;
            }

            try {
                $plugin->update_user_enrol($instance, $enrolment->userid, ENROL_USER_SUSPENDED);

                $record = (object) [
                    'userid' => $enrolment->userid,
                    'courseid' => $enrolment->courseid,
                    'enrolid' => $enrolment->enrolid,
                    'userenrolmentid' => $enrolment->userenrolmentid,
                    'reason' => $reason,
                    'status' => self::STATUS_SUSPENDED,
                    'createdby' => $actorid,
                    'timecreated' => time(),
                    'reactivatedby' => 0,
                    'timereactivated' => 0,
                ];
                $DB->insert_record('tool_enrolsuspension', $record);
                $result['suspended']++;
            } catch (\Throwable $exception) {
                $result['skipped']++;
                $result['errors'][] = $exception->getMessage();
            }
        }

        $transaction->allow_commit();
        return $result;
    }

    /** Reactivate audit rows and their enrolments. */
    public static function reactivate(array $recordids, int $actorid): array {
        global $DB;

        $recordids = array_values(array_unique(array_filter(array_map('intval', $recordids))));
        $result = ['reactivated' => 0, 'skipped' => 0, 'errors' => []];
        if (!$recordids) {
            return $result;
        }

        [$insql, $params] = $DB->get_in_or_equal($recordids, SQL_PARAMS_NAMED, 'rid');
        $params['status'] = self::STATUS_SUSPENDED;
        $records = $DB->get_records_select('tool_enrolsuspension', "id {$insql} AND status = :status", $params);
        $transaction = $DB->start_delegated_transaction();

        foreach ($records as $record) {
            $instance = $DB->get_record('enrol', ['id' => $record->enrolid]);
            if (!$instance) {
                $result['skipped']++;
                continue;
            }
            $plugin = enrol_get_plugin($instance->enrol);
            if (!$plugin) {
                $result['skipped']++;
                continue;
            }

            try {
                $plugin->update_user_enrol($instance, $record->userid, ENROL_USER_ACTIVE);
                $record->status = self::STATUS_REACTIVATED;
                $record->reactivatedby = $actorid;
                $record->timereactivated = time();
                $DB->update_record('tool_enrolsuspension', $record);
                $result['reactivated']++;
            } catch (\Throwable $exception) {
                $result['skipped']++;
                $result['errors'][] = $exception->getMessage();
            }
        }

        $transaction->allow_commit();
        return $result;
    }
}
