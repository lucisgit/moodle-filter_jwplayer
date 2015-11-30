moodle-filter_jwplayer
======================

This a filter plugin that allows using JW Player 7 for playing HTML5 and
Flash content in Moodle 2.9 and higher<sup>1</sup>. The filter is designed
to achieve consistency of the player appearance in all major browsers and
mobile platforms. The player supports Flash fallback, which provides more
devices and formats coverage than either HTML5 or Flash can handle alone.
The filter also supports RTMP as well as HLS and MPEG-DASH
streaming<sup>2</sup>.

<sub><sup>1</sup> See plugin release notes for the list of supported Moodle versions.</sub>
<sub><sup>2</sup> HLS and MPEG-DASH support require paid license.</sub>

Installation
------------

JW Player installation procedure consists of three steps: player libraries
installation (optional), filter installation and filter configuration.

The filter does not include JW Player libraries due license limitations.
You are supposed to make sure that JW Player libraries are available in
your system, either by copying them in specified directory in Moodle
(self-hosted mode), or by configuring filter to use the cloud version of JW
Player hosted by JW Player CDN (more preferable option). In either case you
need to register on JW Player website http://www.jwplayer.com/sign-up/ and
accept terms of use.

### JW Player libraries installation (only for self-hosted player)

If you decide to use self-hosted player, you need to download player libraries from [License Keys &
Downloads](https://dashboard.jwplayer.com/#/players/downloads) area of
account dashboard, unpack, and place the content of unpacked `jwplayer-x.x.x`
directiry to `./lib/jwplayer/` directory in Moodle. This is where filter will
be looking for `jwplayer.js` file when you select self-hosted mode in the
filter settings.

### Filter installation

The filter installation is pretty strightforward. Filter files need to be
placed in `./filter/jwplayer` directory in Moodle, then you will need to go
through installation process as normal by loggining in as admin.

### Filter configuration

When the filter plugin installation is completed, the plugin configuration
page will be displayed (alternatively you may access it via Site
Administration -> Plugins -> Filters).

At a minimum, you need to specify player hosting method of your choise and
a license key. The license key is required for any hosting method,
irrespective whether you are using a free or premium version of JW Player.
The license key can be found on  [License Keys &
Downloads](https://dashboard.jwplayer.com/#/players/downloads) area of
account dashboard. You need to copy a licence key text field for "JW Player 7
(Self-Hosted)" and insert it in the "Player licence key" field in the
filter settings, even if you decided to use cloud-hosted player for your
installation<sup>3</sup>.

There are more settings, allowing you to configure media types for which
player will be used, player appearance, analytics.

Once the filter is configured, the final step would be to enable the filter
on Manage Filters  page in Site Administration area and move it above
"Multimedia plugins" to give it a higher priority.

<sub><sup>3</sup> Notice, that cloud-hosting method has nothing to do with
Cloud Player concept you will find on JW Player website, that allows
pre-configuring player and using it anywhere else. In our case, cloud is
similar to self-hosted, the difference is that libaries are hosted using
JW Player CDN rather than located locally in Moodle.</sub>

Upgrading from JW6
------------------

Upgrading from JW Player 6 (plugin versions 0.3 and earlier, 6-0.x) to JW
Player 7 (version 7-0.x) is only possible for Moodle 2.9 or higher (given it
is supported in plugin release notes). The upgrade procedure is pretty
standard. There are some major changes were
[introduced in JW 7](http://support.jwplayer.com/customer/en/portal/articles/2037989-migration-from-jw6-to-jw7),
that affected this plugin configuration:

* JW7 requires a license key, regardless of using a free or paid version.
JW6 license key does not work with JW7, you need a new one from [License
Keys & Downloads](https://dashboard.jwplayer.com/#/players/downloads)
area of account dashboard page.

* Cloud-hosted mode does not require an account token any more, but requires
self-hosted player license key instead. The concept has changed in a way
that JW Player 7 supplies pre-configured player for the given accout token.
Handling pre-configured player in this plugin is complicated and confusing.
It has been decided to use CDN hosted version of JW Player in
"cloud-hosting" mode. This made installation process more strightforward,
but now the same license key required for both cloud and self-hosted modes,
as cloud in this case is similar to self-hosted, but supplied using JW
Player CDN. For configuration details, see Filter configuration section
ablve.

* Custom skin can't be uploaded as XML file any more. JW Player 7 is using CSS
skinning, corresponding configuration settings have been added. Using skins
is now possible in free version.

* Google Analyics support has changed. It requires Google Analytics code to
already be added to pages (e.g. using Additional HTML site setting), at the
filter level user may adjust some customisation parameters. Google Analyics
is now supported in free version.

Usage
-----

Any multimedia content added to Moodle through HTML editor, either using
the URL or media embedding dialog, will be rendered and played using JW
Player if the format is supported and enabled in the filter configuration.
For more details on supported formats see [Media Format
Reference](http://support.jwplayer.com/customer/en/portal/articles/1403635-media-format-reference)
on JW Player website (ignore YouTube and RSS sections as they are not
supported by plugin).

Embedding multimedia content using File resource will not render using JW
Player filter due to the current Moodle limitations
([MDL-47495](https://tracker.moodle.org/browse/MDL-47495)).

Advanced use
------------

The filter plugin has extensive customisation features.

### Global HTML attributes

[Global HTML
attributes](https://developer.mozilla.org/en/docs/Web/HTML/Global_attributes)
in the player link will be applied to the rendered player outer span tag.
These attributes are:

_accesskey, class, contenteditable, contextmenu, dir, draggable, dropzone,
hidden, id, lang, spellcheck, style, tabindex, title, translate_

In addition, attribures that start with _data-_ (but not _data-jwplayer-_)
will be applied to player's outer span tag.

For example, `<a style="text-align: right;"
href="https://some.stream.org/functional.webm">functional.webm</a>` will
make player aligned to the right.

### JW Player specific attributes

HTML attributes in the player link that start with _data-jwplayer-_ prefix,
will be used as player configuration options. The possible options are:

_aspectratio, autostart, controls, height, mute, primary, repeat, width,
androidhls, hlslabels, description, mediaid_

For full description of each option, please refer to [configuration
reference](http://support.jwplayer.com/customer/portal/articles/1413113-configuration-options-reference)
on JW Player website. Options _file_ and _image_ are not relevant, thus
can't be applied.

For example, `<a data-jwplayer-autostart="true"
href="https://some.stream.org/functional.webm">functional.webm</a>` will
make player start playing video automatically on page load.

### Default player dimentions

The default player width is 400px (unless responsive mode is enabled in
filter configuration). This can be changed site-wide using relevant
constants, defined in Moodle `config.php`. For example, adding
`define('FILTER_JWPLAYER_VIDEO_WIDTH', 600);` will make your player default
width 600px (for non-responsive mode). The list of possible constants can
be found at the top part of
[lib.php](https://github.com/lucisgit/moodle-filter_jwplayer/blob/JW7/lib.php)
file.

### CDN JW Player version

While in self-hosted mode, choosing a different release is a matter of
downloading desired JW7 release and replacing files in ./lib/jwplayer,
cloud-hosted version is using a constant to determine the version to use in
JW Player CDN. Plugin is using the most recent stable version of JW Player
[available](http://support.jwplayer.com/customer/portal/articles/1403726-jw-player-7-release-notes)
at release time, however if different version is required, it can be
specified using `FILTER_JWPLAYER_CLOUD_VERSION` constant defined in Moodle
`config.php`, e.g. `define('FILTER_JWPLAYER_CLOUD_VERSION', '7.1.4');` will
make filter using player version 7.1.4.

When changing version, makes sure it exists in CDN by substituting version
number in the URL and testing its availability in the browser, e.g.
<https://ssl.p.jwpcdn.com/player/v/7.1.4/jwplayer.js>
