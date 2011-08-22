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
 * @package    local
 * @subpackage testopaqueqe
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/testopaqueqe/engine.php');


/**
 * Unit tests for the test Opaque engine.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_local_testopaqueqe_engine extends UnitTestCase {
    protected $engine;

    public function setUp() {
        parent::setUp();
        $this->engine = new local_testopaqueqe_engine();
    }

    public function tearDown() {
        $this->engine = null;
        parent::tearDown();
    }

    public function test_get_question_metadata_normal() {
        $this->assertEqual('<questionmetadata>
                     <scoring><marks>3</marks></scoring>
                     <plainmode>no</plainmode> 
                 </questionmetadata>',
                $this->engine->getQuestionMetadata('test', '1.0', ''));
    }

    public function test_get_question_metadata_fail() {
        $this->expectException();
        $this->engine->getQuestionMetadata('metadata.fail', '1.0', '');
    }

    public function test_get_question_metadata_slow() {
        $start = microtime(true);
        $this->assertEqual('<questionmetadata>
                     <scoring><marks>3</marks></scoring>
                     <plainmode>no</plainmode> 
                 </questionmetadata>',
                $this->engine->getQuestionMetadata('metadata.slow', '0.05', ''));
        $this->assertTrue(microtime(true) - $start > 0.05);
    }
}


class test_local_testopaqueqe_resource extends UnitTestCase {
    public function test_make_from_file() {
        global $CFG;
        $resource = local_testopaqueqe_resource::make_from_file(
                $CFG->dirroot . '/local/testopaqueqe/pix/world.gif', 'world.gif', 'image/gif');
        $this->assertEqual('world.gif', $resource->filename);
        $this->assertEqual('image/gif', $resource->mimeType);
    }
}