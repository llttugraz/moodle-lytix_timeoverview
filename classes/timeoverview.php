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
 * A course overview and filter plugin
 *
 * @package    lytix_timeoverview
 * @author     Guenther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview;

use cache_definition;
use context_course;
use lytix_helper\calculation_helper;
use lytix_helper\course_settings;

/**
 * Class timeoverview
 */
class timeoverview implements \cache_data_source {
    /**
     * Variable for instance.
     *
     * @var null
     */
    protected static $instance = null;

    /**
     * Gets instance for cache.
     *
     * @param cache_definition $definition
     * @return object|timeoverview|null
     */
    public static function get_instance_for_cache(cache_definition $definition) {
        if (is_null(self::$instance)) {
            self::$instance = new timeoverview();
        }
        return self::$instance;
    }

    /**
     * mandatory function
     *
     * @param int $key
     * @return array|mixed
     */
    public function load_for_cache($key) {
        return self::get_timeoverview($key);
    }

    /**
     * mandatory function
     *
     * @param array $keys
     * @return array
     */
    public function load_many_for_cache(array $keys) {
        $courses = [];
        foreach ($keys as $key) {
            if ($course = $this->load_for_cache((int) $key)) {
                $courses[(int) $key] = $course;
            }
        }
        return $courses;
    }

    /**
     * Load timeoverview.
     *
     * @param int $courseid
     * @return bool|float|int|mixed|string
     * @throws \coding_exception
     */
    public static function load_timeoverview($courseid) {
        $cache = \cache::make('lytix_timeoverview', 'timeoverview');
        return $cache->get($courseid);
    }

    /**
     * Get the data for timeoverview widget. We use the aggregation method from the helper class and
     * style the data accordingly.
     *
     * @param int $courseid
     * @return array
     */
    private static function get_timeoverview($courseid) {

        $start = course_settings::getcoursestartdate($courseid);
        $courseend = course_settings::getcourseenddate($courseid);
        $today = new \DateTime('today midnight');
        $end = null;

        // Special case for iMooX. There is no Semesterend or a old one.
        if ($courseend && $courseend->getTimestamp() <= $today->getTimestamp()) {
            $end = $today;
        } else {
            $end = $courseend;
        }

        $record = calculation_helper::get_activity_aggregation($courseid, $start->getTimestamp(), $end->getTimestamp());

        $sum = $record['time']['core'] + $record['time']['forum'] + $record['time']['grade'] +
            $record['time']['submission'] + $record['time']['resource'] + $record['time']['quiz'] +
            $record['time']['video'] + $record['time']['feedback'];

        $data[] = ['Type' => 'Resource', 'MedianTime' => calculation_helper::div($record['time']['resource'], $sum)];
        $data[] = ['Type' => 'Video', 'MedianTime' => calculation_helper::div($record['time']['video'], $sum)];
        $data[] = ['Type' => 'Submission', 'MedianTime' => calculation_helper::div($record['time']['submission'], $sum)];
        $data[] = ['Type' => 'Quiz', 'MedianTime' => calculation_helper::div($record['time']['quiz'], $sum)];
        $data[] = ['Type' => 'Forum', 'MedianTime' => calculation_helper::div($record['time']['forum'], $sum)];
        $data[] = ['Type' => 'Grade', 'MedianTime' => calculation_helper::div($record['time']['grade'], $sum)];
        $data[] = ['Type' => 'Course', 'MedianTime' => calculation_helper::div($record['time']['core'], $sum)];
        $data[] = ['Type' => 'Feedback', 'MedianTime' => calculation_helper::div($record['time']['feedback'], $sum)];

        $return['Activities'] = $data;
        return $return;
    }
}
