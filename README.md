moodle-filter_jwplayer
======================

This a filter plugin that allows using JW Player 6 for playing HTML5 and
Flash content in Moodle 2.6 and higher<sup>1</sup>. The filter is designed
to achieve consistency of the player appearance in all major browsers and
mobile platforms. The player supports Flash fallback, which provides more
devices and formats coverage than either HTML5 or Flash can handle alone.
The filter also supports RTMP and HLS streaming<sup>2</sup>.

<sub><sup>1</sup> See plugin release notes for the list of supported Moodle versions.</sub>
<sub><sup>2</sup> HLS support require paid license.</sub>

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
Player) or download the self-hosted version of the JW Player 6 and unpack it to
./lib/jwplayer/ directory in Moodle (that is where filter will be looking for
it in case you will select self-hosted option in the filter settings). Using
cloud version is recommended by JW Player, for full comparions see [this
page](http://www.longtailvideo.com/support/jw-player/31770/cloud-hosted-vs-self-hosted-jw-player).

The filter installation is pretty strightforward. Filter files need to be
placed in ./filter/jwplayer directory inside Moodle, then you will need to go
through installation process by loggining in as admin.

The last step is to configure the filter. After the installation, the plugin
configuration page will be shown (alternatively you may access it via Site
Administration -> Plugins -> Filters). You need to specify whether you use
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
        $mediaserver = 'rtmp://your.server.com';

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
        if (strpos($matches[1], $mediaserver !== false) {
            // Special treatment for RTMP.
            $urls = core_media::split_alternatives($matches[1], $width, $height);
            $result = $this->renderer->embed_alternatives($urls, $name, $width, $height);
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
