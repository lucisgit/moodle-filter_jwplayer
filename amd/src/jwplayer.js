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
 * JW Player module.
 *
 * @module     filter_jwplayer/jwplayer
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2015 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jwplayer'], function(jwplayer) {
    return {
        init: function (licensekey) {
            // Unfortinalely other loaded parts of JW player assume that jwplayer
            // lives in window.jwplayer, so we need this hack.
            window.jwplayer = window.jwplayer || jwplayer;
            if (licensekey) {
                window.jwplayer.key = licensekey;
            }
        },
        setupPlayer: function (playersetup) {
            var playerinstance = jwplayer(playersetup.playerid);
            playerinstance.setup(playersetup.setupdata);

            if (playersetup.downloadbtn != undefined) {
                playerinstance.addButton(playersetup.downloadbtn.img, playersetup.downloadbtn.tttext, function() {
                    // Grab the file that's currently playing.
                    window.location.href = playerinstance.getPlaylistItem().file + '?forcedownload=true';
                }, "download");
            }
        }
    };
});