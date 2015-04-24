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
            get_string('hostingmethoddesc', 'filter_jwplayer'),
            'cloud', $hostingmethodchoice));

    // Use HTTPS for cloud hosting.
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/securehosting',
            get_string('securehosting', 'filter_jwplayer'),
            get_string('securehostingdesc', 'filter_jwplayer'),
            0));

    // Account token.
    $settings->add(new filter_jwplayer_accounttoken_setting('filter_jwplayer/accounttoken',
            get_string('accounttoken', 'filter_jwplayer'),
            get_string('accounttokendesc', 'filter_jwplayer'),
            ''));

    // License key.
    $settings->add(new admin_setting_configtext('filter_jwplayer/licensekey',
            get_string('licensekey', 'filter_jwplayer'),
            get_string('licensekeydesc', 'filter_jwplayer'),
            ''));

    // Enabled extensions.
    $supportedextensions = $jwplayer->list_supported_extensions();
    $enabledextensionsmenu = array_combine($supportedextensions, $supportedextensions);
    $settings->add(new admin_setting_configmultiselect('filter_jwplayer/enabledextensions',
            get_string('enabledextensions', 'filter_jwplayer'),
            get_string('enabledextensionsdesc', 'filter_jwplayer'),
            $supportedextensions, $enabledextensionsmenu));

    // RTMP support.
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/supportrtmp',
            get_string('supportrtmp', 'filter_jwplayer'),
            get_string('supportrtmpdesc', 'filter_jwplayer'),
            0));

    // Download button.
    $settings->add(new admin_setting_configcheckbox('filter_jwplayer/downloadbutton',
            get_string('downloadbutton', 'filter_jwplayer'),
            get_string('downloadbuttondesc', 'filter_jwplayer'),
            0));

    $settings->add(new admin_setting_heading('paideditionsconfig',
            get_string('paideditionsconfig', 'filter_jwplayer'),
            get_string('paideditionsconfigdescr', 'filter_jwplayer')));

    // Skins.
    $skins = array('beelden', 'bekle', 'five', 'glow', 'roundster', 'six', 'stormtrooper', 'vapor');
    $skinoptions = array('' => get_string('standardskin', 'filter_jwplayer'));
    $skinoptions = array_merge($skinoptions, array_combine($skins, $skins));
    $settings->add(new admin_setting_configselect('filter_jwplayer/skin',
            get_string('useplayerskin', 'filter_jwplayer'), '', '', $skinoptions));
}

