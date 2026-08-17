<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/** English language strings. @package tool_enrolsuspension */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Enrolment suspension control';
$string['navigationcategory'] = 'Suspension control';
$string['dashboard'] = 'Dashboard';
$string['history'] = 'History';
$string['enrolsuspension:manage'] = 'Legacy: manage enrolment suspensions';
$string['enrolsuspension:view'] = 'View suspension history';
$string['enrolsuspension:suspend'] = 'Search users and suspend enrolments';
$string['enrolsuspension:reactivate'] = 'Reactivate suspended enrolments';
$string['enrolsuspension:import'] = 'Import users for suspension';
$string['search'] = 'Search';
$string['next'] = 'Next';
$string['suspend'] = 'Suspend';
$string['reactivate'] = 'Reactivate';
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Enter name, email, username or ID number';
$string['selectedusers'] = 'Selected users';
$string['reason'] = 'Reason';
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
$string['nocoursesfound'] = 'No currently active courses were found for the selected users.';
$string['shortnamecourse'] = 'Short name';
$string['enrolledselectedusers'] = 'Enrolled users';
$string['enrolledlabel'] = 'Enrolled';
$string['notenrolledlabel'] = 'Not enrolled';
$string['suspendallcurrentcourses'] = 'Suspend all current active courses of the selected users';
$string['selectatleastonecourse'] = 'Select at least one course.';
$string['suspensiondetails'] = 'Suspension details';
$string['permanentsuspensionnotice'] = 'The suspension has no automatic expiry. Reactivation is manual.';
$string['reasonrequired'] = 'Enter a suspension reason.';
$string['review'] = 'Review';
$string['summaryitem'] = 'Item';
$string['details'] = 'Details';
$string['usercoursepairstoaffect'] = 'User-course combinations';
$string['enrolmentlinkstoaffect'] = 'Exact enrolment links';
$string['enrolmentdetails'] = 'Enrolment link details';
$string['enrolmentmethod'] = 'Enrolment method';
$string['supportstatus'] = 'Management status';
$string['supported'] = 'Supported';
$string['unsupportedoperationblocked'] = 'This operation cannot be confirmed because one or more active enrolment links ' .
    'are managed by a method which does not permit manual changes. This prevents a synchronisation source from silently ' .
    'undoing the suspension.';
$string['unsupportedmethodreason'] = 'Not supported ({$a})';
$string['suspensiontype'] = 'Suspension type';
$string['permanent'] = 'Permanent until manual reactivation';
$string['confirmsuspension'] = 'Confirm suspension';
$string['suspensionsuccess'] = '{$a} exact enrolment link(s) suspended successfully.';
$string['reactivationsuccess'] = '{$a} exact enrolment link(s) reactivated successfully.';
$string['reactivationsskipped'] = '{$a} link(s) were not reactivated. Check the history and enrolment method.';
$string['reactivationstale'] = 'Record {$a}: the original enrolment link no longer exists. The history item was marked stale.';
$string['reactivationalreadychanged'] = 'Record {$a}: the original enrolment link had already changed state. ' .
    'The history item was marked stale.';
$string['reactivationgenericerror'] = 'Record {$a}: reactivation failed. Technical details were sent to developer debugging.';
$string['activesuspensions'] = 'Active suspensions';
$string['reactivatedsuspensions'] = 'Reactivated suspensions';
$string['stalesuspensions'] = 'Stale records';
$string['nohistoryrecords'] = 'No records were found.';
$string['suspendedon'] = 'Suspended on';
$string['performedby'] = 'Performed by';
$string['reactivationinfo'] = 'Reactivation/status change';
$string['reactivateselected'] = 'Reactivate selected';
$string['deleteduserfallback'] = 'Deleted/missing user #{$a}';
$string['deletedcoursefallback'] = 'Deleted/missing course #{$a}';
$string['unknownoperator'] = 'Unknown/deleted operator';
$string['importcsv'] = 'Import CSV/TXT';
$string['csvfile'] = 'CSV or TXT file';
$string['csvinstructions'] = 'The file must contain exactly one column with a required header: username, email, or idnumber. ' .
    'A maximum of 1,000 values is processed per upload.';
$string['csvdelimiter'] = 'Delimiter';
$string['csvdelimitercomma'] = 'Comma';
$string['csvdelimitersemicolon'] = 'Semicolon';
$string['csvdelimitertab'] = 'Tab';
$string['csvinvalidheader'] = 'Invalid CSV header. Use exactly one column named username, email, or idnumber.';
$string['csvpreview'] = 'Import preview';
$string['csvfoundcount'] = 'Unique users found: {$a}';
$string['csvmissingcount'] = 'Values not found: {$a}';
$string['csvambiguouscount'] = 'Ambiguous values: {$a}';
$string['csvmissing'] = 'Not found';
$string['csvambiguous'] = 'Ambiguous';
$string['csvnousers'] = 'No uniquely matching users were found.';
$string['continuewithfound'] = 'Continue with uniquely matched users';
$string['coursesuspensions'] = 'Suspensions';
$string['operationexpired'] = 'This suspension operation expired. Start a new operation.';
$string['operationlocked'] = 'This operation can no longer be edited.';
$string['operationnotready'] = 'The operation is not ready for confirmation.';
$string['operationalreadyused'] = 'This operation has already been submitted or is being processed.';
$string['operationstatechanged'] = 'The enrolment state changed after review. No changes were applied. Review the operation again.';
$string['operationcontainsunsupported'] = 'The reviewed operation contains an unsupported enrolment method.';
$string['alreadymanagedsuspension'] = 'An active plugin suspension already exists for one of the exact enrolment links.';
$string['enrolmentstateunchanged'] = 'Moodle did not change the expected enrolment state. The operation was rolled back.';
$string['noactiveenrolments'] = 'No currently active enrolment links remain for this selection.';
$string['postrequired'] = 'This action only accepts a POST request.';
$string['operationgenericerror'] = 'The operation could not be completed. No partial suspension was committed.';
$string['privacy:metadata:table'] = 'Audit records for enrolment suspension and reactivation.';
$string['privacy:metadata:operation'] = 'Temporary workflow operations created by suspension operators.';
$string['privacy:metadata:operationitem'] = 'Exact enrolment links frozen for a workflow review.';
$string['privacy:metadata:operationid'] = 'The workflow operation associated with the audit record.';
$string['privacy:metadata:userid'] = 'The user whose enrolment is affected.';
$string['privacy:metadata:courseid'] = 'The course associated with the enrolment.';
$string['privacy:metadata:enrolid'] = 'The enrolment instance identifier.';
$string['privacy:metadata:userenrolmentid'] = 'The exact user enrolment identifier.';
$string['privacy:metadata:activekey'] = 'Internal uniqueness marker for an active managed suspension.';
$string['privacy:metadata:reason'] = 'The administrative reason entered for the suspension.';
$string['privacy:metadata:status'] = 'The current audit record status.';
$string['privacy:metadata:createdby'] = 'The user who created or performed the operation.';
$string['privacy:metadata:timecreated'] = 'The time the record was created.';
$string['privacy:metadata:reactivatedby'] = 'The user who reactivated or reconciled the record.';
$string['privacy:metadata:timereactivated'] = 'The time of reactivation or reconciliation.';

$string['toomanyusers'] = 'A maximum of 500 users can be processed in one operation.';
$string['toomanycourses'] = 'A maximum of 500 courses can be processed in one operation.';
$string['reasontoolong'] = 'The reason must contain at most 1,000 characters.';
$string['privacy:metadata:operationuser'] = 'Users selected for a temporary suspension workflow.';
$string['csvtoomanymatched'] = '{$a} unique users were found. Reduce the file to at most 500 users per operation.';
$string['exporthistory'] = 'Export current history as CSV';
$string['taskcleanupoperations'] = 'Clean up expired suspension workflow operations';
$string['privacy:metadata:operationtoken'] = 'Random token used to identify the temporary workflow.';
$string['privacy:metadata:courseids'] = 'The courses selected in the temporary workflow.';
$string['privacy:metadata:forcedcourseid'] = 'The course which scoped a course-level shortcut operation.';
$string['privacy:metadata:operationstatus'] = 'The current state of the temporary workflow.';
$string['privacy:metadata:claimtoken'] = 'Random token used to make confirmation single-use.';
$string['privacy:metadata:timemodified'] = 'The last time the temporary workflow was changed.';
$string['privacy:metadata:expiresat'] = 'The time when an unconfirmed temporary workflow expires.';
$string['privacy:metadata:consumedat'] = 'The time when the workflow was confirmed and consumed.';
$string['privacy:metadata:enroltype'] = 'The enrolment method associated with the frozen link.';
$string['privacy:metadata:supported'] = 'Whether the enrolment method was supported at review time.';
$string['privacy:metadata:supportreason'] = 'The reason an enrolment method could not be managed.';
$string['supportreason_methodnotallowlisted'] = 'the enrolment method is not in the explicit supported-method list';
$string['supportreason_pluginunavailable'] = 'the enrolment plugin is unavailable or disabled';
$string['supportreason_instancedisabled'] = 'the enrolment instance is disabled';
$string['supportreason_methodprotected'] = 'the enrolment method does not allow manual management';
$string['supportreason_missingmethodcapability'] = 'the operator does not have the enrolment method management capability';
