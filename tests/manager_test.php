<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Tests for suspension workflow integrity.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension;

use tool_enrolsuspension\local\manager;
use tool_enrolsuspension\local\operation_manager;

/** Workflow and exact-link tests. */
final class manager_test extends \advanced_testcase {
    /** Create one manual enrolment and return its objects. */
    private function create_manual_enrolment(): array {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'manual'], '*', MUST_EXIST);
        $plugin = enrol_get_plugin('manual');
        $plugin->enrol_user($instance, $user->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $ue = $DB->get_record('user_enrolments', [
            'enrolid' => $instance->id,
            'userid' => $user->id,
        ], '*', MUST_EXIST);

        return [$course, $user, $instance, $plugin, $ue, $USER];
    }

    /** Parallel operations keep independent user selections. */
    public function test_parallel_operations_are_isolated(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $usera = $this->getDataGenerator()->create_user();
        $userb = $this->getDataGenerator()->create_user();

        $opa = operation_manager::create([$usera->id], $USER->id);
        $opb = operation_manager::create([$userb->id], $USER->id);

       $this->assertSame([(int) $usera->id], operation_manager::userids($opa));
       $this->assertSame([(int) $userb->id], operation_manager::userids($opb));
       $this->assertNotSame($opa->token, $opb->token);
    }

    /** A reviewed operation can only be consumed once and creates one active audit row per exact link. */
    public function test_suspend_operation_is_idempotent(): void {
        global $DB;

        [$course, $user, , , $ue, $actor] = $this->create_manual_enrolment();
        $op = operation_manager::create([$user->id], $actor->id);
        operation_manager::set_courses($op->token, $actor->id, [$course->id]);
        operation_manager::freeze($op->token, $actor->id, 'Test');

        $result = manager::suspend_operation($op->token, $actor->id);
        $this->assertSame(1, $result['suspended']);
        $this->assertEquals(ENROL_USER_SUSPENDED,
            $DB->get_field('user_enrolments', 'status', ['id' => $ue->id]));
        $this->assertEquals(1, $DB->count_records('tool_enrolsuspension', [
            'activekey' => 'ue:' . $ue->id,
        ]));

        $this->expectException(\moodle_exception::class);
        manager::suspend_operation($op->token, $actor->id);
    }

    /** Reactivating an old audit record never activates a newly-created enrolment link. */
    public function test_reactivation_does_not_target_recreated_link(): void {
        global $DB;

        [$course, $user, $instance, $plugin, $ue, $actor] = $this->create_manual_enrolment();
        $op = operation_manager::create([$user->id], $actor->id);
        operation_manager::set_courses($op->token, $actor->id, [$course->id]);
        operation_manager::freeze($op->token, $actor->id, 'Test');
        manager::suspend_operation($op->token, $actor->id);
        $audit = $DB->get_record('tool_enrolsuspension', ['userenrolmentid' => $ue->id], '*', MUST_EXIST);

        $plugin->unenrol_user($instance, $user->id);
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $plugin->enrol_user($instance, $user->id, $studentroleid, 0, 0, ENROL_USER_SUSPENDED);
        $newue = $DB->get_record('user_enrolments', [
            'enrolid' => $instance->id,
            'userid' => $user->id,
        ], '*', MUST_EXIST);
        $this->assertNotEquals($ue->id, $newue->id);

        $result = manager::reactivate([$audit->id], $actor->id);
        $this->assertSame(0, $result['reactivated']);
        $this->assertEquals(ENROL_USER_SUSPENDED,
            $DB->get_field('user_enrolments', 'status', ['id' => $newue->id]));
        $this->assertEquals(manager::STATUS_STALE,
            $DB->get_field('tool_enrolsuspension', 'status', ['id' => $audit->id]));
    }
}
