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
define(['jwplayer', 'core/config', 'core/yui', 'core/log'], function(jwplayer, mdlconfig, Y, log) {
    // Private functions and variables.
    /** @var {int} logcontext Moodle page context id. */
    var logcontext = null;

    /**
     * Event logging. Called when player event is triggered.
     *
     * @method logevent
     * @private
     * @param {Object[]} event JW Player event.
     */
    var logevent = function(event) {
        var playerinstance = this;
        var config = {
            method: 'POST',
            data:  {
                'sesskey' : mdlconfig.sesskey,
                'event': JSON.stringify(event),
                'id': logcontext,
                'title': playerinstance.getPlaylistItem().title,
                'file': playerinstance.getPlaylistItem().file,
                'position': playerinstance.getPosition(),
                'bitrate': playerinstance.getCurrentQuality().bitrate,
            },
            on: {
                failure: function(o) {
                    log.error(o);
		}
            }
        };

        if (event.type == "play") {
            // For play events wait a short time before setting position so it picks up new position after seeks.
            setTimeout(function(){config.data.position = playerinstance.getPosition();}, 10);
        }

        if (event.type == "levelsChanged") {
            // Pass information of quality levels for quality level events.
            config.data.qualitylevel = JSON.stringify(playerinstance.getQualityLevels());
        }
        if (event.type == "audioTrackChanged") {
            // Pass information of audio tracks for audio track events.
            config.data.audiotracks = JSON.stringify(playerinstance.getAudioTracks());
        }
        if (event.type == "captionsChanged") {
            // Pass information of captions for caption events.
            config.data.captions = JSON.stringify(playerinstance.getCaptionsList());
        }

        //log.debug(config.data);
        Y.io(mdlconfig.wwwroot + '/filter/jwplayer/eventlogger.php', config);
    };

    /**
     * Error logging. Called when player error event is triggered.
     *
     * @method logevent
     * @private
     * @param {Object[]} event JW Player event.
     */
    var logerror = function(event) {
        var errormsg = this.getPlaylistItem().title + ' ' + event.type + ': '+ event.message;
        log.error(errormsg);
    };

    return {
        /**
         * Initialise a player.
         *
         * @method init
         * @param {string} licensekey JW Player license key.
         */
        init: function (licensekey) {
            // Unfortinalely other loaded parts of JW player assume that jwplayer
            // lives in window.jwplayer, so we need this hack.
            window.jwplayer = window.jwplayer || jwplayer;
            if (licensekey) {
                window.jwplayer.key = licensekey;
            }
        },
        /**
         * Setup player instance.
         *
         * @method init
         * @param {Object[]} playersetup JW Player setup parameters.
         */
        setupPlayer: function (playersetup) {
            //log.debug(playersetup);
            logcontext = playersetup.logcontext;
            var playerinstance = jwplayer(playersetup.playerid);
            playerinstance.setup(playersetup.setupdata);

            // Add download button if required.
            if (typeof(playersetup.downloadbtn) !== 'undefined') {
                playerinstance.addButton(playersetup.downloadbtn.img, playersetup.downloadbtn.tttext, function() {
                    // Grab the file that's currently playing.
                    window.location.href = playerinstance.getPlaylistItem().file + '?forcedownload=true';
                }, "download");
            }

            // Track errors and log them in browser console.
            playerinstance.on('setupError', logerror);
            playerinstance.on('error', logerror);

            // Track required events and log them in Moodle.
            playersetup.logevents.forEach(function (eventname) {
                playerinstance.on(eventname, logevent);
            });
        }
    };
});