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
 *  JW Player event logger.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Owen Barritt, Wine & Spirit Education Trust
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
require_once(dirname(dirname(__DIR__)).'/config.php');

require_login(null, false, null, false, true);
require_sesskey();

// Required Parameters.
$contextid = required_param('id', PARAM_INT);
$eventjson = required_param('event', PARAM_TEXT);
$event = json_decode($eventjson);

// Optional Information Parameters.
$title = optional_param('title', null, PARAM_TEXT);
$file = optional_param('file', null, PARAM_TEXT);
$bitrate = optional_param('bitrate', null, PARAM_TEXT);
$position = optional_param('position', null, PARAM_TEXT);
$quality = json_decode(optional_param('qualitylevel', null, PARAM_TEXT));
$audiotracks = json_decode(optional_param('audiotracks', null, PARAM_TEXT));
$captions = json_decode(optional_param('captions', null, PARAM_TEXT));

// Store additional parameters.
$other = array(
    'event' => $eventjson
);
if ($title) {
    $other['title'] = $title;
}
if ($file) {
    $other['file'] = $file;
}
if ($bitrate) {
    $other['bitrate'] = $bitrate;
}
if ($position) {
    $other['position'] = $position;
}


if ($event->{'type'} == 'setupError' || $event->{'type'} == 'error') {
    // setupError and error events.
    if (isset($event->{'message'})) {
        $other['errmessage'] = $event->{'message'};
    }

    $logevent = \filter_jwplayer\event\media_playback_failed::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;

} else if ($event->{'type'} == 'playAttempt') {
    // playAttempt events.

    $logevent = \filter_jwplayer\event\media_playback_launched::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'complete') {
    // complete events.

    $logevent = \filter_jwplayer\event\media_playback_completed::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'play') {
    // play events.

    $logevent = \filter_jwplayer\event\media_playback_started::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'pause' || $event->{'type'} == 'buffer' || $event->{'type'} == 'idle') {
    // pause and buffer events.
    $other['reason'] = $event->{'type'};

    $logevent = \filter_jwplayer\event\media_playback_stopped::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'seek') {
    // seek events.
    $other{'offset'} = $event->{'offset'};

    $logevent = \filter_jwplayer\event\media_playback_position_moved::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'visualQuality' || $event->{'type'} == 'levelsChanged') {
    // visualQuality events.
    if ($event->{'type'} == 'visualQuality') {
        $other{'hls'} = true;
        $other{'level'} = $event->{'level'}->{'label'};
        $other{'mode'} = $event->{'mode'};
        $other{'reason'} = $event->{'reason'};
        $other{'width'} = $event->{'level'}->{'width'};
        $other{'height'} = $event->{'level'}->{'height'};
        $other{'bitrate'} = $event->{'level'}->{'bitrate'};
    } else {
        $other{'hls'} = false;
        $other{'level'} = $quality[$event->{'currentQuality'}]->{'label'};
        $other{'mode'} = '';
        $other{'reason'} = '';
        $other{'width'} = '';
        $other{'height'} = '';
        $other{'bitrate'} = '';
    }

    $logevent = \filter_jwplayer\event\media_qualitylevel_switched::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'audioTrackChanged') {
    $other{'audiotrack'} = $audiotracks[$event->{'currentTrack'}]->{'name'};

    $logevent = \filter_jwplayer\event\media_audiotrack_switched::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else if ($event->{'type'} == 'captionsChanged') {
    $other{'captions'} = $captions[$event->{'track'}]->{'label'};

    $logevent = \filter_jwplayer\event\media_captions_switched::create(array(
        'contextid' => $contextid,
        'other' => $other
    ));
    $logevent->trigger();

    $result = true;
} else {
    // unknown event, so return false.
    $result = false;
}

echo $OUTPUT->header();

echo json_encode(array('result' => $result));