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
 * External function used to search users.
 *
 * @package    tool_enrolsuspension
 * @copyright  2026
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_enrolsuspension\external;

defined('MOODLE_INTERNAL') || die();

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External function used by the user autocomplete field.
 */
class search_users extends external_api {

    /**
     * Defines the parameters accepted by the function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(
                PARAM_TEXT,
                'Name, email, username or ID number'
            ),
        ]);
    }

    /**
     * Searches users.
     *
     * @param string $query Search text.
     * @return array
     */
    public static function execute(string $query): array {
        global $DB;

        $params = self::validate_parameters(
            self::execute_parameters(),
            ['query' => $query]
        );

        $context = context_system::instance();

        self::validate_context($context);

        require_capability(
            'tool/enrolsuspension:suspend',
            $context
        );

        $query = trim($params['query']);

        // Evita consultas muito amplas.
        if (\core_text::strlen($query) < 2) {
            return [];
        }

        $likevalue = '%' . $DB->sql_like_escape($query) . '%';

        $sql = "SELECT id,
                       firstname,
                       lastname,
                       email,
                       username,
                       idnumber
                  FROM {user}
                 WHERE deleted = 0
                   AND id <> :guestid
                   AND (
                        " . $DB->sql_like('firstname', ':firstname', false) . "
                        OR " . $DB->sql_like('lastname', ':lastname', false) . "
                        OR " . $DB->sql_like('email', ':email', false) . "
                        OR " . $DB->sql_like('username', ':username', false) . "
                        OR " . $DB->sql_like('idnumber', ':idnumber', false) . "
                   )
              ORDER BY firstname ASC, lastname ASC";

        $sqlparams = [
            'guestid' => guest_user()->id,
            'firstname' => $likevalue,
            'lastname' => $likevalue,
            'email' => $likevalue,
            'username' => $likevalue,
            'idnumber' => $likevalue,
        ];

        $users = $DB->get_records_sql(
            $sql,
            $sqlparams,
            0,
            20
        );

        $results = [];

        foreach ($users as $user) {
            $results[] = [
                'id' => (int) $user->id,
                'fullname' => fullname($user),
                'email' => $user->email,
                'username' => $user->username,
                'idnumber' => $user->idnumber ?? '',
            ];
        }

        return $results;
    }

    /**
     * Defines the data returned by the function.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(
                    PARAM_INT,
                    'User ID'
                ),
                'fullname' => new external_value(
                    PARAM_TEXT,
                    'Full name'
                ),
                'email' => new external_value(
                    PARAM_EMAIL,
                    'Email address'
                ),
                'username' => new external_value(
                    PARAM_RAW,
                    'Username'
                ),
                'idnumber' => new external_value(
                    PARAM_RAW,
                    'ID number'
                ),
            ])
        );
    }
}
