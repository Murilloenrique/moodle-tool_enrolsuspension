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
 * Privacy provider for tool_enrolsuspension.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\privacy;


use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy implementation for audit and workflow records.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    /**
     * Describe the personal data stored by this plugin.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('tool_enrolsuspension_log', [
            'operationid' => 'privacy:metadata:operationid',
            'userid' => 'privacy:metadata:userid',
            'courseid' => 'privacy:metadata:courseid',
            'enrolid' => 'privacy:metadata:enrolid',
            'userenrolmentid' => 'privacy:metadata:userenrolmentid',
            'activekey' => 'privacy:metadata:activekey',
            'reason' => 'privacy:metadata:reason',
            'status' => 'privacy:metadata:status',
            'createdby' => 'privacy:metadata:createdby',
            'timecreated' => 'privacy:metadata:timecreated',
            'reactivatedby' => 'privacy:metadata:reactivatedby',
            'timereactivated' => 'privacy:metadata:timereactivated',
        ], 'privacy:metadata:table');
        $collection->add_database_table('tool_enrolsuspension_op', [
            'token' => 'privacy:metadata:operationtoken',
            'courseids' => 'privacy:metadata:courseids',
            'forcedcourseid' => 'privacy:metadata:forcedcourseid',
            'reason' => 'privacy:metadata:reason',
            'status' => 'privacy:metadata:operationstatus',
            'claimtoken' => 'privacy:metadata:claimtoken',
            'createdby' => 'privacy:metadata:createdby',
            'timecreated' => 'privacy:metadata:timecreated',
            'timemodified' => 'privacy:metadata:timemodified',
            'expiresat' => 'privacy:metadata:expiresat',
            'consumedat' => 'privacy:metadata:consumedat',
        ], 'privacy:metadata:operation');
        $collection->add_database_table('tool_enrolsuspension_opusr', [
            'operationid' => 'privacy:metadata:operationid',
            'userid' => 'privacy:metadata:userid',
        ], 'privacy:metadata:operationuser');
        $collection->add_database_table('tool_enrolsuspension_opitm', [
            'operationid' => 'privacy:metadata:operationid',
            'userid' => 'privacy:metadata:userid',
            'userenrolmentid' => 'privacy:metadata:userenrolmentid',
            'courseid' => 'privacy:metadata:courseid',
            'enrolid' => 'privacy:metadata:enrolid',
            'enroltype' => 'privacy:metadata:enroltype',
            'supported' => 'privacy:metadata:supported',
            'supportreason' => 'privacy:metadata:supportreason',
        ], 'privacy:metadata:operationitem');
        return $collection;
    }

    /**
     * Get contexts containing personal data for a user.
     *
     * @param int $userid User ID.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $list = new contextlist();
        $hasdata = $DB->record_exists_select(
            'tool_enrolsuspension_log',
            'userid = :u1 OR createdby = :u2 OR reactivatedby = :u3',
            ['u1' => $userid, 'u2' => $userid, 'u3' => $userid]
        )
            || $DB->record_exists('tool_enrolsuspension_op', ['createdby' => $userid])
            || $DB->record_exists('tool_enrolsuspension_opusr', ['userid' => $userid])
            || $DB->record_exists('tool_enrolsuspension_opitm', ['userid' => $userid]);
        if ($hasdata) {
            $list->add_system_context();
        }
        return $list;
    }

    /**
     * Add users who have personal data in the supplied context.
     *
     * @param userlist $userlist Approved user list.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_system) {
            return;
        }
        $userlist->add_from_sql('userid', 'SELECT userid FROM {tool_enrolsuspension_log}', []);
        $userlist->add_from_sql('createdby', 'SELECT createdby FROM {tool_enrolsuspension_log} WHERE createdby > 0', []);
        $userlist->add_from_sql(
            'reactivatedby',
            'SELECT reactivatedby FROM {tool_enrolsuspension_log} WHERE reactivatedby > 0',
            []
        );
        $userlist->add_from_sql('createdby', 'SELECT createdby FROM {tool_enrolsuspension_op} WHERE createdby > 0', []);
        $userlist->add_from_sql('userid', 'SELECT userid FROM {tool_enrolsuspension_opusr}', []);
        $userlist->add_from_sql('userid', 'SELECT userid FROM {tool_enrolsuspension_opitm}', []);
    }

    /**
     * Export personal data for an approved context list.
     *
     * @param approved_contextlist $contextlist Approved context list.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if (!in_array(context_system::instance()->id, $contextlist->get_contextids(), true)) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        $audit = $DB->get_records_select(
            'tool_enrolsuspension_log',
            'userid = :u1 OR createdby = :u2 OR reactivatedby = :u3',
            ['u1' => $userid, 'u2' => $userid, 'u3' => $userid],
            'timecreated ASC'
        );
        $selected = $DB->get_records('tool_enrolsuspension_opusr', ['userid' => $userid], 'id ASC');
        $items = $DB->get_records('tool_enrolsuspension_opitm', ['userid' => $userid], 'id ASC');
        $operationids = array_keys($DB->get_records('tool_enrolsuspension_op', ['createdby' => $userid], '', 'id'));
        foreach ($selected as $record) {
            $operationids[] = (int) $record->operationid;
        }
        foreach ($items as $record) {
            $operationids[] = (int) $record->operationid;
        }
        foreach ($audit as $record) {
            if ((int) $record->operationid > 0) {
                $operationids[] = (int) $record->operationid;
            }
        }
        $operationids = array_values(array_unique(array_filter(array_map('intval', $operationids))));
        $operations = $operationids
            ? $DB->get_records_list('tool_enrolsuspension_op', 'id', $operationids, 'timecreated ASC')
            : [];
        if ($audit || $operations || $selected || $items) {
            writer::with_context(context_system::instance())->export_data(
                [get_string('pluginname', 'tool_enrolsuspension')],
                (object) [
                    'auditrecords' => array_values($audit),
                    'operationscreated' => array_values($operations),
                    'operationselections' => array_values($selected),
                    'operationitems' => array_values($items),
                ]
            );
        }
    }

    /**
     * Delete personal data for all users in a context.
     *
     * @param \context $context Context to delete from.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_system) {
            return;
        }

        // Reconcile links which this plugin still actively controls before removing its audit data.
        $active = $DB->get_records('tool_enrolsuspension_log', [
            'status' => \tool_enrolsuspension\local\manager::STATUS_SUSPENDED,
        ]);
        foreach ($active as $record) {
            self::reconcile_active_subject_record($record);
        }

        $DB->delete_records('tool_enrolsuspension_opitm');
        $DB->delete_records('tool_enrolsuspension_opusr');
        $DB->delete_records('tool_enrolsuspension_op');
        $DB->delete_records('tool_enrolsuspension_log');
    }

    /**
     * Delete personal data for one approved user.
     *
     * @param approved_contextlist $contextlist Approved context list.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        if (!in_array(context_system::instance()->id, $contextlist->get_contextids(), true)) {
            return;
        }
        self::delete_users([$contextlist->get_user()->id]);
    }

    /**
     * Delete personal data for an approved user list.
     *
     * @param approved_userlist $userlist Approved user list.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        if (!$userlist->get_context() instanceof \context_system) {
            return;
        }
        self::delete_users($userlist->get_userids());
    }

    /**
     * Delete/anonymise all personal references for approved users.
     * Active subject suspensions are first reactivated when the exact original link can still be safely managed.
     *
     * @param int[] $userids Approved user ids.
     */
    private static function delete_users(array $userids): void {
        global $DB;

        $userids = array_values(array_unique(array_filter(array_map('intval', $userids))));
        if (!$userids) {
            return;
        }
        foreach ($userids as $userid) {
            $subjectrecords = $DB->get_records('tool_enrolsuspension_log', ['userid' => $userid]);
            foreach ($subjectrecords as $record) {
                if ((int) $record->status === \tool_enrolsuspension\local\manager::STATUS_SUSPENDED) {
                    self::reconcile_active_subject_record($record);
                }
            }
            $DB->delete_records('tool_enrolsuspension_log', ['userid' => $userid]);

            // The operator's identity is not required for the remaining operational history.
            $DB->set_field('tool_enrolsuspension_log', 'createdby', 0, ['createdby' => $userid]);
            $DB->set_field('tool_enrolsuspension_log', 'reactivatedby', 0, ['reactivatedby' => $userid]);

            $operationids = array_keys($DB->get_records('tool_enrolsuspension_op', ['createdby' => $userid], '', 'id'));
            foreach ($DB->get_records('tool_enrolsuspension_opusr', ['userid' => $userid], '', 'operationid') as $opuser) {
                $operationids[] = (int) $opuser->operationid;
            }
            foreach ($DB->get_records('tool_enrolsuspension_opitm', ['userid' => $userid], '', 'operationid') as $opitem) {
                $operationids[] = (int) $opitem->operationid;
            }
            $operationids = array_values(array_unique(array_filter(array_map('intval', $operationids))));
            if ($operationids) {
                [$insql, $params] = $DB->get_in_or_equal($operationids, SQL_PARAMS_NAMED, 'op');
                $DB->delete_records_select('tool_enrolsuspension_opitm', "operationid {$insql}", $params);
                $DB->delete_records_select('tool_enrolsuspension_opusr', "operationid {$insql}", $params);
                $DB->delete_records_select('tool_enrolsuspension_op', "id {$insql}", $params);
            }
        }
    }
    /**
     * Best-effort reconciliation before deleting an active subject audit record.
     *
     * Only the exact historical link is considered. New enrolment links are never activated.
     * Legacy links managed by a method which now refuses manual changes are left untouched.
     *
     * @param \stdClass $record Audit record.
     */
    private static function reconcile_active_subject_record(\stdClass $record): void {
        global $DB;

        $ue = $DB->get_record('user_enrolments', ['id' => $record->userenrolmentid]);
        $instance = $DB->get_record('enrol', ['id' => $record->enrolid]);
        if (!$ue || !$instance) {
            return;
        }
        if (
            (int) $ue->userid !== (int) $record->userid
            || (int) $ue->enrolid !== (int) $record->enrolid
            || (int) $instance->courseid !== (int) $record->courseid
            || (int) $ue->status !== ENROL_USER_SUSPENDED
        ) {
            return;
        }

        $enrolment = (object) [
            'userenrolmentid' => $ue->id,
            'userid' => $ue->userid,
            'enrolid' => $instance->id,
            'enrol' => $instance->enrol,
            'courseid' => $instance->courseid,
        ];
        [$supported] = \tool_enrolsuspension\local\manager::assess_manageability($enrolment);
        if (!$supported) {
            return;
        }

        try {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->update_user_enrol($instance, $record->userid, ENROL_USER_ACTIVE);
        } catch (\Throwable $exception) {
            debugging($exception->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
