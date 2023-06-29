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
 * Helper class for the cache.
 *
 * @package    lytix_timeoverview
 * @author     Günther Moser <moser@tugraz.at>
 * @copyright  2023 Educational Technologies, Graz, University of Technology
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace lytix_timeoverview;

/**
 * Class cache_reset
 */
class cache_reset {
    /**
     * Resets lytix_timeoverview cache.
     * @param int $courseid
     */
    public static function reset_cache($courseid): bool {
        // Reset Activities'.
        $cache = \cache::make('lytix_timeoverview', 'timeoverview');
        if ($cache->get($courseid)) {
            return $cache->delete($courseid);
        }
        return true;
    }
}
