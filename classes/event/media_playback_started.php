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
 * The JWPlayer media_playback_started event.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Owen Barritt, Wine & Spirit Education Trust
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_jwplayer\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Playback started.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Owen Barritt, Wine & Spirit Education Trust
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_playback_started extends \core\event\base {
    protected function init() {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public static function get_name() {
        return get_string('eventmedia_playback_started', 'filter_jwplayer');
    }

    public function get_description() {
        $logstring = "The user with id {$this->userid} has started playback of the video {$this->other['file']}";

        if (isset($this->other['position']) && $this->other['position'] != 'undefined') {
            $logstring .= " from {$this->other['position']}s";
        }
        if (isset($this->other['bitrate']) && $this->other['bitrate'] != 'undefined') {
            $logstring .= " at a bitrate of {$this->other['bitrate']}";
        }

        return $logstring;
    }
}