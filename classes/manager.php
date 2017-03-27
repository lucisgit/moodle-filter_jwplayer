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
 *  JW Player media filter manager.
 *
 * @package    filter_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/filter/jwplayer/classes/player.php');

/**
 * We have to override manager for media files to use it in the filter.
 *
 * @package    filter_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class filter_jwplayer_manager extends core_media_manager {

    public static function instance($page = null) {
        // Use the passed $page if given, otherwise the $PAGE global.
        if (!$page) {
            global $PAGE;
            $page = $PAGE;
        }
        if (self::$instance === null || ($page && self::$instance->page !== $page)) {
            self::$instance = new self($page);
        }
        return self::$instance;
    }

    protected function get_players() {
        $players = parent::get_players();
        array_unshift($players, new filter_jwplayer_media());
        $this->players = $players;
        return $this->players;
    }
}
