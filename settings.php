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
 *  JW Player media filtering settings.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once(__DIR__ . '/lib.php');
    require_once(__DIR__ . '/adminlib.php');
    $jwplayer = new filter_jwplayer_media();

    // Hosting method.
    $hostingmethodchoice = array(
        'cloud' => get_string('hostingmethodcloud', 'filter_jwplayer'),
        'self' => get_string('hostingmethodself', 'filter_jwplayer'),
    );
    $settings->add(new filter_jwplayer_hostingmethod_setting('filter_jwplayer/hostingmethod',
            get_string('hostingmethod', 'filter_jwplayer'),
            get_string('hostingmethoddesc', 'filter_jwplayer', FILTER_JWPLAYER_CLOUD_VERSION),
            'cloud', $hostingmethodchoice));

    // License key.
    $settings->add(new filter_jwplayer_license_setting('filter_jwplayer/licensekey',
            get_string('licensekey', 'filter_jwplayer'),
            get_string('licensekeydesc', 'filter_jwplayer'),
            ''));

    // Enabled extensions.
    $supportedextensions = $jwplayer->list_supported_extensions();
    $enabledextensionsmenu = array_combine($supportedextensions, $supportedextensions);
    array_splice($supportedextensions, array_search('mpd', $supportedextensions), 1);  // disable mpeg-dash as it requires premium licence or higher.
    array_splice($supportedextensions, array_search('m3u8', $supportedextensions), 1);  // disable HLS by default as it needs a Premium licence
    $settings->add(new admin_setting_configmultiselect('filter_jwplayer/enabledextensions',
            get_string('enabledextensions', 'filter_jwplayer'),
            get_string('enabledextensionsdesc', 'filter_jwplayer'),
            $supportedextensions, $enabledextensionsmenu));

    // RTMP support.
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/supportrtmp',
            get_string('supportrtmp', 'filter_jwplayer'),
            get_string('supportrtmpdesc', 'filter_jwplayer'),
            0));

    // Enabled events to log.
    $supportedevents = $jwplayer->list_supported_events();
    $supportedeventsmenu = array_combine($supportedevents, $supportedevents);
    $settings->add(new admin_setting_configmultiselect('filter_jwplayer/enabledevents',
            get_string('enabledevents', 'filter_jwplayer'),
            get_string('enabledeventsdesc', 'filter_jwplayer'),
            array('play', 'pause', 'complete'), $supportedeventsmenu));

    // Appearance related settings.
    $settings->add(new admin_setting_heading('appearanceconfig',
            get_string('appearanceconfig', 'filter_jwplayer'), ''));

    // Default Poster Image.
    $settings->add(new admin_setting_configstoredfile('filter_jwplayer/defaultposter',
            get_string('defaultposter', 'filter_jwplayer'),
            get_string('defaultposterdesc', 'filter_jwplayer'),
            'defaultposter',
            0,
            array(
                'accepted_types' => array('image')
            )));

    // Download button.
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/downloadbutton',
            get_string('downloadbutton', 'filter_jwplayer'),
            get_string('downloadbuttondesc', 'filter_jwplayer'),
            0));

    // Display Style (Fixed Width or Responsive).
    $displaystylechoice = array(
        'fixed' => get_string('displayfixed', 'filter_jwplayer'),
        'responsive' => get_string('displayresponsive', 'filter_jwplayer'),
    );
    $settings->add(new admin_setting_configselect('filter_jwplayer/displaystyle',
            get_string('displaystyle', 'filter_jwplayer'),
            get_string('displaystyledesc', 'filter_jwplayer'),
            'fixed', $displaystylechoice));

    // Skins.
    $skins = array('beelden', 'bekle', 'five', 'glow', 'roundster', 'six', 'stormtrooper', 'vapor');
    $skinoptions = array('' => get_string('standardskin', 'filter_jwplayer'));
    $skinoptions = array_merge($skinoptions, array_combine($skins, $skins));
    $settings->add(new admin_setting_configselect('filter_jwplayer/skin',
            get_string('useplayerskin', 'filter_jwplayer'), '', '', $skinoptions));

    // Custom skin.
    $settings->add(new admin_setting_configtext('filter_jwplayer/customskincss',
            get_string('customskincss', 'filter_jwplayer'),
            get_string('customskincssdesc', 'filter_jwplayer'),
            ''));

    // Google Analytics settings.
    $settings->add(new admin_setting_heading('googleanalyticsconfig',
            get_string('googleanalyticsconfig', 'filter_jwplayer'),
            get_string('googleanalyticsconfigdesc', 'filter_jwplayer')));

    $addhtml = new moodle_url('/admin/settings.php', array('section' => 'additionalhtml'));
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/googleanalytics',
            get_string('googleanalytics', 'filter_jwplayer'),
            get_string('googleanalyticsdesc', 'filter_jwplayer', $addhtml->out()),
            0));

    $settings->add(new admin_setting_configtext('filter_jwplayer/gaidstring',
            get_string('gaidstring', 'filter_jwplayer'),
            get_string('gaidstringdesc', 'filter_jwplayer'),
            'file'));

    $settings->add(new admin_setting_configtext('filter_jwplayer/galabel',
            get_string('galabel', 'filter_jwplayer'),
            get_string('galabeldesc', 'filter_jwplayer'),
            'file'));
}