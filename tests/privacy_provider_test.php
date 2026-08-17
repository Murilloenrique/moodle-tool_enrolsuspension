<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Privacy provider tests.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension;

use tool_enrolsuspension\privacy\provider;

/** Basic Privacy API discovery test. */
final class privacy_provider_test extends \core_privacy\tests\provider_testcase {
    /** No system context is returned for a user with no stored plugin data. */
    public function test_empty_context_discovery(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(0, $contextlist->get_contextids());
    }
}
