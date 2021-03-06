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
 * Mock class to test the WS filter.
 * This class is needed because we cannot mock the WS_SERVER constant.
 *
 * @package    filter_ws
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/filter/ws/filter.php');

/**
 * Mock class to test the WS filter.
 *
 * @package    filter_ws
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_ws_mock extends filter_ws {

    /**
     * Detects if the user is accesing Moodle via Web Services.
     *
     * @return boolean True if the user is accesing via WS
     */
    protected function is_ws_access() {
        return true;
    }
}
