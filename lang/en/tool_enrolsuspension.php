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

/** English language strings. @package tool_enrolsuspension */
defined('MOODLE_INTERNAL') || die();

$string['activesuspensions'] = 'Active suspensions';
$string['alreadymanagedsuspension'] = 'An active plugin suspension already exists for one of the exact enrolment links.';
$string['availablecourses'] = 'Courses found';
$string['confirmsuspension'] = 'Confirm suspension';
$string['continuewithfound'] = 'Continue with uniquely matched users';
$string['coursesuspensions'] = 'Suspensions';
$string['csvambiguous'] = 'Ambiguous';
$string['csvambiguouscount'] = 'Ambiguous values: {$a}';
$string['csvdelimiter'] = 'Delimiter';
$string['csvdelimitercomma'] = 'Comma';
$string['csvdelimitersemicolon'] = 'Semicolon';
$string['csvdelimitertab'] = 'Tab';
$string['csvfile'] = 'CSV or TXT file';
$string['csvfoundcount'] = 'Unique users found: {$a}';
$string['csvinstructions'] = 'The file must contain exactly one column with a required header: username, email, or idnumber. ' .
    'A maximum of 1,000 values is processed per upload.';
$string['csvinvalidheader'] = 'Invalid CSV header. Use exactly one column named username, email, or idnumber.';
$string['csvmissing'] = 'Not found';
$string['csvmissingcount'] = 'Values not found: {$a}';
$string['csvnousers'] = 'No uniquely matching users were found.';
$string['csvpreview'] = 'Import preview';
$string['csvtoomanymatched'] = '{$a} unique users were found. Reduce the file to at most 500 users per operation.';
$string['dashboard'] = 'Dashboard';
$string['deletedcoursefallback'] = 'Deleted/missing course #{$a}';
$string['deleteduserfallback'] = 'Deleted/missing user #{$a}';
$string['details'] = 'Details';
$string['enrolledlabel'] = 'Enrolled';
$string['enrolledselectedusers'] = 'Enrolled users';
$string['enrolmentdetails'] = 'Enrolment link details';
$string['enrolmentlinkstoaffect'] = 'Exact enrolment links';
$string['enrolmentmethod'] = 'Enrolment method';
$string['enrolmentstateunchanged'] = 'Moodle did not change the expected enrolment state. The operation was rolled back.';
$string['enrolsuspension:import'] = 'Import users for suspension';
$string['enrolsuspension:manage'] = 'Legacy: manage enrolment suspensions';
$string['enrolsuspension:reactivate'] = 'Reactivate suspended enrolments';
$string['enrolsuspension:suspend'] = 'Search users and suspend enrolments';
$string['enrolsuspension:view'] = 'View suspension history';
$string['exporthistory'] = 'Export current history as CSV';
$string['history'] = 'History';
$string['idnumberlabel'] = 'ID number';
$string['importcsv'] = 'Import CSV/TXT';
$string['minimumcharacters'] = 'Enter at least 2 characters.';
$string['navigationcategory'] = 'Suspension control';
$string['next'] = 'Next';
$string['noactiveenrolments'] = 'No currently active enrolment links remain for this selection.';
$string['nocoursesfound'] = 'No currently active courses were found for the selected users.';
$string['nohistoryrecords'] = 'No records were found.';
$string['notenrolledlabel'] = 'Not enrolled';
$string['nousersfound'] = 'No users were found.';
$string['nousersselected'] = 'No users have been selected.';
$string['operationalreadyused'] = 'This operation has already been submitted or is being processed.';
$string['operationcontainsunsupported'] = 'The reviewed operation contains an unsupported enrolment method.';
$string['operationexpired'] = 'This suspension operation expired. Start a new operation.';
$string['operationgenericerror'] = 'The operation could not be completed. No partial suspension was committed.';
$string['operationlocked'] = 'This operation can no longer be edited.';
$string['operationnotready'] = 'The operation is not ready for confirmation.';
$string['operationstatechanged'] = 'The enrolment state changed after review. No changes were applied. Review the operation again.';
$string['performedby'] = 'Performed by';
$string['permanent'] = 'Permanent until manual reactivation';
$string['permanentsuspensionnotice'] = 'The suspension has no automatic expiry. Reactivation is manual.';
$string['pluginname'] = 'Enrolment suspension control';
$string['postrequired'] = 'This action only accepts a POST request.';
$string['privacy:metadata:activekey'] = 'Internal uniqueness marker for an active managed suspension.';
$string['privacy:metadata:claimtoken'] = 'Random token used to make confirmation single-use.';
$string['privacy:metadata:consumedat'] = 'The time when the workflow was confirmed and consumed.';
$string['privacy:metadata:courseid'] = 'The course associated with the enrolment.';
$string['privacy:metadata:courseids'] = 'The courses selected in the temporary workflow.';
$string['privacy:metadata:createdby'] = 'The user who created or performed the operation.';
$string['privacy:metadata:enrolid'] = 'The enrolment instance identifier.';
$string['privacy:metadata:enroltype'] = 'The enrolment method associated with the frozen link.';
$string['privacy:metadata:expiresat'] = 'The time when an unconfirmed temporary workflow expires.';
$string['privacy:metadata:forcedcourseid'] = 'The course which scoped a course-level shortcut operation.';
$string['privacy:metadata:operation'] = 'Temporary workflow operations created by suspension operators.';
$string['privacy:metadata:operationid'] = 'The workflow operation associated with the audit record.';
$string['privacy:metadata:operationitem'] = 'Exact enrolment links frozen for a workflow review.';
$string['privacy:metadata:operationstatus'] = 'The current state of the temporary workflow.';
$string['privacy:metadata:operationtoken'] = 'Random token used to identify the temporary workflow.';
$string['privacy:metadata:operationuser'] = 'Users selected for a temporary suspension workflow.';
$string['privacy:metadata:reactivatedby'] = 'The user who reactivated or reconciled the record.';
$string['privacy:metadata:reason'] = 'The administrative reason entered for the suspension.';
$string['privacy:metadata:status'] = 'The current audit record status.';
$string['privacy:metadata:supported'] = 'Whether the enrolment method was supported at review time.';
$string['privacy:metadata:supportreason'] = 'The reason an enrolment method could not be managed.';
$string['privacy:metadata:table'] = 'Audit records for enrolment suspension and reactivation.';
$string['privacy:metadata:timecreated'] = 'The time the record was created.';
$string['privacy:metadata:timemodified'] = 'The last time the temporary workflow was changed.';
$string['privacy:metadata:timereactivated'] = 'The time of reactivation or reconciliation.';
$string['privacy:metadata:userenrolmentid'] = 'The exact user enrolment identifier.';
$string['privacy:metadata:userid'] = 'The user whose enrolment is affected.';
$string['reactivate'] = 'Reactivate';
$string['reactivatedsuspensions'] = 'Reactivated suspensions';
$string['reactivateselected'] = 'Reactivate selected';
$string['reactivationalreadychanged'] = 'Record {$a}: the original enrolment link had already changed state. ' .
    'The history item was marked stale.';
$string['reactivationgenericerror'] = 'Record {$a}: reactivation failed. Technical details were sent to developer debugging.';
$string['reactivationinfo'] = 'Reactivation/status change';
$string['reactivationsskipped'] = '{$a} link(s) were not reactivated. Check the history and enrolment method.';
$string['reactivationstale'] = 'Record {$a}: the original enrolment link no longer exists. The history item was marked stale.';
$string['reactivationsuccess'] = '{$a} exact enrolment link(s) reactivated successfully.';
$string['reason'] = 'Reason';
$string['reasonrequired'] = 'Enter a suspension reason.';
$string['reasontoolong'] = 'The reason must contain at most 1,000 characters.';
$string['removeuser'] = 'Remove user';
$string['review'] = 'Review';
$string['search'] = 'Search';
$string['searching'] = 'Searching...';
$string['searchresults'] = 'Users found';
$string['searchuser'] = 'Search user';
$string['searchusers'] = 'Search users';
$string['searchusersplaceholder'] = 'Enter name, email, username or ID number';
$string['selectatleastonecourse'] = 'Select at least one course.';
$string['selectatleastoneuser'] = 'Select at least one user.';
$string['selectcourses'] = 'Select courses';
$string['selectedusers'] = 'Selected users';
$string['selectuser'] = 'Select';
$string['selectusersfirst'] = 'Select one or more users first.';
$string['shortnamecourse'] = 'Short name';
$string['stalesuspensions'] = 'Stale records';
$string['summaryitem'] = 'Item';
$string['supported'] = 'Supported';
$string['supportreason_instancedisabled'] = 'the enrolment instance is disabled';
$string['supportreason_methodnotallowlisted'] = 'the enrolment method is not in the explicit supported-method list';
$string['supportreason_methodprotected'] = 'the enrolment method does not allow manual management';
$string['supportreason_missingmethodcapability'] = 'the operator does not have the enrolment method management capability';
$string['supportreason_pluginunavailable'] = 'the enrolment plugin is unavailable or disabled';
$string['supportstatus'] = 'Management status';
$string['suspend'] = 'Suspend';
$string['suspendallcurrentcourses'] = 'Suspend all current active courses of the selected users';
$string['suspendedon'] = 'Suspended on';
$string['suspensiondetails'] = 'Suspension details';
$string['suspensionsuccess'] = '{$a} exact enrolment link(s) suspended successfully.';
$string['suspensiontype'] = 'Suspension type';
$string['taskcleanupoperations'] = 'Clean up expired suspension workflow operations';
$string['toomanycourses'] = 'A maximum of 500 courses can be processed in one operation.';
$string['toomanyusers'] = 'A maximum of 500 users can be processed in one operation.';
$string['unknownoperator'] = 'Unknown/deleted operator';
$string['unsupportedmethodreason'] = 'Not supported ({$a})';
$string['unsupportedoperationblocked'] = 'This operation cannot be confirmed because one or more active enrolment links ' .
    'are managed by a method which does not permit manual changes. This prevents a synchronisation source from silently ' .
    'undoing the suspension.';
$string['usercoursepairstoaffect'] = 'User-course combinations';
