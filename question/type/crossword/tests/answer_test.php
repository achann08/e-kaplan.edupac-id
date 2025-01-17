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

namespace qtype_crossword;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for qtype_crossword answer.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class answer_test extends \advanced_testcase {

    /**
     * Test is_correct function.
     *
     * @dataProvider test_is_correct_provider
     * @covers \qtype_crossword_question::is_correct
     */
    public function test_is_correct(array $answerdata) {
        // Create a normal crossword question.
        $q = \test_question_maker::make_question('crossword', 'normal_with_hyphen_and_space');
        foreach ($q->answers as $key => $answer) {
            $this->assertTrue($answer->is_correct($answerdata[$key]));
        }
    }

    /**
     * Data provider for test_is_correct() test cases.
     *
     * @coversNothing
     * @return array List of data sets (test cases)
     */
    public function test_is_correct_provider(): array {
        return [
            'Normal case' => [
                ['DAVID ATTENBOROUGH', 'GORDON BROWN', 'TIM BERNERS-LEE']
            ],
            'With Underscore' => [
                ['DAVID_ATTENBOROUGH', 'GORDON_BROWN', 'TIM_BERNERS-LEE']
            ]
        ];
    }

    /**
     * Test generate_answer_hint function.
     *
     * @covers \qtype_crossword_question::generate_answer_hint
     */
    public function test_generate_answer_hint() {
        // Create a normal crossword question.
        $q = \test_question_maker::make_question('crossword', 'normal_with_hyphen_and_space');
        $expecteddata = [
            ['5, 12', ['space' => [5]]],
            ['6, 5', ['space' => [6]]],
            ['3, 7-3', ['space' => [3], 'hyphen' => [11]]],
        ];
        foreach ($q->answers as $key => $answer) {
            $this->assertEquals($expecteddata[$key], $answer->generate_answer_hint());
        }
    }
}
