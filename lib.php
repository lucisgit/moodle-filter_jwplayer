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
 *  JW Player media filtering library.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/medialib.php');

if (!defined('FILTER_JWPLAYER_VIDEO_WIDTH')) {
    // Default video width if no width is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_VIDEO_WIDTH', 400);
}
if (!defined('FILTER_JWPLAYER_AUDIO_WIDTH')) {
    // Default audio width if no width is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_AUDIO_WIDTH', 400);
}
if (!defined('FILTER_JWPLAYER_AUDIO_HEIGTH')) {
    // Default audio heigth if no heigth is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_AUDIO_HEIGTH', 30);
}

/**
 *  JW Player media filtering library.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_jwplayer_media extends core_media_player {

    /**
     * Generates code required to embed the player.
     *
     * @param array $urls URLs of media files
     * @param string $name Display name; '' to use default
     * @param int $width Optional width; 0 to use default
     * @param int $height Optional height; 0 to use default
     * @param array $options Options array
     * @return string HTML code for embed
     */
    public function embed($urls, $name, $width, $height, $options) {
        global $PAGE, $CFG;
        // We do embedding only here. JW player setup is done in the filter.
        $output = '';

        $sources = array();
        foreach ($urls as $url) {
            // Add the details for this source.
            $source = array(
                'file' => urldecode($url),
            );
            if (strtolower(pathinfo($url, PATHINFO_EXTENSION)) === 'mov') {
                $source['type'] = 'mp4';
            }
            $sources[] = $source;
        }

        if (count($sources) > 0) {
            $playerid = 'filter_jwplayer_media_' . html_writer::random_id();

            $playersetupdata = array(
                'title' => $this->get_name('', $urls),
                'playlist' => array(
                    array('sources' => $sources),
                ),
            );
            // If width is not provided, use default.
            if (!$width) {
                $width = FILTER_JWPLAYER_VIDEO_WIDTH;
            }
            $playersetupdata['width'] = $width;
            // Let player choose the height unless it is provided.
            if ($height) {
                $playersetupdata['height'] = $height;
            }

            // If we are dealing with audio, show just the control bar.
            if (mimeinfo('string', $sources[0]['file']) === 'audio') {
                $playersetupdata['width'] = FILTER_JWPLAYER_AUDIO_WIDTH;
                $playersetupdata['height'] = FILTER_JWPLAYER_AUDIO_HEIGTH;
            }

            // Set up the player.
            $jsmodule = array(
                'name' => $playerid,
                'fullpath' => '/filter/jwplayer/module.js',
            );
            $playersetup = array(
                'playerid' => $playerid,
                'setupdata' => $playersetupdata,
            );
            $PAGE->requires->js_init_call('M.filter_jwplayer.init', $playersetup, true, $jsmodule);
            if (get_config('filter_jwplayer', 'downloadbutton')) {
                $img = $CFG->wwwroot.'/filter/jwplayer/img/download.png';
                $tttext = get_string('videodownloadbtntttext', 'filter_jwplayer');
                $addbuttonparams = array(
                    'playerid' => $playerid,
                    'img' => $img,
                    'tttext' => $tttext,
                );
                $PAGE->requires->js_init_call('M.filter_jwplayer.add_button', $addbuttonparams, true, $jsmodule);
            }
            $playerdiv = html_writer::tag('div', $this->get_name('', $urls), array('id' => $playerid));
            $output .= html_writer::tag('div', $playerdiv, array('class' => 'filter_jwplayer_media'));
        }

        return $output;
    }

    /**
     * Gets the list of file extensions supported by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function get_supported_extensions() {
        return explode(',', get_config('filter_jwplayer', 'enabledextensions'));
    }

    /**
     * Generates the list of file extensions supported by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function list_supported_extensions() {
        $video = array('mp4', 'm4v', 'f4v', 'mov', 'flv', 'webm', 'ogv');
        $audio = array('aac', 'm4a', 'f4a', 'mp3', 'ogg', 'oga');
        $streaming = array('m3u8');
        return array_merge($video, $audio, $streaming);
    }

    /**
     * Gets the ranking of this player.
     *
     * See parent class function for more details.
     *
     * @return int Rank
     */
    public function get_rank() {
        return 1;
    }

    /**
     * @return bool True if player is enabled
     */
    public function is_enabled() {
        global $CFG;
        $hostingmethod = get_config('filter_jwplayer', 'hostingmethod');
        $accounttoken = get_config('filter_jwplayer', 'accounttoken');
        if (($hostingmethod === 'cloud') && empty($accounttoken)) {
            // Cloud mode, but no account token is provided.
            return false;
        }
        $hostedjwplayerpath = $CFG->libdir . '/jwplayer/jwplayer.js';
        if (($hostingmethod === 'self') && !is_readable($hostedjwplayerpath)) {
            // Self-hosted mode, but no jwplayer files.
            return false;
        }
        return true;
    }
}
