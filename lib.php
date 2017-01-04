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

// Current version of cloud-hosted JW Player.
if (!defined('FILTER_JWPLAYER_CLOUD_VERSION')) {
    // This is the only place where version needs to be changed in case of new
    // release avialability.
    define('FILTER_JWPLAYER_CLOUD_VERSION', '7.3.6');
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
     *                       image
     *                           use 'image' key with a moodle_url to an image as poster image
     *                           displayed before playback starts.
     *                       subtitles
     *                           use 'subtitles' key with an array of subtitle track files
     *                           in vtt or srt format indexed by label name.
     *                           Example: $options['subtitles']['English'] = http://example.com/english.vtt
     * @return string HTML code for embed
     */
    public function embed($urls, $name, $width, $height, $options) {
        global $PAGE, $CFG;
        // We do embedding only here. JW player setup is done in the filter.
        $output = '';

        $sources = array();
        $streams = array();
        $playersetupdata = array();

        foreach ($urls as $url) {
            // Add the details for this source.
            $source = array(
                'file' => urldecode($url->out(false)),
            );
            // Help to determine the type of mov.
            $ext = core_media::get_extension($url);
            if ($ext === 'mov') {
                $source['type'] = 'mp4';
            }

            if ($ext === 'mpd') {
                // Dash variable needs to be set if we have a dash stream_bucket_append
                $playersetupdata['dash'] = true;
                $streams[] = $source;
            } else if ($url->get_scheme() === 'rtmp' || $ext === 'm3u8' || $ext === 'smil') {
                // For RTMP, HLS and Dynamic RTMP we set rendering mode to Flash to
                // ensure streams play is possible even when mp4 fallbacks are given.
                $playersetupdata['primary'] = 'flash';
                $streams[] = $source;
            } else {
                $sources[] = $source;
            }
        }

        // Make sure that stream URLs are at the start of the list.
        $sources = array_merge($streams, $sources);

        if (count($sources) > 0) {
            $playerid = 'filter_jwplayer_media_' . html_writer::random_id();

            // Process data-jwplayer attributes.
            if (!empty($options['htmlattributes'])) {
                foreach ($options['htmlattributes'] as $attrib => $atval) {
                    if (strpos($attrib, 'data-jwplayer-') === 0) {
                        // Treat attributes starting data-jwplayer as options.
                        $opt = preg_replace('~^data-jwplayer-~', '', $attrib);
                        $atval = trim((string) $atval);
                        if (strpos($atval, ': ') || strpos($atval, '; ') || strpos($atval, ', ')) {
                            // If attribute contains any of :;, it needs to be split to an array.
                            $atvalarray = preg_split('~[,;] ~', $atval);
                            $newatval = array();
                            foreach ($atvalarray as $dataval) {
                                $newdata = explode(': ', $dataval, 2);
                                if (count($newdata) > 1) {
                                    $newdata[1] = trim($newdata[1]);
                                    if (filter_var($newdata[1], FILTER_VALIDATE_URL)) {
                                        // If value is a URL convert to moodle_url.
                                        $newdata[1] = new moodle_url($newdata[1]);
                                    }
                                    $newatval[trim($newdata[0])] = $newdata[1];
                                } else {
                                    $newdata[0] = trim($newdata[0]);
                                    if (filter_var($newdata[0], FILTER_VALIDATE_URL)) {
                                        // If value is a URL convert to moodle_url.
                                        $newdata[0] = new moodle_url($newdata[0]);
                                    }
                                    $newatval[] = $newdata[0];
                                }
                            }
                            $atval = $newatval;
                        } else if (filter_var($atval, FILTER_VALIDATE_URL)) {
                            // If value is a URL convert to moodle_url.
                            $atval = new moodle_url($atval);
                        }
                        $options[$opt] = $atval;
                    } else {
                        // Pass any other global HTML attributes to the player span tag.
                        $globalhtmlattributes = array(
                            'accesskey',
                            'class',
                            'contenteditable',
                            'contextmenu',
                            'dir',
                            'draggable',
                            'dropzone',
                            'hidden',
                            'id',
                            'lang',
                            'spellcheck',
                            'style',
                            'tabindex',
                            'title',
                            'translate'
                        );
                        if (in_array($attrib, $globalhtmlattributes) || strpos($attrib, 'data-' === 0)) {
                            $newattributes[$attrib] = $atval;
                        }
                    }
                }
            }

            // Set up playlist.
            $playlistitem = array('sources' => $sources);

            // Set Title from title attribute of a tag if it has one if not default to filename.
            if (isset($options['htmlattributes']['title'])) {
                $playlistitem['title'] = (string) $options['htmlattributes']['title'];
            } else {
                $playlistitem['title'] = $this->get_name('', $urls);
            }

            // Setup video description.
            if (isset($options['description'])) {
                $playlistitem['description'] = $options['description'];
            }

            // Setup video mediaid and use this for the playerid.
            if (isset($options['mediaid']) && strlen(trim($options['mediaid']))) {
                $playlistitem['mediaid'] = $options['mediaid'];
                $playerid = 'filter_jwplayer_media_' . preg_replace('/\s+/', '', $options['mediaid']);
            }

            // Setup poster image.
            if (isset($options['image']) && $options['image'] instanceof moodle_url) {
                $playlistitem['image'] = urldecode($options['image']->out(false));
            } else if ($poster = get_config('filter_jwplayer', 'defaultposter')) {
                $syscontext = context_system::instance();
                $playlistitem['image'] = moodle_url::make_pluginfile_url($syscontext->id, 'filter_jwplayer', 'defaultposter', null, null, $poster)->out(true);
            }

            // Setup subtitle tracks.
            if (isset($options['subtitles'])) {
                $tracks = array();
                foreach ($options['subtitles'] as $label => $subtitlefileurl) {
                    if ($subtitlefileurl instanceof moodle_url) {
                        $tracks[] = array(
                            'file' => urldecode($subtitlefileurl->out(false)),
                            'label' => $label,
                        );
                    }
                }
                $playlistitem['tracks'] = $tracks;
            }

            $playersetupdata['playlist'] = array($playlistitem);

            // If width and/or height are set in the options override those from URL or defaults.
            if (isset($options['width'])) {
                $width = $options['width'];
            }
            if (isset($options['height'])) {
                $height = $options['height'];
            }

            // If we are dealing with audio, show just the control bar.
            if (mimeinfo('string', $sources[0]['file']) === 'audio') {
                $width = FILTER_JWPLAYER_AUDIO_WIDTH;
                $height = FILTER_JWPLAYER_AUDIO_HEIGHT;
            }

            // If width is not provided, use default.
            if (!$width) {
                // Use responsive width if choosen in settings otherwise default to fixed width.
                if (get_config('filter_jwplayer', 'displaystyle') === 'responsive') {
                    $width = FILTER_JWPLAYER_VIDEO_WIDTH_RESPONSIVE;
                } else {
                    $width = FILTER_JWPLAYER_VIDEO_WIDTH;
                }
            }

            if (is_numeric($width)) {
                $width = round($width);
            }
            $playersetupdata['width'] = $width;

            // If width is a percentage surrounding span needs to have its width set so it does not default to 0px.
            $outerspanargs = array('class' => 'filter_jwplayer_playerblock');
            if (!is_numeric($width)) {
                $outerspanargs['style'] = 'width: '.$width.';';
                $width = '100%';  // As the outer span in now at the required width, we set the width of the player to 100%.
            }

            // Automatically set the height unless it is specified.
            if ($height) {
                if (is_numeric($height)) {
                    $playersetupdata['height'] = $height;
                } else if (is_numeric($width)) {
                    // If width is numeric and height is percentage, calculate height from width.
                    $playersetupdata['height'] = round($width * floatval($height) / 100);
                } else {
                    // If width is also percentage, then set aspect ratio.
                    $playersetupdata['aspectratio'] = "100:".floatval($height);
                }
            } else {
                if (is_numeric($width)) {
                    // If width is numeric calculate height from default aspect ratio.
                    $playersetupdata['height'] = round($width * FILTER_JWPLAYER_VIDEO_ASPECTRATIO_H / FILTER_JWPLAYER_VIDEO_ASPECTRATIO_W);
                } else if (isset($options['aspectratio'])) {
                    // Responsive videos need aspect ratio set to automatically set height, if this is set in $options use that.
                    $playersetupdata['aspectratio'] = $options['aspectratio'];
                } else {
                    // Use default aspectration.
                    $playersetupdata['aspectratio'] = FILTER_JWPLAYER_VIDEO_ASPECTRATIO_W.":".FILTER_JWPLAYER_VIDEO_ASPECTRATIO_H;
                }
            }

            // Set additional player options: autostart, mute, controls, repeat, hlslabels, androidhls, primary.
            if (isset($options['autostart'])) {
                $playersetupdata['autostart'] = $options['autostart'];
            }
            if (isset($options['mute'])) {
                $playersetupdata['mute'] = $options['mute'];
            }
            if (isset($options['controls'])) {
                $playersetupdata['controls'] = $options['controls'];
            }
            if (isset($options['repeat'])) {
                $playersetupdata['repeat'] = $options['repeat'];
            }
            if (isset($options['hlslabels']) && is_array($options['hlslabels'])) {
                $playersetupdata['hlslabels'] = $options['hlslabels'];
            }
            if (isset($options['androidhls'])) {
                $playersetupdata['androidhls'] = $options['androidhls'];
            }
            if (isset($options['primary'])) {
                // if primary is set in $options then this will override all defaults including those for streams set above.
                $playersetupdata['primary'] = $options['primary'];
            }

            // Load skin.
            if ($customskincss = get_config('filter_jwplayer', 'customskincss')) {
                $playersetupdata['skin'] = $customskincss;
            } else if ($skin = get_config('filter_jwplayer', 'skin')) {
                $playersetupdata['skin'] = $skin;
            }

            // Set Google Analytics settings if enabled.
            if (get_config('filter_jwplayer', 'googleanalytics')) {
                if (isset($options['gaidstring'])) {
                    $gaidstring = $options['gaidstring'];
                } else {
                    $gaidstring = get_config('filter_jwplayer', 'gaidstring');
                }

                if (isset($options['galabel'])) {
                    $galabel = $options['galabel'];
                } else {
                    $galabel = get_config('filter_jwplayer', 'galabel');
                }

                $playersetupdata['ga'] = array(
                    'idstring' => $gaidstring,
                    'label' => $galabel
                );
            }

            $playersetup = new stdClass();
            $playersetup->playerid = $playerid;
            $playersetup->setupdata = $playersetupdata;

            // Add download button if required and supported.
            if (get_config('filter_jwplayer', 'downloadbutton') && !count($streams)) {
                $playersetup->downloadbtn = array(
                    'img' => $CFG->wwwroot.'/filter/jwplayer/img/download.png',
                    'tttext' => get_string('videodownloadbtntttext', 'filter_jwplayer'),
                );
            }

            // Pass the page context variables for logging
            $playersetup->logcontext = $PAGE->context->id;
            $playersetup->logevents = $this->get_supported_events();

            // Set required class for player span tag.
            if (isset($options['htmlattributes']['class'])) {
                $newattributes['class'] .= ' filter_jwplayer_media';
            } else {
                $newattributes['class'] = 'filter_jwplayer_media';
            }

            // Set up the player.
            $PAGE->requires->js_call_amd('filter_jwplayer/jwplayer', 'setupPlayer', array($playersetup));
            $playerdiv = html_writer::tag('span', $this->get_name('', $urls), array('id' => $playerid));
            $outerspan = html_writer::tag('span', $playerdiv, $outerspanargs);
            $output .= html_writer::tag('span', $outerspan, $newattributes);
        }

        return $output;
    }

    /**
     * Gets the list of file extensions supported (enabled) by this media player.
     *
     * @return array Array of strings (extension not including dot e.g. 'mp3')
     */
    public function get_supported_extensions() {
        return explode(',', get_config('filter_jwplayer', 'enabledextensions'));
    }

    /**
     * Gets the list of events supported (enabled) by this media player.
     *
     * @return array Array of strings
     */
    public function get_supported_events() {
        return explode(',', get_config('filter_jwplayer', 'enabledevents'));
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
        $streaming = array('m3u8', 'smil', 'mpd');
        return array_merge($video, $audio, $streaming);
    }

    /**
     * Generates the list of supported events that can be logged.
     *
     * @return array Array of strings
     */
    public function list_supported_events() {
        $events = array(
            'playAttempt',
            'play',
            'buffer',
            'pause',
            'idle',
            'complete',
            'error',
            'setupError',
            'seek',
            'visualQuality',
            'levelsChanged',
            'audioTrackChanged',
            'captionsChanged',
        );
        return $events;
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
     * Checks if player is enabled.
     *
     * @return bool True if player is enabled
     */
    public function is_enabled() {
        global $CFG;
        $hostingmethod = get_config('filter_jwplayer', 'hostingmethod');
        $licensekey = get_config('filter_jwplayer', 'licensekey');
        if (($hostingmethod === 'cloud') && empty($licensekey)) {
            // Cloud mode, but no license key.
            return false;
        }
        $hostedjwplayerpath = $CFG->libdir . '/jwplayer/jwplayer.js';
        if (($hostingmethod === 'self') && (!is_readable($hostedjwplayerpath) || empty($licensekey))) {
            // Self-hosted mode, but no jwplayer files and/or no license.
            return false;
        }
        return true;
    }
}
