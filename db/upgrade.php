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
 * JW Player media filtering upgrade routines.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2015 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_filter_jwplayer_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2015112500) {
        // Delete customskin file, as the setting has been removed.
        if (get_config('filter_jwplayer', 'customskin') !== false) {
            $fs = get_file_storage();
            $fs->delete_area_files(context_system::instance()->id, 'filter_jwplayer', 'playerskin', 0);
            unset_config('customskin', 'filter_jwplayer');
        }

        // Unset other removed settings.
        unset_config('gatrackingobject', 'filter_jwplayer');
        unset_config('securehosting', 'filter_jwplayer');
        unset_config('accounttoken', 'filter_jwplayer');

        upgrade_plugin_savepoint(true, 2015112500, 'filter', 'jwplayer');
    }
    return true;
}
