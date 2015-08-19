M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata, downloadbtn, logcontext) {
    var playerinstance = jwplayer(playerid);
    playerinstance.setup(setupdata);

    if (downloadbtn != undefined) {
        jwplayer(playerid).addButton(downloadbtn.img, downloadbtn.tttext, function() {
                // Grab the file that's currently playing.
                window.location.href = playerinstance.getPlaylistItem().file + '?forcedownload=true';
            }, "download");
    }

    var logevent = function(event) {
        var config = {
            method: 'POST',
            data:  {
                'sesskey' : M.cfg.sesskey,
                'event': JSON.stringify(event),
                'id': logcontext,
                'title': playerinstance.getPlaylistItem().title,
                'file': playerinstance.getPlaylistItem().file,
                'position': playerinstance.getPosition(),
                'bitrate': playerinstance.getCurrentQuality().bitrate,
            }
        };

        if (event.type == "play") {
            // for play events wait a short time before setting position so it picks up new position after seeks.
            setTimeout(function(){config.data.position = playerinstance.getPosition()},10);
        }

        if (event.type == "levelsChanged") {
            // pass information of quality levels for quality level events.
            config.data.qualitylevel = JSON.stringify(playerinstance.getQualityLevels());
        }
        if (event.type == "audioTrackChanged") {
            // pass information of audio tracks for audio track events.
            config.data.audiotracks = JSON.stringify(playerinstance.getAudioTracks());
        }
        if (event.type == "captionsChanged") {
            // pass information of captions for caption events.
            config.data.captions = JSON.stringify(playerinstance.getCaptionsList());
        }

        Y.io(M.cfg.wwwroot + '/filter/jwplayer/eventlogger.php', config);
    }

    playerinstance.on('playAttempt', logevent);
    playerinstance.on('play', logevent);
    playerinstance.on('buffer', logevent);
    playerinstance.on('pause', logevent);
    playerinstance.on('idle', logevent);
    playerinstance.on('complete', logevent);
    playerinstance.on('error', logevent);
    playerinstance.on('setupError', logevent);
    playerinstance.on('seek', logevent);
    playerinstance.on('visualQuality', logevent);
    playerinstance.on('levelsChanged', logevent);
    playerinstance.on('audioTrackChanged', logevent);
    playerinstance.on('captionsChanged', logevent);
};
