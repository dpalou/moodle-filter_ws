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
 * WS content filter.
 *
 * @package    filter_ws
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_ws;

if (class_exists('\core_filters\text_filter')) {
    class_alias('\core_filters\text_filter', 'base_text_filter');
} else {
    class_alias('\moodle_text_filter', 'base_text_filter');
}

/**
 * Given some text, return relevant text according to whether it's a WS request or not.
 *
 * The way the filter works is as follows:
 *
 *    - look for fws blocks in the text.
 *    - if the block condition matches the current request, print it.
 *    - else, don't print the text inside the block
 *
 *  Example syntax:
 *    {fws web}Web only.{fws} Some common text for any request. {fws ws}WS only{fws}
 *
 * @package    filter_ws
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_filter extends \base_text_filter {

    /**
     * This function filters the received text based on the tags embedded in the text,
     * and whether the request is being done from WS or not.
     *
     * @param string $text The text to filter.
     * @param array $options The filter options.
     * @return string The filtered text.
     */
    public function filter($text, array $options = array()) {

        if (stripos($text, '{fws') === false) {
            return $text;
        }

        $search = '/{fws\s+([a-z0-9]+)\s*(?:ua="([^"]+)")?\s*}(.*?){\s*fws\s*}/is';
        $result = preg_replace_callback($search, array($this, 'replace_callback'), $text);

        if (is_null($result)) {
            return $text; // Error during regex processing, keep original text.
        } else {
            return $result;
        }
    }

    /**
     * This function filters the current block of ws tag. If the request belongs to the
     * specified condition (web or ws), it returns the text of the block. Otherwise it
     * returns an empty string.
     *
     * @param array $textblock An array containing the matching captured pieces of the
     *                         regular expression. They are the condition of the tag,
     *                         and the text associated with that condition.
     * @return string
     */
    protected function replace_callback($textblock) {
        $isws = $this->is_ws_access();

        // First check that the text should be displayed by this type of access.
        if ($textblock[1] == 'any' || ($textblock[1] == 'web' && !$isws) || ($textblock[1] == 'ws' && $isws)) {

            // Access is the right one. Check if we should filter the user agent too.
            if ($textblock[2]) {
                // Check that the user agent contains the right text.
                $useragent = core_useragent::get_user_agent_string();

                if (!preg_match('/' . $textblock[2] . '/', $useragent)) {
                    // The user agent doesn't contain the condition, don't display the contents.
                    return '';
                }
            }

            return $textblock[3];
        }

        return '';
    }

    /**
     * Detects if the user is accesing Moodle via Web Services.
     *
     * @return boolean True if the user is accesing via WS
     */
    protected function is_ws_access() {
        global $ME;

        // First check this global const.
        if (WS_SERVER) {
            return true;
        }

        // Check file acces via webservice/pluginfile.php.
        if (strpos($ME, "webservice/pluginfile.php") !== false) {
            $token = optional_param('token', '', PARAM_ALPHANUM);
            if ($token) {
                return true;
            }
        }

        // Files can also be accessed using tokenpluginfile.php. This endpoint is used both by LMS and the app, so checking if
        // it's a WS access it's trickier. Just check if the request comes from the app in that case.
        if (strpos($ME, "/tokenpluginfile.php") !== false && \core_useragent::is_moodle_app()) {
            return true;
        }

        return false;
    }
}
