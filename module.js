M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata, buttons) {
    jwplayer(playerid).setup(setupdata);

    function btncallback(i) {
        return function() {
            M.filter_jwplayer.execute_namespaced_function(buttons[i].callback, [playerid]);
        };
    }

    for (var i = 0; i < buttons.length; i++) {
        jwplayer(playerid).addButton(buttons[i].img, buttons[i].text, btncallback(i), buttons[i].id);
    }
};

M.filter_jwplayer.download = function(playerid) {
    window.location.href = jwplayer(playerid).getPlaylistItem().file + '?forcedownload=true';
};

M.filter_jwplayer.execute_namespaced_function = function(namespacedfunctionname, args) {
    var namespaces = namespacedfunctionname.split(".");
    var fn = namespaces.pop();
    var context = window;
    for (var i = 0; i < namespaces.length; i++) {
        context = context[namespaces[i]];
    }
    context[fn].apply(context, args);
};
