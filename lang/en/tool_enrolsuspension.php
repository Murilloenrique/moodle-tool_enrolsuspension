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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * English language strings.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin.
$string['pluginname'] = 'Enrolment suspension control';

// Capabilities.
$string['enrolsuspension:manage'] = 'Manage enrolment suspensions';

// Menu.
$string['navigationcategory'] = 'Suspension control';
$string['dashboard'] = 'Dashboard';
$string['history'] = 'History';

// Buttons.
$string['search'] = 'Search';
$string['next'] = 'Next';
$string['suspend'] = 'Suspend';
$string['reactivate'] = 'Reactivate';

// Form fields.
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Enter name, email, username or ID number';
$string['selectedusers'] = 'Selected users';
$string['reason'] = 'Reason';

// Messages.
$string['searchresults'] = 'Users found';
$string['nousersfound'] = 'No users were found.';
$string['minimumcharacters'] = 'Enter at least 2 characters.';
$string['searching'] = 'Searching...';
$string['removeuser'] = 'Remove user';
$string['nousersselected'] = 'No users have been selected.';
$string['selectuser'] = 'Select';
$string['idnumberlabel'] = 'ID number';
$string['selectatleastoneuser'] = 'Select at least one user.';
$string['selectusersfirst'] = 'Select one or more users first.';
$string['selectcourses'] = 'Select courses';
$string['availablecourses'] = 'Courses found';
$string['nocoursesfound'] = 'No courses were found for the selected users.';
$string['shortnamecourse'] = 'Short name';
$string['enrolledselectedusers'] = 'Enrolled users';
$string['enrolledlabel'] = 'Enrolled';
$string['notenrolledlabel'] = 'Not enrolled';
$string['suspendallcurrentcourses'] = 'Suspend all current courses of the selected users';
$string['selectatleastonecourse'] = 'Select at least one course.';
$string['suspensiondetails'] = 'Suspension details';
$string['permanentsuspensionnotice'] = 'The suspension is permanent and can only be removed manually.';
$string['review'] = 'Review';
$string['summaryitem'] = 'Item';
$string['details'] = 'Details';
$string['usercoursepairstoaffect'] = 'User-course combinations to affect';
$string['enrolmentlinkstoaffect'] = 'Enrolment links to suspend';
$string['enrolmentdetails'] = 'Enrolment link details';
$string['enrolmentmethod'] = 'Enrolment method';
$string['overlappingenrolmentsnotice'] = '{$a->links} active enrolment links were found for {$a->pairs} user-course combination(s). This occurs when the same user is enrolled in the same course through more than one method. Every active link must be suspended to block access.';
$string['enrolmentstoaffect'] = 'Enrolment links to suspend';
$string['suspensiontype'] = 'Suspension type';
$string['permanent'] = 'Permanent';
$string['confirmsuspension'] = 'Confirm suspension';
$string['suspensionsuccess'] = '{$a} enrolment link(s) suspended successfully.';
$string['suspensionsskipped'] = '{$a} enrolment link(s) could not be changed.';
$string['enrolpluginunavailable'] = 'The {$a} enrolment method is unavailable.';
$string['reactivationsuccess'] = '{$a} enrolment link(s) reactivated successfully.';
$string['activesuspensions'] = 'Active suspensions';
$string['reactivatedsuspensions'] = 'Reactivated suspensions';
$string['nohistoryrecords'] = 'No records were found.';
$string['suspendedon'] = 'Suspended on';
$string['performedby'] = 'Performed by';
$string['reactivateselected'] = 'Reactivate selected';
$string['importcsv'] = 'Import CSV';
$string['csvfile'] = 'CSV or TXT file';
$string['csvinstructions'] = 'Use one column containing username, email, or ID number. The header is optional.';
$string['csvusersfound'] = '{$a} user(s) found in the file.';
$string['csvnousers'] = 'No matching users were found in the file.';
$string['coursesuspensions'] = 'Suspensions';
$string['privacy:metadata:table'] = 'Enrolment suspension and reactivation records.';
$string['privacy:metadata:userid'] = 'The user whose enrolment was suspended.';
$string['privacy:metadata:courseid'] = 'The course associated with the suspension.';
$string['privacy:metadata:reason'] = 'The reason entered for the suspension.';
$string['privacy:metadata:createdby'] = 'The user who performed the suspension.';
$string['privacy:metadata:timecreated'] = 'The time the suspension was performed.';
