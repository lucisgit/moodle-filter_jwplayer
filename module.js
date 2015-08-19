M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata, downloadbtn) {
    var playerinstance = jwplayer(playerid);
    playerinstance.setup(setupdata);

    if (downloadbtn != undefined) {
        jwplayer(playerid).addButton(downloadbtn.img, downloadbtn.tttext, function() {
                // Grab the file that's currently playing.
                window.location.href = playerinstance.getPlaylistItem().file + '?forcedownload=true';
            }, "download");
    }
};
