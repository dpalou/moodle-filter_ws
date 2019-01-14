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
 * Tests for filter_ws.
 *
 * Some of the test are based on unit tests from filter_multilang2 by Iñaki Arenaza,
 * and those tests were based on unit tests from filter_text by Damyon Wise.
 *
 * @package    filter_ws
 * @category   test
 * @copyright  2014 Damyon Wiese
 * @copyright  2016 Iñaki Arenaza & Mondragon Unibertsitatea
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/ws/filter.php');
require_once($CFG->dirroot . '/filter/ws/tests/classes/filter_mock.php');

/**
 * Unit tests for WebService filter.
 *
 * Test that the filter produces the right content for each case.
 *
 * @package    filter_ws
 * @category   test
 * @copyright  2014 Damyon Wiese
 * @copyright  2016 Iñaki Arenaza & Mondragon Unibertsitatea
 * @copyright  2019 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_ws_testcase extends advanced_testcase {
    /** @var object The filter plugin object to perform the tests on */
    protected $filter;

    /** @var object A mock object to perform the tests simulating a WS request. */
    protected $filtermock;

    /**
     * Setup the test framework
     *
     * @return void
     */
    protected function setUp() {
        $this->resetAfterTest();

        $this->filter = new filter_ws(context_system::instance(), array());
        $this->filtermock = new filter_ws_mock(context_system::instance(), array());
    }

    /**
     * Test filter based on the source of the request (web or ws).
     *
     * @return void
     */
    public function test_request_source() {

        $tests = array(
            array ( // Test without WS filter tags.
                'raw' => 'No tags',
                'web' => 'No tags',
                'ws'  => 'No tags',
            ),
            array ( // Test only web text.
                'raw' => '{fws web}Web only, no more tags{fws}',
                'web' => 'Web only, no more tags',
                'ws'  => '',
            ),
            array ( // Test only WS text.
                'raw' => '{fws ws}WS only, no more tags{fws}',
                'web' => '',
                'ws'  => 'WS only, no more tags',
            ),
            array ( // Test having two tags, one for each origin.
                'raw' => '{fws web}Web only{fws}{fws ws}WS only{fws}',
                'web' => 'Web only',
                'ws'  => 'WS only',
            ),
            array ( // Test using some uppercase letters in the tag name.
                'raw' => '{FwS web}Web only, uppercase{fWs}{FWS ws}WS only, uppercase{fwS}',
                'web' => 'Web only, uppercase',
                'ws'  => 'WS only, uppercase',
            ),
            array ( // Test having some text outside of the tags.
                'raw' => 'Common text: {fws web}Web only{fws}{fws ws}WS only{fws}. And more common text.',
                'web' => 'Common text: Web only. And more common text.',
                'ws'  => 'Common text: WS only. And more common text.',
            ),
            array ( // Test the "any" origin.
                'raw' => '{fws any}Common text: {fws}{fws web}Web only{fws}{fws ws}WS only{fws}',
                'web' => 'Common text: Web only',
                'ws'  => 'Common text: WS only',
            ),
            array ( // Test bad syntax: no origin.
                'raw' => '{fws}Bad syntax{fws}',
                'web' => '{fws}Bad syntax{fws}',
                'ws'  => '{fws}Bad syntax{fws}',
            ),
            array ( // Test bad syntax: no origin followed by some valid tags.
                'raw' => '{fws}Bad syntax{fws}{fws web}Web only{fws}{fws ws}WS only{fws}',
                'web' => '{fws}Bad syntax{fws}Web only',
                'ws'  => '{fws}Bad syntax{fws}WS only',
            ),
            array ( // Test invalid origin.
                'raw' => '{fws invalid}Bad syntax{fws}',
                'web' => '',
                'ws'  => '',
            ),
        );

        foreach ($tests as $test) {
            // Simulate a request that comes from web.
            $this->assertEquals($test['web'], $this->filter->filter($test['raw']));

            // Simulate a request that comes from WS.
            $this->assertEquals($test['ws'], $this->filtermock->filter($test['raw']));
        }
    }

    /**
     * Test filter based on the user agent of the request.
     *
     * @return void
     */
    public function test_user_agent() {
        // Set a fake user agent.
        core_useragent::instance(true, 'MyBrowser v42.0.4711');

        // Test a user agent different than the current one.
        $this->assertEquals('', $this->filter->filter('{fws web ua="NotFound"}Some content{fws}'));

        // Test a user agent that matches.
        $this->assertEquals('Some content', $this->filter->filter('{fws any ua="MyBrowser"}Some content{fws}'));

        // Mix two tags, one that matches and another that doesn't.
        $this->assertEquals('Displayed', $this->filter->filter(
                '{fws any ua="NotFound"}Not seen{fws}{fws any ua="MyBrowser"}Displayed{fws}'));

        // Now test with a regular expression.
        $this->assertEquals('RegExp found', $this->filter->filter('{fws any ua="v4\d\.\d\.\d+"}RegExp found{fws}'));
    }
}
