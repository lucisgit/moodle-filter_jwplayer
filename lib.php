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
 * Effectively, this is a copy of core_media::split_alternatives that does
 * not get confused with rtmp:// scheme.
 *
 * Given a string containing multiple URLs separated by #, this will split
 * it into an array of moodle_url objects suitable for using when calling
 * embed_alternatives.
 *
 * Note that the input string should NOT be html-escaped (i.e. if it comes
 * from html, call html_entity_decode first).
 *
 * @param string $combinedurl String of 1 or more alternatives separated by #
 * @param int $width Output variable: width (will be set to 0 if not specified)
 * @param int $height Output variable: height (0 if not specified)
 * @return array Array of 1 or more moodle_url objects
 */
function filter_jwplayer_split_alternatives($combinedurl, &$width, &$height) {
    $urls = explode('#', $combinedurl);
    $width = 0;
    $height = 0;
    $returnurls = array();

    foreach ($urls as $url) {
        $matches = null;

        // You can specify the size as a separate part of the array like
        // #d=640x480 without actually including a url in it.
        if (preg_match('/^d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) {
            $width  = $matches[1];
            $height = $matches[2];
            continue;
        }

        // Can also include the ?d= as part of one of the URLs (if you use
        // more than one they will be ignored except the last).
        if (preg_match('/\?d=([\d]{1,4})x([\d]{1,4})$/i', $url, $matches)) {
            $width  = $matches[1];
            $height = $matches[2];

            // Trim from URL.
            $url = str_replace($matches[0], '', $url);
        }

        // Clean up url.
        $url = filter_var($url, FILTER_VALIDATE_URL);
        if (empty($url)) {
            continue;
        }

        // Turn it into moodle_url object.
        $returnurls[] = new moodle_url($url);
    }

    return $returnurls;
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
     * @param array $urls Moodle URLs of media files
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
        $playersetupdata = array();

        foreach ($urls as $url) {
            // Add the details for this source.
            $source = array(
                'file' => urldecode($url),
            );
            // Help to determine the type of mov.
            if (strtolower(pathinfo($url, PATHINFO_EXTENSION)) === 'mov') {
                $source['type'] = 'mp4';
            }

            if ($url->get_scheme() === 'rtmp') {
                // For RTMP we set rendering mode to Flash and making sure
                // URL is the first in the list.
                $playersetupdata['primary'] = 'flash';
                array_unshift($sources, $source);
            } else {
                $sources[] = $source;
            }
        }

        if (count($sources) > 0) {
            $playerid = 'filter_jwplayer_media_' . html_writer::random_id();

            $playersetupdata['title'] = $this->get_name('', $urls);
            $playersetupdata['playlist'] = array(array('sources' => $sources));
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
     * Lists keywords that must be included in a url that can be embedded with
     * this media player.
     *
     * @return array Array of keywords to add to the embeddable markers list
     */
    public function get_embeddable_markers() {
        $markers = parent::get_embeddable_markers();
        // Add RTMP support if enabled.
        if (get_config('filter_jwplayer', 'supportrtmp')) {
            $markers[] = 'rtmp://';
        }
        return $markers;
    }

    /**
     * Generates the list of file extensions supported by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function list_supported_extensions() {
        $video = array('mp4', 'm4v', 'f4v', 'mov', 'flv', 'webm', 'ogv');
        $audio = array('aac', 'm4a', 'f4a', 'mp3', 'ogg', 'oga');
        $streaming = array('m3u8', 'smil');
        return array_merge($video, $audio, $streaming);
    }

    /**
     * Given a list of URLs, returns a reduced array containing only those URLs
     * which are supported by this player. (Empty if none.)
     * @param array $urls Array of moodle_url
     * @param array $options Options (same as will be passed to embed)
     * @return array Array of supported moodle_url
     */
    public function list_supported_urls(array $urls, array $options = array()) {
        $extensions = $this->get_supported_extensions();
        $result = array();
        foreach ($urls as $url) {
            // If RTMP support is disabled, skip the URL.
            if (!get_config('filter_jwplayer', 'supportrtmp') && ($url->get_scheme() === 'rtmp')) {
                continue;
            }
            // If RTMP support is enabled, URL is supported.
            if (get_config('filter_jwplayer', 'supportrtmp') && ($url->get_scheme() === 'rtmp')) {
                $result[] = $url;
                continue;
            }
            if (in_array(core_media::get_extension($url), $extensions)) {
                // URL is matching one of enabled extensions.
                $result[] = $url;
            }
        }
        return $result;
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
