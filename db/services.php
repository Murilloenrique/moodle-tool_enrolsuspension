<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * External service definitions for the Enrol Suspension plugin.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'tool_enrolsuspension_search_users' => [
        'classname' => \tool_enrolsuspension\external\search_users::class,
        'methodname' => 'execute',
        'description' => 'Searches users for the suspension selector.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'tool/enrolsuspension:manage',
    ],
];