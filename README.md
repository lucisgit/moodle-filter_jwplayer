moodle-filter_jwplayer
======================

This is Moodle filter that allows using JW Player for playing HTML5 and flash
content. The filter is designed to achieve consistency of the player appearance
in all major browsers and mobile platforms. The player supports flash fallback,
providing more devices and formats coverage than either HTML5 or flash can
handle alone.

Installation
------------

There are three stages of installation that include JW player installation
(optional), filter installation and filter configuration.

The filter does not include JW Player libraries due license limitations. You
are supposed to make sure that JW Player libraries are available either by
installing them locally, or configuring filter to use the cloud version of JW
Player. In either cases the first step would be to register on JW Player
website https://account.jwplayer.com/ and accept terms and conditions. Once
this step is done, you have a choice of using cloud version (hosted by JW
Player) or download the self-hosted version of the player and unpack it to
./lib/jwplayer/ directory in Moodle (that is where filter will be looking for
it in case you will select self-hosted option in the filter settings). Using
cloud version is recommended by JW Player, for full comparions see [this
page](http://www.longtailvideo.com/support/jw-player/31770/cloud-hosted-vs-self-hosted-jw-player).

The filter installation is pretty strightforward. Filter files need to be
placed in ./filter/jwplayer directory inside Moodle, then you will need to go
through installation process by loggining in as admin.

The last step is to configure the filter. After the installation, the plugin
configuration page will be shown (alternatively you may access it via Site
Adminstration -> Plugins -> Filters). You need to specify whether you use
self-hosted or cloud version of JW Player. In the latter case you would need to
specify aditionally, whether player libraries will be accessed using secure
connection (https) and the "Account token", which you may obtain from your
account settings page on [JW Player](https://account.jwplayer.com/#/account)
website. Account token is essentially the file name from cloud-hosted player
code, e.g. for script path `http://jwpsrv.com/library/ABCDEF012345.js` the
corresponding account token that needs to be entered in the settings field is
`ABCDEF012345`. For the self-hosted option, depending on your organisation type,
you may specify the license key taken from account settings page on [JW Player](https://account.jwplayer.com/#/account) website. 

You may also choose the file types, for which player will be used in the filter settings page.

Once the filter is configured, the final thing to do would be to enable the
filter on Manage Filters  page in Site Administration area and move it above
"Multimedia plugins" to give it higher priority.

Usage
-----

Any multimedia content added to moodle will be played using JW Player if the
format is supported and enabled in the config. For more details on supported
formats see [this page](http://www.longtailvideo.com/support/jw-player/28836/media-format-support)
(ignore YouTube section) on JW Player website.

Note for developers
-------------------

The filter extends core_media_player class, which allows it to use the same neat
approach to embed JW Player as used for other players in the core.

The filter can easily be extended if required, so that you may use JW Player
for other type of media, e.g. rtmp streams, and making it handle some perculiar
data source that require some modifications and extra parameters passed to JW
Player (e.g. if you want to use it for your custom media server). For
inspiration, below is the filter.php content needed for handling RTMP from your
server:
```
class filter_luflashmedia extends filter_jwplayer {
    /**
     * Replace link with embedded content, if supported.
     *
     * @param array $matches
     * @return string
     */
    private function callback(array $matches) {
        $result = '';

        // Check if we ignore it.
        if (preg_match('/class="[^"]*nomediaplugin/i', $matches[0])) {
            return $matches[0];
        }

        // Get name.
        $name = trim($matches[2]);
        if (empty($name) or strpos($name, 'http') === 0) {
            $name = ''; // Use default name.
        }

        // Split provided URL into alternatives.
        if (strpos($matches[1], 'rtmp://your.server.com' !== false) {
            // Special treatment for RTMP.
            $urls = core_media::split_alternatives($matches[1], $width, $height);
            $options['playersetupdata'] = array('primary' => 'flash');
            $result = $this->renderer->embed_alternatives($urls, $name, $width, $height, $options);
        }

        // If something was embedded, return it, otherwise return original.
        if ($result !== '') {
            return $result;
        } else {
            return $matches[0];
        }
    }
}
```
