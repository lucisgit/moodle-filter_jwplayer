M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata) {
    jwplayer(playerid).setup(setupdata);
};

M.filter_jwplayer.add_button = function(Y, playerid, img, tttext) {
    jwplayer(playerid).addButton(img, tttext, function() {
            // Grab the file that's currently playing.
            window.location.href = jwplayer(playerid).getPlaylistItem().file + '?forcedownload=true';
            }, "download");
};
