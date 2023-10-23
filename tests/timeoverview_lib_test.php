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
 * Testcases for timoverview.
 *
 * @package    lytix_timeoverview
 * @author     Günther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/lib/externallib.php');

use external_api;
use externallib_advanced_testcase;
use lytix_helper\dummy;

/**
 * Class timeoverview_test.
 * @coversDefaultClass  \lytix_timeoverview\timeoverview_lib
 * @group learners_corner
 */
class timeoverview_lib_test extends externallib_advanced_testcase {
    /**
     * Variable for course.
     *
     * @var \stdClass|null
     */
    private $course = null;

    /**
     * Variable for the context
     *
     * @var bool|\context|\context_course|null
     */
    private $context = null;

    /**
     * Variable for the students
     *
     * @var array
     */
    private $students = [];

    /**
     * Setup called before any test case.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $course            = new \stdClass();
        $course->fullname  = 'Timeoverview Test Course';
        $course->shortname = 'timeoverview_test_course';
        $course->category  = 1;

        $this->students = dummy::create_fake_students(10);
        $return         = dummy::create_course_and_enrol_users($course, $this->students);
        $this->course   = $return['course'];
        $this->context  = \context_course::instance($this->course->id);

        set_config('course_list', $this->course->id, 'local_lytix');
        // Set platform.
        set_config('platform', 'learners_corner', 'local_lytix');
        // Set start and end.
        $semstart = new \DateTime('4 months ago');
        $semstart->setTime(0, 0);
        set_config('semester_start', $semstart->format('Y-m-d'), 'local_lytix');
        $semend = new \DateTime('today midnight');
        set_config('semester_end', $semend->format('Y-m-d'), 'local_lytix');
    }

    /**
     * Tests timeoverview webservice.
     * @covers ::timeoverview_get
     * @covers ::timeoverview_get_returns
     * @covers ::timeoverview_get_parameters
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     */
    public function test_empty_timeoverview() {
        $return = timeoverview_lib::timeoverview_get($this->context->id, $this->course->id);
        try {
            external_api::clean_returnvalue(timeoverview_lib::timeoverview_get_returns(), $return);
        } catch (\invalid_response_exception $e) {
            if ($e) {
                self::assertFalse(true, "invalid_responce_exception thrown.");
            }
        }
        // Basic asserts.
        $this::assertEquals(1, count($return));

        $this->assertTrue(key_exists('Activities', $return));

        // 7 Activities.
        $this::assertEquals(8, count($return['Activities']));
    }

    /**
     * Create activites and check timeoverview webservice.
     * @covers ::timeoverview_get
     * @covers ::timeoverview_get_returns
     * @covers ::timeoverview_get_parameters
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \invalid_response_exception
     * @throws \restricted_context_exception
     */
    public function test_timeoverview_activities() {
        $date = new \DateTime('4 months ago');
        date_add($date, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));

        dummy::create_fake_data_for_course($date, $today, $this->students[0], $this->course->id, $this->context);

        $result = timeoverview_lib::timeoverview_get($this->context->id, $this->course->id);
        external_api::clean_returnvalue(timeoverview_lib::timeoverview_get_returns(), $result);

        $this->assertTrue(key_exists('Activities', $result));
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $result['Activities'][$i]['MedianTime'];
        }
        // Sum should always be equal to 1.
        $this::assertEquals(1, round($sum));
    }
}
