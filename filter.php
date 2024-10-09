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

/*
 * __________________________________________________________________________
 *
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
 *
 * __________________________________________________________________________
 */

 class_alias(\filter_ws\text_filter::class, \filter_ws::class);