<?php
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
 *  JW Player media filtering
 *
 *  This filter will replace certain links to a media file with
 *  a JW Player that plays that media inline.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/filter/jwplayer/lib.php');

/**
 * Automatic media embedding filter class.
 *
 * @package    filter
 * @subpackage jwplayer
 * @copyright  2014 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_jwplayer extends moodle_text_filter {
    /** @var filter_jwplayer Media renderer */
    protected $renderer;
    /** @var string Partial regex pattern indicating possible embeddable content */
    protected $embedmarkers;

    /**
     * Implement the filtering.
     *
     * @param $text some HTML content.
     * @param array $options options passed to the filters
     * @return the HTML content after the filtering has been applied.
     */
    public function filter($text, array $options = array()) {
        global $CFG, $PAGE;

        if (!is_string($text) or empty($text)) {
            // Non string data can not be filtered anyway.
            return $text;
        }
        if (stripos($text, '</a>') === false) {
            // Performance shortcut - all regexes bellow end with the </a> tag,
            // if not present nothing can match.
            return $text;
        }

        if (!$this->renderer) {
            $this->renderer = $PAGE->get_renderer('filter_jwplayer');
            $this->embedmarkers = $this->renderer->get_embeddable_markers();
        }

        // Handle all links that contain any 'embeddable' marker text (it could
        // do all links, but the embeddable markers thing should make it faster
        // by meaning for most links it doesn't drop into PHP code).
        $newtext = preg_replace_callback($re = '~<a\s[^>]*href="([^"]*(?:' .
                $this->embedmarkers . ')[^"]*)"[^>]*>([^>]*)</a>~is',
                array($this, 'callback'), $text);

        if (empty($newtext) or $newtext === $text) {
            // Error or not filtered.
            return $text;
        }

        return $newtext;
    }

    /**
     * Replace link with embedded content, if supported.
     *
     * @param array $matches
     * @return string
     */
    private function callback(array $matches) {
        // Check if we ignore it.
        if (preg_match('/class="[^"]*nomediaplugin/i', $matches[0])) {
            return $matches[0];
        }

        // Get name.
        $name = trim($matches[2]);
        if (empty($name) or strpos($name, 'http') === 0) {
            $name = ''; // Use default name.
        }

        // Prepare options.
        $options = array();

        // Get <a> tag attributes.
        $escapedmatch = preg_replace('/&(?!(?:apos|quot|[gl]t|amp);|#)/','&amp;',$matches[0]); // Escape any unescaped & characters.
        $doc = new DOMDocument();
        $doc->strictErrorChecking = FALSE;
        $doc->loadHTML($escapedmatch);  // Load HTML as a DOMDocument.
        if ($atag = simplexml_import_dom($doc)->body[0]->a[0]){  // Make sure xml is valid xml if not do nothing.
            $options['htmlattributes'] = $atag->attributes();
        }

        // Split provided URL into alternatives.
        $urls = filter_jwplayer_split_alternatives($matches[1], $width, $height);
        $result = $this->renderer->embed_alternatives($urls, $name, $width, $height, $options);

        // If something was embedded, return it, otherwise return original.
        if ($result !== '') {
            return $result;
        } else {
            return $matches[0];
        }
    }

    /**
     * Setup filter requirements.
     *
     * @param moodle_page $page the page we are going to add requirements to.
     * @param context $context the context which contents are going to be filtered.
     */
    public function setup($page, $context) {
        filter_jwplayer_setup($page);
    }
}
