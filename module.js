M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata, downloadbtn) {
    jwplayer(playerid).setup(setupdata);

    if (downloadbtn != undefined) {
        jwplayer(playerid).addButton(downloadbtn.img, downloadbtn.tttext, function() {
                // Grab the file that's currently playing.
                window.location.href = jwplayer(playerid).getPlaylistItem().file + '?forcedownload=true';
            }, "download");
    }
};
