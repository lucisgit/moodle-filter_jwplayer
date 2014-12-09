M.filter_jwplayer = {};

M.filter_jwplayer.init = function(Y, playerid, setupdata) {
    jwplayer(playerid).setup(setupdata);
};

M.filter_jwplayer.addButton = function(Y, playerid, img, tttext) {
	jwplayer(playerid).addButton(
		//This portion is what designates the graphic used for the button
		img,
		//This portion determines the text that appears as a tooltip
		tttext, 
		//This portion designates the functionality of the button itself
		function() {
			//With the below code, we're grabbing the file that's currently playing
			window.location.href = jwplayer().getPlaylistItem()['file'] + '?forcedownload=true';
		},
		//And finally, here we set the unique ID of the button itself.
		"download"
	);
};
