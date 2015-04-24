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

$string['accounttoken'] = 'Cloud-hosted player account token';
$string['accounttokendesc'] = 'Cloud-hosted player account token from account settings page on <a href="https://account.jwplayer.com/#/account">JW player website</a>. This is the file name from cloud-hosted player code, e.g. for script path http://jwpsrv.com/library/ABCDEF012345.js the corresponding account token that needs to be entered in the field above is ABCDEF012345. Not required if self-hosted player is used.';
$string['downloadbutton'] = 'Download button';
$string['downloadbuttondesc'] = 'Add a button in the upper left corner of the player for downloading the video file.';
$string['enabledextensions'] = 'Enabled extensions';
$string['enabledextensionsdesc'] = 'Only selected file extensions will be handled by the filter.';
$string['errornoaccounttoken'] = 'Cloud-hosted player requires account token';
$string['errornojwplayerinstalled'] = 'No JW player files found in Moodle';
$string['filtername'] = 'JW Player multimedia filter';
$string['hostingmethod'] = 'Player hosting method';
$string['hostingmethodcloud'] = 'Cloud-hosted';
$string['hostingmethoddesc'] = 'Cloud hosted JW player is used by default. If you prefer self-hosted option, make sure you downloaded JW player files and placed them in /lib/jwplayer/ directory in Moodle.';
$string['hostingmethodself'] = 'Self-hosted';
$string['licensekey'] = 'Self-hosted player license key';
$string['licensekeydesc'] = 'Self-hosted player license key from account settings page on <a href="https://account.jwplayer.com/#/account">JW player website</a>. Optional if free edition is used.';
$string['paideditionsconfig'] = 'Settings for paid editions of JW Player';
$string['paideditionsconfigdescr'] = 'Settings below only work with Pro, Premium and Ads editions. They have no effect for free edition.';
$string['securehosting'] = 'HTTPS for cloud-hosted player';
$string['securehostingdesc'] = 'Use https for cloud-hosted player.';
$string['standardskin'] = 'standard';
$string['supportrtmp'] = 'Support RTMP streams';
$string['supportrtmpdesc'] = 'If enabled, links that start with rtmp:// will be handled by filter, irrespective of whether its extension is enabled in the supported extensions setting.';
$string['useplayerskin'] = 'Use player skin';
$string['videodownloadbtntttext'] = 'Download Video';
