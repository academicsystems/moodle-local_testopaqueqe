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
 * Unit tests for the test Opaque engine.
 *
 * @package    local_testopaqueqe
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../engine.php');

class test_local_testopaqueqe_resource extends basic_testcase {
    public function test_make_from_file() {
        $resource = local_testopaqueqe_resource::make_from_file(
                '../pix/world.gif', 'world.gif', 'image/gif');
        $this->assertEquals('world.gif', $resource->filename);
        $this->assertEquals('image/gif', $resource->mimeType);
    }
}
