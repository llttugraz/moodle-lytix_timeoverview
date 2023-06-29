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
 * Testcases for cache building
 *
 * @package    lytix_timeoverview
 * @author     Guenther Moser
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/lib/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use lytix_helper\dummy;
use lytix_timeoverview\task\refresh_timeoverview_cache;

/**
 * Building cache tests.
 * @group learners_corner
 * @coversDefaultClass \lytix_timeoverview\task\refresh_timeoverview_cache
 */
class refresh_timeoverview_cache_test extends \externallib_advanced_testcase {
    /**
     * Course variable.
     * @var \stdClass|null
     */
    private $course = null;

    /**
     * Variable for the students
     *
     * @var array
     */
    private $students = [];

    /**
     * Sets up course for tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $date = new \DateTime('4 months ago');
        date_add($date, date_interval_create_from_date_string('6 hours'));
        $today = new \DateTime('today midnight');
        date_add($today, date_interval_create_from_date_string('6 hours'));

        set_config('semester_start', $date->format('Y-m-d'), 'local_lytix');
        set_config('semester_end', $today->format('Y-m-d'), 'local_lytix');

        // Set platform.
        set_config('platform', 'learners_corner', 'local_lytix');

        $course            = new \stdClass();
        $course->fullname  = 'Timeoverview Test Course';
        $course->shortname = 'timeoverview_test_course';
        $course->category  = 1;
        $course->enablecompletion = 1;

        $this->students = dummy::create_fake_students(10);
        $return         = dummy::create_course_and_enrol_users($course, $this->students);
        $this->course   = $return['course'];
        $context  = \context_course::instance($this->course->id);

        // Add course to config list.
        set_config('course_list', $this->course->id, 'local_lytix');

        dummy::create_fake_data_for_course($date, $today, $this->students[0], $this->course->id, $context);
        dummy::create_fake_data_for_course($date, $today, $this->students[1], $this->course->id, $context);
        dummy::create_fake_data_for_course($date, $today, $this->students[2], $this->course->id, $context);
    }

    /**
     * Test get_name of task.
     * @covers ::get_name
     * @return void
     */
    public function test_task_get_name() {
        $task = new refresh_timeoverview_cache();
        self::assertEquals("Refresh caches for lytix subplugin timeoverview.", $task->get_name());
    }

    /**
     * Test execute of task.
     * @covers ::execute
     * @covers \lytix_timeoverview\timeoverview::load_timeoverview
     * @covers \lytix_timeoverview\timeoverview::load_for_cache
     * @covers \lytix_timeoverview\timeoverview::get_instance_for_cache
     * @covers \lytix_timeoverview\timeoverview::get_timeoverview
     * @covers \lytix_timeoverview\timeoverview::aggregated_logstore_get
     * @covers \lytix_timeoverview\cache_reset::reset_cache
     * @return void
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function test_task_execute() {
        $task = new refresh_timeoverview_cache();
        self::assertTrue($task->execute(), "task failed.");
    }

    /**
     * Test fail of execute.
     * @covers ::execute
     * @covers \lytix_timeoverview\timeoverview::load_timeoverview
     * @covers \lytix_timeoverview\timeoverview::load_for_cache
     * @covers \lytix_timeoverview\timeoverview::get_instance_for_cache
     * @covers \lytix_timeoverview\timeoverview::get_timeoverview
     * @covers \lytix_timeoverview\timeoverview::aggregated_logstore_get
     * @covers \lytix_timeoverview\cache_reset::reset_cache
     * @return void
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public function test_task_execute_fail() {
        set_config('course_list', "0," . $this->course->id . ",666", 'local_lytix');
        $task = new refresh_timeoverview_cache();
        self::assertFalse($task->execute(), "task should fail, but did not.");
    }
}
