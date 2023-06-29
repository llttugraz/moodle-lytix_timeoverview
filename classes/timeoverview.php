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
 * @category   cache
 * @author     Güntgher Moser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
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
     * Get timeoverview.
     *
     * @param int $courseid
     * @return array
     */
    private static function get_timeoverview($courseid) {

        try {
            $coursecontext = context_course::instance($courseid);
            $result        = self::aggregated_logstore_get($coursecontext->id, $courseid);
            $records       = $result['data'];
        } catch (\dml_exception $e) {
            return [$e->getMessage()];
        }

        // Times arrays.
        $coretime       = [];
        $forumtime      = [];
        $gradetime      = [];
        $submissiontime = [];
        $resourcetime   = [];
        $quiztime       = [];
        $videotime      = [];
        $feedbacktime   = [];

        foreach ($records as $record) {
            if ($record['Aggregation'] == 'Time' || $record['Aggregation'] == 'Zeit') {
                $coretime[]       = $record['core'];
                $forumtime[]      = $record['forum'];
                $gradetime[]      = $record['grade'];
                $submissiontime[] = $record['submission'];
                $resourcetime[]   = $record['resource'];
                $quiztime[]       = $record['quiz'];
                $videotime[]      = $record['video'];
                $feedbacktime[]   = $record['feedback'];
            }
        }

        $coremediantime       = calculation_helper::median($coretime);
        $forummediantime      = calculation_helper::median($forumtime);
        $grademediantime      = calculation_helper::median($gradetime);
        $submissionmediantime = calculation_helper::median($submissiontime);
        $resourcemediantime   = calculation_helper::median($resourcetime);
        $quizmediantime       = calculation_helper::median($quiztime);
        $videomediantime      = calculation_helper::median($videotime);
        $feedbackmediantime   = calculation_helper::median($feedbacktime);

        $data = [];

        $allmediantime = $coremediantime + $forummediantime + $grademediantime + $submissionmediantime +
                         $resourcemediantime + $quizmediantime + $videomediantime + $feedbackmediantime;

        $coremediantime       = calculation_helper::div($coremediantime, $allmediantime);
        $forummediantime      = calculation_helper::div($forummediantime, $allmediantime);
        $grademediantime      = calculation_helper::div($grademediantime, $allmediantime);
        $submissionmediantime = calculation_helper::div($submissionmediantime, $allmediantime);
        $resourcemediantime   = calculation_helper::div($resourcemediantime, $allmediantime);
        $quizmediantime       = calculation_helper::div($quizmediantime, $allmediantime);
        $videomediantime      = calculation_helper::div($videomediantime, $allmediantime);
        $feedbackmediantime   = calculation_helper::div($feedbackmediantime, $allmediantime);

        $data[] = ['Type' => 'Resource', 'MedianTime' => $resourcemediantime];
        $data[] = ['Type' => 'Video', 'MedianTime' => $videomediantime];
        $data[] = ['Type' => 'Submission', 'MedianTime' => $submissionmediantime];
        $data[] = ['Type' => 'Quiz', 'MedianTime' => $quizmediantime];
        $data[] = ['Type' => 'Forum', 'MedianTime' => $forummediantime];
        $data[] = ['Type' => 'Grade', 'MedianTime' => $grademediantime];
        $data[] = ['Type' => 'Course', 'MedianTime' => $coremediantime];
        $data[] = ['Type' => 'Feedback', 'MedianTime' => $feedbackmediantime];

        $return['Activities'] = $data;
        return $return;
    }

    /**
     * Gets activities.
     *
     * @param int $contextid
     * @param int $courseid
     * @return array[]
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function aggregated_logstore_get($contextid, $courseid) {
        global $DB;

        $start  = course_settings::getcoursestartdate($courseid);
        date_add($start, date_interval_create_from_date_string('2 hours'));

        // Use smaller date.
        $courseend = course_settings::getcourseenddate($courseid);
        $today       = new \DateTime('today midnight');
        $end         = null;
        if ($today->getTimestamp() < $courseend->getTimestamp()) {
            $end = $today;
        } else {
            $end = $courseend;
        }
        date_add($end, date_interval_create_from_date_string('2 hours'));

        $data = [];

        // First get records from start till today.
        $cnt = "SELECT COUNT(*) FROM {lytix_helper_dly_mdl_acty} logs
                WHERE logs.courseid = :courseid AND logs.contextid = :contextid
                AND logs.timestamp >= :startdate AND logs.timestamp <= :enddate";

        $params['courseid']  = $courseid;
        $params['contextid'] = $contextid;
        $params['startdate'] = $start->getTimestamp();
        $params['enddate']   = $end->getTimestamp();

        $totalrecords = $DB->count_records_sql($cnt, $params);

        // Times.
        $coretime       = 0;
        $forumtime      = 0;
        $gradetime      = 0;
        $submissiontime = 0;
        $resourcetime   = 0;
        $quiztime       = 0;
        $bbbtime        = 0;
        $h5ptime        = 0;
        $feedbacktime   = 0;

        // Clicks.
        $coreclick       = 0;
        $forumclick      = 0;
        $gradeclick      = 0;
        $submissionclick = 0;
        $resourceclick   = 0;
        $quizclick       = 0;
        $bbbclick        = 0;
        $h5pclick        = 0;
        $feedbackclick   = 0;

        $processed = 0;
        $limitfrom = 0;
        $limitnum  = 75000;

        while ($processed < $totalrecords) {
            // Get records from start till today.
            $sql = "SELECT * FROM {lytix_helper_dly_mdl_acty} logs
                WHERE logs.courseid = :courseid AND logs.contextid = :contextid
                AND logs.timestamp >= :startdate AND logs.timestamp <= :enddate";

            $params['courseid']  = $courseid;
            $params['contextid'] = $contextid;
            $params['startdate'] = $start->getTimestamp();
            $params['enddate']   = $end->getTimestamp();

            $userrecords = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);

            foreach ($userrecords as $record) {
                // Aggregate specific times.
                $coretime        += $record->core_time;
                $coreclick       += $record->core_click;
                $forumtime       += $record->forum_time;
                $forumclick      += $record->forum_click;
                $gradetime       += $record->grade_time;
                $gradeclick      += $record->grade_click;
                $submissiontime  += $record->submission_time;
                $submissionclick += $record->submission_click;
                $resourcetime    += $record->resource_time;
                $resourceclick   += $record->resource_click;
                $quiztime        += $record->quiz_time;
                $quizclick       += $record->quiz_click;
                $bbbtime         += $record->bbb_time;
                $bbbclick        += $record->bbb_click;
                $h5ptime         += $record->h5p_time;
                $h5pclick        += $record->h5p_click;
                $feedbacktime    += $record->feedback_time;
                $feedbackclick   += $record->feedback_click;
            }

            $processed += count($userrecords);
            $limitfrom += $limitnum;
        }

        $data[] = [
                'Aggregation' => get_string('time', 'lytix_timeoverview'),

                'core'       => $coretime,
                'forum'      => $forumtime,
                'grade'      => $gradetime,
                'submission' => $submissiontime,
                'resource'   => $resourcetime,
                'quiz'       => $quiztime,
                'video'      => $bbbtime + $h5ptime,
                'feedback'  => $feedbacktime,
        ];

        $data[] = [
                'Aggregation' => get_string('clicks', 'lytix_timeoverview'),

                'core'       => $coreclick,
                'forum'      => $forumclick,
                'grade'      => $gradeclick,
                'submission' => $submissionclick,
                'resource'   => $resourceclick,
                'quiz'       => $quizclick,
                'video'      => $bbbclick + $h5pclick,
                'feedback'  => $feedbackclick,
        ];

        return [
                'data' => $data,
        ];
    }
}
