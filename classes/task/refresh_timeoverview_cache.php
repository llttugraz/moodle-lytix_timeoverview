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
 * Refresh the cache for the activity plugin
 *
 * @package    lytix_timeoverview
 * @category   task
 * @author     Günther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview\task;
// Important to get libraries here, else we get a conflict with the unit-tests.
// Note that we do NOT need to use global $CFG.
use lytix_timeoverview\cache_reset;
use lytix_timeoverview\timeoverview;

/**
 * Class refresh_timeoverview_cache
 */
class refresh_timeoverview_cache extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     * @return string
     */
    public function get_name() {
        return get_string('cron_refresh_lytix_timeoverview_cache', 'lytix_timeoverview');
    }

    /**
     * Execute Task.
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function execute(): bool {
        global $DB;
        if (get_config('local_lytix', 'platform') == 'learners_corner' ||
            get_config('local_lytix', 'platform') == 'course_dashboard' ||
            get_config('local_lytix', 'platform') == 'creators_dashboard') {
            $courseids = explode(',', get_config('local_lytix', 'course_list'));
            $success = true;
            foreach ($courseids as $courseid) {
                if (!$DB->record_exists('course', ['id' => $courseid])) {
                    return false;
                }
                if (!cache_reset::reset_cache((int)$courseid)) {
                    echo "There was an error deleting the cache for course $courseid.";
                    $success = false;
                }
                if (!timeoverview::load_timeoverview((int)$courseid)) {
                    echo "There was an error creating the caches for course $courseid.";
                    $success = false;
                }
            }
            return $success;
        }
        return false;
    }
}

