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
 * Administration pages for tool_enrolsuspension.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$ADMIN->add('root', new admin_category(
    'tool_enrolsuspension_category',
    get_string('navigationcategory', 'tool_enrolsuspension')
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_dashboard',
    get_string('dashboard', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/index.php'),
    'tool/enrolsuspension:suspend'
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_history',
    get_string('history', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/history.php'),
    'tool/enrolsuspension:view'
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_import',
    get_string('importcsv', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/import.php'),
    'tool/enrolsuspension:import'
));
