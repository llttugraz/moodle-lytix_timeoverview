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
 * This is a one-line short description of the file.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    lytix_timeoverview
 * @author     Guenther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * Class timeoverview_lib
 */
class timeoverview_lib extends \external_api {
    /**
     * Checks parameters.
     * @return \external_function_parameters
     */
    public static function timeoverview_get_parameters() {
        return new \external_function_parameters(
                [
                        'contextid' => new \external_value(PARAM_INT, 'Context Id', VALUE_REQUIRED),
                        'courseid'  => new \external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                ]
        );
    }

    /**
     * Checks return values.
     * @return \external_single_structure
     */
    public static function timeoverview_get_returns() {
        return new \external_single_structure(
                [
                        'Activities' => new \external_multiple_structure(
                                new \external_single_structure(
                                    [
                                            'Type'       => new \external_value(PARAM_TEXT, 'Type fo target (Name)',
                                                                                    VALUE_REQUIRED),
                                            'MedianTime' => new \external_value(PARAM_FLOAT,
                                                                            'Duration of time spend for this target (median)',
                                                                            VALUE_REQUIRED),
                                    ], 'external_single_structure for activities', VALUE_OPTIONAL
                                ), 'external_multiple_structure for activities', VALUE_OPTIONAL
                        ),
                ]
        );
    }

    /**
     * Gets data for timeoverview.
     * @param int $contextid
     * @param int $courseid
     * @return mixed
     * @throws \coding_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     */
    public static function timeoverview_get($contextid, $courseid) {
        $params = self::validate_parameters(self::timeoverview_get_parameters(), [
                'contextid' => $contextid,
                'courseid'  => $courseid
        ]);

        // We always must call validate_context in a webservice.
        $context = \context::instance_by_id($params['contextid'], MUST_EXIST);
        self::validate_context($context);

        // Check for the helper plugin.
        $plugin = \local_lytix\helper\plugin_check::is_installed('helper');
        if ($plugin) {
            return timeoverview::load_timeoverview((int)$courseid);
        } else {
            return [];
        }
    }
}
