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
 * Strings for component 'filter_jwplayer', language 'en'
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['appearanceconfig'] = 'Appearance';
$string['customskincss'] = 'Custom CSS skin name';
$string['customskincssdesc'] = 'Use a custom CSS skin.  Styles should be added to the site css as described in <a href="http://support.jwplayer.com/customer/portal/articles/1412123-building-jw-player-skins">JW Player website</a>.';
$string['downloadbutton'] = 'Download button';
$string['downloadbuttondesc'] = 'Add a button in the upper left corner of the player for downloading the video file.';
$string['defaultposter'] = 'Default poster';
$string['defaultposterdesc'] = 'Default poster image to use with videos.';
$string['displaystyle'] = 'Display Style';
$string['displaystyledesc'] = 'Default display style to use for videos if no video width specified.';
$string['displayfixed'] = 'Fixed Width';
$string['displayresponsive'] = 'Responsive';
$string['enabledevents'] = 'Events logging';
$string['enabledeventsdesc'] = 'Selected events will traced and recorded in activity logs (viewable in Reports section of the course). Make sure you select only required ones, as selecting more will increase logged data size. By default we trace only "play" and "pause" button clicks and that video has been viewed in full ("complete" event). For the details on events please refer to <a href="http://support.jwplayer.com/customer/portal/articles/1413089-javascript-api-reference">JW Player</a> website.';
$string['enabledextensions'] = 'Enabled extensions';
$string['enabledextensionsdesc'] = 'Only selected file extensions will be handled by the filter. Note HLS (.m3u8) and MPEG-Dash (.mpd) require a Premium, Enterprise or Ads licence for the player.';
$string['errornojwplayerinstalled'] = 'No JW Player files found in Moodle';
$string['errornolicensekey'] = 'Self-hosted player requires license key';
$string['eventmedia_audiotrack_switched'] = 'Media audio track switched';
$string['eventmedia_captions_switched'] = 'Media caption track switched';
$string['eventmedia_playback_completed'] = 'Media playback completed';
$string['eventmedia_playback_failed'] = 'Media playback failed';
$string['eventmedia_playback_launched'] = 'Media playback launched';
$string['eventmedia_playback_position_moved'] = 'Media playback position moved';
$string['eventmedia_playback_started'] = 'Media playback started';
$string['eventmedia_playback_stopped'] = 'Media playback stopped';
$string['eventmedia_qualitylevel_switched'] = 'Media quality level switched';
$string['filtername'] = 'JW Player multimedia filter';
$string['gaidstring'] = 'Play/Complete Action';
$string['gaidstringdesc'] = 'Action to record in Google Analytics for Play/Complete Events (e.g. file or title). For more information, see ga.idstring configuration option in documentaion on JW Player website.';
$string['galabel'] = 'Other Event Action';
$string['galabeldesc'] = 'Label to record in Google Analytics for player Events (e.g. file or title). For more information, see ga.label configuration option in documentaion on JW Player website.';
$string['googleanalytics'] = 'Google Analytics Integration';
$string['googleanalyticsconfig'] = 'Google Analytics';
$string['googleanalyticsconfigdesc'] = 'Please refer to documentation on the <a href="http://support.jwplayer.com/customer/portal/articles/1417179-integration-with-google-analytics">JW Player website</a> for more information on Google Analytics integration.';
$string['googleanalyticsdesc'] = 'Enable integration with Google Analytics.  Requires Google Analytics code to already be added to pages, you can add it using <a href="{$a}">Additional HTML</a> site setting.';
$string['hostingmethod'] = 'Player hosting method';
$string['hostingmethodcloud'] = 'Cloud-hosted';
$string['hostingmethoddesc'] = 'Cloud hosted JW Player (<a href="http://support.jwplayer.com/customer/en/portal/articles/1403726-jw-player-7-release-notes">version {$a}</a>) is used by default. Notice, that this cloud-hosted mode has nothing to do with <a href="http://www.jwplayer.com/products/jwplayer/cloud-video-player/">cloud video player</a> concept described on JW Player website. Cloud-hosting mode just loads JW Player libraries from their CDN hosting as opposed to specific Moodle directory like in self-hosted mode. If you prefer self-hosted option, make sure you downloaded JW Player 7 (Self-Hosted) zip archive from <a href="https://dashboard.jwplayer.com/#/players/downloads">License Keys & Downloads</a> page on JW Player website, unpacked it and placed content in /lib/jwplayer/ directory in Moodle.';
$string['hostingmethodself'] = 'Self-hosted';
$string['licensekey'] = 'Player license key';
$string['licensekeydesc'] = 'Player license key from <a href="https://dashboard.jwplayer.com/#/players/downloads">License Keys & Downloads</a> page on JW Player website. Specify here a key for "JW Player 7 (Self-Hosted)", even if you are using cloud-hosted hosting method in the settings above.';
$string['paideditionsconfig'] = 'Settings for paid editions of JW Player';
$string['paideditionsconfigdescr'] = 'Settings below only work with Pro, Premium and Ads editions. They have no effect for free edition.';
$string['standardskin'] = 'standard';
$string['supportrtmp'] = 'Support RTMP streams';
$string['supportrtmpdesc'] = 'If enabled, links that start with rtmp:// will be handled by filter, irrespective of whether its extension is enabled in the supported extensions setting.';
$string['useplayerskin'] = 'Use player skin';
$string['videodownloadbtntttext'] = 'Download Video';
