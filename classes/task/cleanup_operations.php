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
 * Scheduled cleanup of expired suspension workflow operations.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\task;

defined('MOODLE_INTERNAL') || die();

/** Scheduled cleanup task. */
class cleanup_operations extends \core\task\scheduled_task {
    /** @return string */
    public function get_name(): string {
        return get_string('taskcleanupoperations', 'tool_enrolsuspension');
    }

    /** Execute cleanup. */
    public function execute(): void {
        \tool_enrolsuspension\local\operation_manager::cleanup_expired();
    }
}
