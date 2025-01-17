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
 * Unit tests for the crossword question editing form.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_crossword;
use qtype_crossword_test_helper;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/type/crossword/tests/helper.php');

/**
 * Unit tests for qtype_crossword editing form.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \qtype_crossword_edit_form
 */
class form_test extends \advanced_testcase {

    /**
     * Data provider for test_form_validation() test cases.
     *
     * @return array List of data sets (test cases)
     */
    public function form_validation_testcases(): array {
        return [
            'Normal case' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        'BRAZIL', 'PARIS', 'ITALY'
                    ],
                    'clue' => [
                        ['text' => 'where is the Christ the Redeemer statue located in?', 'format' => FORMAT_HTML],
                        ['text' => 'Eiffel Tower is located in?', 'format' => FORMAT_HTML],
                        ['text' => 'Where is the Leaning Tower of Pisa?', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 1, 0
                    ],
                    'startrow' => [
                        1, 0, 3
                    ],
                    'startcolumn' => [
                        0, 2, 2
                    ],
                ], []
            ],
            'The letter at the intersection of two words do not match' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        'AAA', 'BBB', 'CCC'
                    ],
                    'clue' => [
                        ['text' => 'Clue A', 'format' => FORMAT_HTML],
                        ['text' => 'Clue B', 'format' => FORMAT_HTML],
                        ['text' => 'Clue C', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 0, 0
                    ],
                    'startrow' => [
                        0, 0, 0
                    ],
                    'startcolumn' => [
                        0, 0, 0
                    ],
                ],
                [
                    'answer[1]' => get_string('wrongintersection', 'qtype_crossword'),
                    'answer[2]' => get_string('wrongintersection', 'qtype_crossword')
                ]
            ],
            'Requires at least 1 word' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        '', '', ''
                    ],
                    'clue' => [
                        ['text' => '', 'format' => FORMAT_HTML],
                        ['text' => '', 'format' => FORMAT_HTML],
                        ['text' => '', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 0, 0
                    ],
                    'startrow' => [
                        0, 0, 0
                    ],
                    'startcolumn' => [
                        0, 0, 0
                    ],
                ], ['answer[0]' => get_string('notenoughwords', 'qtype_crossword', 1)]
            ],
            'The word start or end position is outside the defined grid size' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        'Toolongtext', 'BBB', 'CCC'
                    ],
                    'clue' => [
                        ['text' => 'Clue A', 'format' => FORMAT_HTML],
                        ['text' => 'Clue B', 'format' => FORMAT_HTML],
                        ['text' => 'Clue C', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 0, 0
                    ],
                    'startrow' => [
                        0, 1, 2
                    ],
                    'startcolumn' => [
                        0, 0, 0
                    ],
                ], ['answer[0]' => get_string('overflowposition', 'qtype_crossword')]
            ],
            'The answer must be alphanumeric characters only' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        'Speci@al char*', 'BBB', 'CCC'
                    ],
                    'clue' => [
                        ['text' => 'Clue A', 'format' => FORMAT_HTML],
                        ['text' => 'Clue B', 'format' => FORMAT_HTML],
                        ['text' => 'Clue C', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 0, 0
                    ],
                    'startrow' => [
                        0, 1, 2
                    ],
                    'startcolumn' => [
                        0, 0, 0
                    ],
                ], ['answer[0]' => get_string('mustbealphanumeric', 'qtype_crossword')]
            ],
            'The word must have both clues and answers' => [
                [
                    'noanswers' => 3,
                    'answer' => [
                        'AAA', '', 'CCC'
                    ],
                    'clue' => [
                        ['text' => '', 'format' => FORMAT_HTML],
                        ['text' => 'Clue B', 'format' => FORMAT_HTML],
                        ['text' => 'Clue C', 'format' => FORMAT_HTML]
                    ],
                    'orientation' => [
                        0, 0, 0
                    ],
                    'startrow' => [
                        0, 1, 2
                    ],
                    'startcolumn' => [
                        0, 0, 0
                    ],
                ],
                [
                    'answer[1]' => get_string('pleaseenterclueandanswer', 'qtype_crossword', 2),
                    'clue[0]' => get_string('pleaseenterclueandanswer', 'qtype_crossword', 1),
                ]
            ]
        ];
    }

    /**
     * Prepare test data.
     *
     * @return array List data $mform and $course.
     */
    public function prepare_test_data(): array {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $gen = $this->getDataGenerator();
        $course = $gen->create_course();
        $context = \context_course::instance($course->id);

        $contexts = qtype_crossword_test_helper::question_edit_contexts($context);
        $category = question_make_default_categories($contexts->all());

        $question = new \stdClass();
        $question->category = $category->id;
        $question->contextid = $category->contextid;
        $question->qtype = 'crossword';
        $question->createdby = 1;
        $question->questiontext = 'Initial text';
        $question->timecreated = '1234567890';
        $question->formoptions = new \stdClass();
        $question->formoptions->canedit = true;
        $question->formoptions->canmove = true;
        $question->formoptions->cansaveasnew = false;
        $question->formoptions->repeatelements = true;

        $qtypeobj = \question_bank::get_qtype($question->qtype);

        $mform = $qtypeobj->create_editing_form('question.php', $question, $category, $contexts, true);
        return [$mform, $course];
    }

    /**
     * Test editing form validation.
     *
     * @dataProvider form_validation_testcases
     * @param array $sampledata
     * @param array $expectederror
     */
    public function test_form_validation(array $sampledata, array $expectederror): void {

        list ($mform, $course) = $this->prepare_test_data();
        $fromform = [
            'category' => 1,
            'name' => 'Test combined with varnumeric',
            'questiontext' => [
                'text' => 'Test crossword qtype',
                'format' => 1
            ],
            'generalfeedback' => [
                'text' => '',
                'format' => 1
            ],
            'partiallycorrectfeedback' => [
                'text' => 'Your answer is partially correct.',
                'format' => 1
            ],
            'shownumcorrect' => 1,
            'incorrectfeedback' => [
                'text' => 'Your answer is incorrect.',
                'format' => 1
            ],
            'numcolumns' => 5,
            'numrows' => 7,
            'penalty' => 0.3333333,
            'numhints' => 0,
            'hints' => [],
            'hintshownumcorrect' => [],
            'tags' => 0,
            'id' => 0,
            'inpopup' => 0,
            'cmid' => 0,
            'courseid' => $course->id,
            'returnurl' => '/mod/quiz/edit.php?cmid=0',
            'scrollpos' => 0,
            'appendqnumstring' => '',
            'qtype' => 'crossword',
            'makecopy' => 0,
            'updatebutton' => 'Save changes and continue editing',
        ];
        $fromform = array_merge($fromform, $sampledata);
        $errors = $mform->validation($fromform, []);
        $this->assertEquals($expectederror, $errors);
    }

    /**
     * Test function generate_alphabet_list.
     *
     * @param array $option Option list.
     * @param array $expected Expected data.
     *
     * @dataProvider generate_alphabet_list_testcases
     */
    public function test_generate_alphabet_list(array $option, array $expected) {
        list ($mform) = $this->prepare_test_data();
        list ($start, $end) = $option;
        $method = new \ReflectionMethod(\qtype_crossword_edit_form::class,
            'generate_alphabet_list');
        $method->setAccessible(true);
        $result = $method->invoke($mform, $start, $end);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for the generate_alphabet_list test.
     *
     * @return array
     */
    public function generate_alphabet_list_testcases(): array {

        return [
            'Alphabet list from 1 to 26' => [
                [0, 26],
                ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                    'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
            ],
            'Alphabet list from 1 to 30' => [
                [0, 30],
                ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
                    'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD']
            ],
        ];
    }

    /**
     * Test function validate_answer.
     *
     * @param array $data Data from provider.
     * @dataProvider validate_answer_testcases
     */
    public function test_validate_answer(array $data) {
        list ($mform) = $this->prepare_test_data();
        $method = new \ReflectionMethod(\qtype_crossword_edit_form::class,
            'validate_answer');
        $method->setAccessible(true);
        $result = $method->invoke($mform, $data[0]);
        $this->assertEquals($data[1], $result);
    }

    /**
     * Data provider for the validate_answer test.
     *
     * @return array
     */
    public function validate_answer_testcases(): array {

        return [
            'Answer start with hyphen' => [
                ['-MOODLE', get_string('wrongpositionhyphencharacter', 'qtype_crossword')],
            ],
            'Answer exists two consecutive hyphen' => [
                ['MO--DLE', get_string('wrongadjacentcharacter', 'qtype_crossword')],
            ],
            'Answer exists two space hyphen' => [
                ['MO  ODLE', get_string('wrongadjacentcharacter', 'qtype_crossword')],
            ],
            'The valid answer' => [
                ['MOODLE', ''],
            ],
        ];
    }
}
