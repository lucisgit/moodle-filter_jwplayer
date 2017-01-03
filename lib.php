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

// Current version of cloud-hosted JW Player.
if (!defined('FILTER_JWPLAYER_CLOUD_VERSION')) {
    // This is the only place where version needs to be changed in case of new
    // release avialability.
    define('FILTER_JWPLAYER_CLOUD_VERSION', '7.8.6');
}

// Size and aspect ratio related defaults.
if (!defined('FILTER_JWPLAYER_VIDEO_WIDTH')) {
    // Default video width if no width is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_VIDEO_WIDTH', 400);
}
if (!defined('FILTER_JWPLAYER_VIDEO_WIDTH_RESPONSIVE')) {
    // Default video width if no width is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_VIDEO_WIDTH_RESPONSIVE', '100%');
}
if (!defined('FILTER_JWPLAYER_VIDEO_ASPECTRATIO_W')) {
    // Default video aspect ratio for responsive mode if no height is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_VIDEO_ASPECTRATIO_W', 16);
}
if (!defined('FILTER_JWPLAYER_VIDEO_ASPECTRATIO_H')) {
    // Default video aspect ratio for responsive mode if no height is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_VIDEO_ASPECTRATIO_H', 9);
}
if (!defined('FILTER_JWPLAYER_AUDIO_WIDTH')) {
    // Default audio width if no width is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_AUDIO_WIDTH', 400);
}
if (!defined('FILTER_JWPLAYER_AUDIO_HEIGHT')) {
    // Default audio heigth if no height is specified.
    // May be defined in config.php if required.
    define('FILTER_JWPLAYER_AUDIO_HEIGHT', 30);
}

/**
 * Effectively, this is a copy of core_media_manager->split_alternatives that does
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
        if (preg_match('/^d=([\d]{1,4}\.?[\d]*%?)x([\d]{1,4}\.?[\d]*%?)$/i', $url, $matches)) {
            $width  = $matches[1];
            $height = $matches[2];
            continue;
        } else if (preg_match('/^d=([\d]{1,4}\.?[\d]*%?)$/i', $url, $matches)) {
            $width = $matches[1];
            continue;
        }

        // Can also include the ?d= as part of one of the URLs (if you use
        // more than one they will be ignored except the last).
        if (preg_match('/\?d=([\d]{1,4}\.?[\d]*%?)x([\d]{1,4}\.?[\d]*%?)$/i', $url, $matches)) {
            $width  = $matches[1];
            $height = $matches[2];

            // Trim from URL.
            $url = str_replace($matches[0], '', $url);
        } else if (preg_match('/\?d=([\d]{1,4}\.?[\d]*%?)$/i', $url, $matches)) {
            $width = $matches[1];
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
 * Setup filter requirements.
 *
 * @param moodle_page $page the page we are going to add requirements to.
 * @return void
 */
function filter_jwplayer_setup($page) {
    global $CFG;

    // It is sufficient to load jwplayer library just once.
    static $runonce;
    if (!isset($runonce)) {
        $runonce = true;
    } else {
        return;
    }

    $hostingmethod = get_config('filter_jwplayer', 'hostingmethod');
    if ($hostingmethod === 'cloud') {
        // Well, this is not really a "cloud" version any more, we are just
        // using jwplayer libraries hosted on JW Player CDN.
        $jwplayer = new moodle_url('https://ssl.p.jwpcdn.com/player/v/' . FILTER_JWPLAYER_CLOUD_VERSION . '/jwplayer');
    } else if ($hostingmethod === 'self') {
        // For self-hosted option, we are looking for player files presence in
        // ./lib/jwplayer/ directory.
        $jwplayer = new moodle_url($CFG->httpswwwroot.'/lib/jwplayer/jwplayer');
    }
    // We need to define jwplayer, since jwplayer doesn't
    // define a module for require.js.
    $requirejs = 'require.config({ paths: {\'jwplayer\': \'' . $jwplayer->out() . '\'}})';
    $page->requires->js_amd_inline($requirejs);

    // Set player license key.
    $licensekey = get_config('filter_jwplayer', 'licensekey');
    $licensejs = 'require.config({ config: {\'filter_jwplayer/jwplayer\': { licensekey: \'' . $licensekey . '\'}}})';
    $page->requires->js_amd_inline($licensejs);
}

function filter_jwplayer_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'defaultposter' && $filearea !== 'playerskin') {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true);

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'filter_jwplayer', $filearea, 0, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}