<?php
namespace tool_enrolsuspension\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('tool_enrolsuspension', [
            'userid' => 'privacy:metadata:userid',
            'courseid' => 'privacy:metadata:courseid',
            'reason' => 'privacy:metadata:reason',
            'createdby' => 'privacy:metadata:createdby',
            'timecreated' => 'privacy:metadata:timecreated',
        ], 'privacy:metadata:table');
        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $list = new contextlist();
        $list->add_system_context();
        return $list;
    }

    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        $records = $DB->get_records('tool_enrolsuspension', ['userid' => $userid]);
        if ($records) {
            writer::with_context(context_system::instance())->export_data(
                [get_string('pluginname', 'tool_enrolsuspension')], (object)['records' => array_values($records)]
            );
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if ($context instanceof \context_system) {
            $DB->delete_records('tool_enrolsuspension');
        }
    }

    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;
        $DB->delete_records('tool_enrolsuspension', ['userid' => $contextlist->get_user()->id]);
    }
}
