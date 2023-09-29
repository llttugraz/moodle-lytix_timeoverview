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
 * Timeoverview plugin for lytix
 *
 * @package    lytix_timeoverview
 * @author     Viktoria Wieser
 * @copyright  2021 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Lytix Timeoverview';

$string['privacy:metadata'] = 'This plugin does not store any data.';

$string['cachedef_timeoverview'] = 'Cache for the timeoverview subplugin.';
$string['cron_refresh_lytix_timeoverview_cache'] = "Refresh caches for lytix subplugin timeoverview.";

// Activity, Time Division.
$string['overview_activities'] = 'Time Overview';

$string['timeoverview_description'] = 'This shows the mean percentage of time students spend on different kinds of activity.';
$string['timeoverview_label_forum'] = 'Forum';
$string['timeoverview_label_quiz'] = 'Quiz';
$string['timeoverview_label_video'] = 'Video';
$string['timeoverview_label_core'] = 'Course';
$string['timeoverview_label_bbb'] = "BigBlueButton";
$string['timeoverview_label_grade'] = 'Grade';
$string['timeoverview_label_submission'] = 'Assignments';
$string['timeoverview_label_resource'] = 'Resources';
$string['timeoverview_label_feedback'] = 'Feedback';

$string['time'] = 'Time';
$string['clicks'] = 'Clicks';

// Privacy.
$string['privacy:nullproviderreason'] = 'This plugin has no database to store user information. It only uses APIs in mod_assign to help with displaying the grading interface.';
