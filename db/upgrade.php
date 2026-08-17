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
 * Upgrade steps for tool_enrolsuspension.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin database structure.
 *
 * @param int $oldversion Previously installed version.
 * @return bool
 */
function xmldb_tool_enrolsuspension_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026081700) {
        $audittable = new xmldb_table('tool_enrolsuspension');

        $operationid = new xmldb_field('operationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');
        if (!$dbman->field_exists($audittable, $operationid)) {
            $dbman->add_field($audittable, $operationid);
        }

        $activekey = new xmldb_field('activekey', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'userenrolmentid');
        if (!$dbman->field_exists($audittable, $activekey)) {
            $dbman->add_field($audittable, $activekey);
        }

        // Repair duplicate legacy active records before creating the unique active-key index.
        $active = $DB->get_records('tool_enrolsuspension', ['status' => 1], 'userenrolmentid ASC, timecreated DESC, id DESC');
        $seen = [];
        foreach ($active as $record) {
            $ueid = (int) $record->userenrolmentid;
            if ($ueid <= 0) {
                $record->status = 2;
                $record->activekey = 'history:' . $record->id;
                $DB->update_record('tool_enrolsuspension', $record);
                continue;
            }
            if (isset($seen[$ueid])) {
                $record->status = 2;
                $record->activekey = 'history:' . $record->id;
            } else {
                $seen[$ueid] = true;
                $record->activekey = 'ue:' . $ueid;
            }
            $DB->update_record('tool_enrolsuspension', $record);
        }
        $remaining = $DB->get_records_select('tool_enrolsuspension', 'activekey IS NULL', [], '', 'id');
        foreach ($remaining as $record) {
            $DB->set_field('tool_enrolsuspension', 'activekey', 'history:' . $record->id, ['id' => $record->id]);
        }
        $activekeyrequired = new xmldb_field(
            'activekey', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'userenrolmentid'
        );
        $dbman->change_field_notnull($audittable, $activekeyrequired);

        $activeindex = new xmldb_index('activekeyuniq', XMLDB_INDEX_UNIQUE, ['activekey']);
        if (!$dbman->index_exists($audittable, $activeindex)) {
            $dbman->add_index($audittable, $activeindex);
        }
        $operationindex = new xmldb_index('operationidx', XMLDB_INDEX_NOTUNIQUE, ['operationid']);
        if (!$dbman->index_exists($audittable, $operationindex)) {
            $dbman->add_index($audittable, $operationindex);
        }

        $operationtable = new xmldb_table('tool_enrolsusp_operation');
        if (!$dbman->table_exists($operationtable)) {
            $operationtable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $operationtable->add_field('token', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL);
            $operationtable->add_field('courseids', XMLDB_TYPE_TEXT, null, null, null);
            $operationtable->add_field('forcedcourseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('reason', XMLDB_TYPE_TEXT, null, null, null);
            $operationtable->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('claimtoken', XMLDB_TYPE_CHAR, '64', null, null);
            $operationtable->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('expiresat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_field('consumedat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $operationtable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $operationtable->add_index('tokenuniq', XMLDB_INDEX_UNIQUE, ['token']);
            $operationtable->add_index('creatorstatusidx', XMLDB_INDEX_NOTUNIQUE, ['createdby', 'status']);
            $operationtable->add_index('expiryidx', XMLDB_INDEX_NOTUNIQUE, ['expiresat']);
            $dbman->create_table($operationtable);
        }

        $usertable = new xmldb_table('tool_enrolsusp_opuser');
        if (!$dbman->table_exists($usertable)) {
            $usertable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $usertable->add_field('operationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $usertable->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $usertable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $usertable->add_key('operationfk', XMLDB_KEY_FOREIGN, ['operationid'], 'tool_enrolsusp_operation', ['id']);
            $usertable->add_index('operationuseruniq', XMLDB_INDEX_UNIQUE, ['operationid', 'userid']);
            $usertable->add_index('userididx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
            $dbman->create_table($usertable);
        }

        $itemtable = new xmldb_table('tool_enrolsusp_opitem');
        if (!$dbman->table_exists($itemtable)) {
            $itemtable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $itemtable->add_field('operationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('userenrolmentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('enrolid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('enroltype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL);
            $itemtable->add_field('supported', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
            $itemtable->add_field('supportreason', XMLDB_TYPE_CHAR, '100', null, null);
            $itemtable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $itemtable->add_key('operationfk', XMLDB_KEY_FOREIGN, ['operationid'], 'tool_enrolsusp_operation', ['id']);
            $itemtable->add_index('operationueuniq', XMLDB_INDEX_UNIQUE, ['operationid', 'userenrolmentid']);
            $dbman->create_table($itemtable);
        }

        // Preserve existing delegated access without granting new roles broader permissions.
        $legacyassignments = $DB->get_records('role_capabilities', [
            'capability' => 'tool/enrolsuspension:manage',
        ]);
        foreach ($legacyassignments as $assignment) {
            if ((int) $assignment->permission !== CAP_ALLOW) {
                continue;
            }
            foreach (['view', 'suspend', 'reactivate', 'import'] as $suffix) {
                assign_capability(
                    'tool/enrolsuspension:' . $suffix,
                    CAP_ALLOW,
                    $assignment->roleid,
                    $assignment->contextid,
                    true
                );
            }
        }

        upgrade_plugin_savepoint(true, 2026081700, 'tool', 'enrolsuspension');
    }

    if ($oldversion < 2026081702) {
        // Rename plugin tables so every table uses the full Frankenstyle component prefix.
        // Drop child foreign keys before renaming the referenced operation table, then recreate them.
        $legacyopuser = new xmldb_table('tool_enrolsusp_opuser');
        $legacyopitem = new xmldb_table('tool_enrolsusp_opitem');
        $legacyoperation = new xmldb_table('tool_enrolsusp_operation');
        $legacyaudit = new xmldb_table('tool_enrolsuspension');

        $legacyuserfk = new xmldb_key(
            'operationfk',
            XMLDB_KEY_FOREIGN,
            ['operationid'],
            'tool_enrolsusp_operation',
            ['id']
        );
        if ($dbman->table_exists($legacyopuser) && $dbman->key_exists($legacyopuser, $legacyuserfk)) {
            $dbman->drop_key($legacyopuser, $legacyuserfk);
        }

        $legacyitemfk = new xmldb_key(
            'operationfk',
            XMLDB_KEY_FOREIGN,
            ['operationid'],
            'tool_enrolsusp_operation',
            ['id']
        );
        if ($dbman->table_exists($legacyopitem) && $dbman->key_exists($legacyopitem, $legacyitemfk)) {
            $dbman->drop_key($legacyopitem, $legacyitemfk);
        }

        $newaudit = new xmldb_table('tool_enrolsuspension_log');
        if ($dbman->table_exists($legacyaudit) && !$dbman->table_exists($newaudit)) {
            $dbman->rename_table($legacyaudit, 'tool_enrolsuspension_log');
        }

        $newoperation = new xmldb_table('tool_enrolsuspension_op');
        if ($dbman->table_exists($legacyoperation) && !$dbman->table_exists($newoperation)) {
            $dbman->rename_table($legacyoperation, 'tool_enrolsuspension_op');
        }

        $newopuser = new xmldb_table('tool_enrolsuspension_opusr');
        if ($dbman->table_exists($legacyopuser) && !$dbman->table_exists($newopuser)) {
            $dbman->rename_table($legacyopuser, 'tool_enrolsuspension_opusr');
        }

        $newopitem = new xmldb_table('tool_enrolsuspension_opitm');
        if ($dbman->table_exists($legacyopitem) && !$dbman->table_exists($newopitem)) {
            $dbman->rename_table($legacyopitem, 'tool_enrolsuspension_opitm');
        }

        $newuserfk = new xmldb_key(
            'operationfk',
            XMLDB_KEY_FOREIGN,
            ['operationid'],
            'tool_enrolsuspension_op',
            ['id']
        );
        if ($dbman->table_exists($newopuser) && !$dbman->key_exists($newopuser, $newuserfk)) {
            $dbman->add_key($newopuser, $newuserfk);
        }

        $newitemfk = new xmldb_key(
            'operationfk',
            XMLDB_KEY_FOREIGN,
            ['operationid'],
            'tool_enrolsuspension_op',
            ['id']
        );
        if ($dbman->table_exists($newopitem) && !$dbman->key_exists($newopitem, $newitemfk)) {
            $dbman->add_key($newopitem, $newitemfk);
        }

        upgrade_plugin_savepoint(true, 2026081702, 'tool', 'enrolsuspension');
    }

    return true;
}
