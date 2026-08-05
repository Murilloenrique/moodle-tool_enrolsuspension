<?php
// This file is part of Moodle - http://moodle.org/
/** Administration pages. @package tool_enrolsuspension */
defined('MOODLE_INTERNAL') || die();

$ADMIN->add('root', new admin_category(
    'tool_enrolsuspension_category',
    get_string('navigationcategory', 'tool_enrolsuspension')
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_dashboard',
    get_string('dashboard', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/index.php'),
    'tool/enrolsuspension:manage'
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_history',
    get_string('history', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/history.php'),
    'tool/enrolsuspension:manage'
));
$ADMIN->add('tool_enrolsuspension_category', new admin_externalpage(
    'tool_enrolsuspension_import',
    get_string('importcsv', 'tool_enrolsuspension'),
    new moodle_url('/admin/tool/enrolsuspension/import.php'),
    'tool/enrolsuspension:manage'
));
