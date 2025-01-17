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
 * This file contains tests that walks a question through simulated student attempts.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_crossword;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the crossword util.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Test safe_normalize function.
     *
     * @dataProvider test_safe_normalize_provider
     * @covers \qtype_crossword\util::safe_normalize
     *
     * @param string $string1 The first string need to compare.
     * @param string $string2 The second string need to compare.
     */
    public function test_safe_normalize(string $string1, string $string2): void {
        $normalstring1 = util::safe_normalize($string1);
        $normalstring2 = util::safe_normalize($string2);
        $this->assertEquals($normalstring1, $normalstring2);
    }

    /**
     * Data provider for test_safe_normalize() test cases.
     *
     * @coversNothing
     * @return array List of data sets (test cases)
     */
    public function test_safe_normalize_provider(): array {
        return [
            'Normal case' => [
                'Hanoi',
                'Hanoi'
            ],
            'Same character but different representation code' => [
                'Amélie',
                'Amélie'
            ]
        ];
    }

    /**
     * Test remove_accent function.
     *
     * @dataProvider test_remove_accent_provider
     * @covers \qtype_crossword\util::remove_accent
     *
     * @param string $containaccent The string contain accent characters.
     * @param string $missingaccent The string does not contain any accent characters.
     */
    public function test_remove_accent(string $containaccent, string $missingaccent): void {
        $accentremovedstring = util::remove_accent($containaccent);
        $this->assertEquals($missingaccent, $accentremovedstring);
    }

    /**
     * Data provider for test_remove_accent() test cases.
     *
     * @coversNothing
     * @return array List of data sets (test cases)
     */
    public function test_remove_accent_provider(): array {
        return [
            'Normal case' => [
                'Hanoi',
                'Hanoi'
            ],
            'One wrong accent' => [
                'médecin',
                'medecin'
            ],
            'Two wrong accent' => [
                'pâté',
                'pate'
            ],
            'Three wrong accent' => [
                'téléphoné',
                'telephone'
            ],
        ];
    }

    /**
     * Test remove_break_characters function.
     *
     * @dataProvider remove_break_characters_testcases
     * @covers \qtype_crossword\util::remove_break_characters
     *
     * @param string $text The string need to change.
     * @param string $expected The expected text.
     */
    public function test_remove_break_characters(string $text, string $expected): void {
        $text = util::remove_break_characters($text);
        $this->assertEquals($expected, $text);
    }

    /**
     * Data provider for test_remove_break_characters.
     *
     * @coversNothing
     * @return array List of data sets (test cases)
     */
    public function remove_break_characters_testcases(): array {
        return [
            'Text with space' => [
                'Los angeles',
                'Losangeles',
            ],
            'Text with hyphen' => [
                'Six-pack',
                'Sixpack',
            ],
            'Text combine hyphen and space' => [
                'Tim Berners-Lee',
                'TimBernersLee',
            ],
        ];
    }
}
