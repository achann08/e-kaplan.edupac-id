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
 * Test helpers for the crossword question type.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/crossword/questiontype.php');

/**
 * Test helper class for the crossword question type.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_crossword_test_helper extends question_test_helper {

    /**
     * Get test question function.
     *
     * @return array The test question array.
     */
    public function get_test_questions(): array {
        return ['normal', 'unicode', 'different_codepoint', 'sampleimage',
            'clear_incorrect_response', 'normal_with_hyphen_and_space',
            'not_accept_wrong_accents', 'accept_wrong_accents_but_subtract_point',
            'accept_wrong_accents_but_not_subtract_point'];
    }

    /**
     * Makes a normal crossword question.
     *
     * The crossword layout is:
     *
     *     P
     * B R A Z I L
     *     R
     *     I T A L Y
     *     S
     *
     * @return qtype_crossword_question
     */
    public function make_crossword_question_normal() {
        question_bank::load_question_definition_classes('crossword');
        $cw = new qtype_crossword_question();
        test_question_maker::initialise_a_question($cw);
        $cw->name = 'Cross word question';
        $cw->questiontext = 'Cross word question text.';
        $cw->correctfeedback = 'Cross word feedback.';
        $cw->correctfeedbackformat = FORMAT_HTML;
        $cw->penalty = 1;
        $cw->defaultmark = 1;
        $cw->numrows = 5;
        $cw->numcolumns = 7;
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $cw->accentpenalty = 0;
        $cw->qtype = question_bank::get_qtype('crossword');
        $answerslist = [
            (object) [
                'id' => 1,
                'questionid' => 1,
                'clue' => 'where is the Christ the Redeemer statue located in?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'BRAZIL',
                'startcolumn' => 0,
                'startrow' => 1,
                'orientation' => 0,
                'feedback' => 'This is correct answer',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 2,
                'questionid' => 1,
                'clue' => 'Eiffel Tower is located in?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'PARIS',
                'startcolumn' => 2,
                'startrow' => 0,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 3,
                'questionid' => 1,
                'clue' => 'Where is the Leaning Tower of Pisa?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'ITALY',
                'startcolumn' => 2,
                'startrow' => 3,
                'orientation' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
        ];

        foreach ($answerslist as $answer) {
            $cw->answers[] = new \qtype_crossword\answer(
                $answer->id,
                $answer->answer,
                $answer->clue,
                $answer->clueformat,
                $answer->orientation,
                $answer->startrow,
                $answer->startcolumn,
                $answer->feedback,
                $answer->feedbackformat,
            );
        }
        return $cw;
    }

    /**
     * Makes a normal crossword question.
     */
    public function get_crossword_question_form_data_normal() {
        $fromform = new stdClass();
        $fromform->name = 'Cross word question';
        $fromform->questiontext = ['text' => 'Crossword question text', 'format' => FORMAT_HTML];
        $fromform->correctfeedback = ['text' => 'Correct feedback', 'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback.', 'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback.', 'format' => FORMAT_HTML];
        $fromform->penalty = 1;
        $fromform->defaultmark = 1;
        $fromform->answer = ['BRAZIL', 'PARIS', 'ITALY'];
        $fromform->clue = [
            [
                'text' => 'where is the Christ the Redeemer statue located in?',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Eiffel Tower is located in?',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Where is the Leaning Tower of Pisa?',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->orientation = [0, 1, 0];
        $fromform->startrow = [1, 0, 3];
        $fromform->startcolumn = [0, 2, 2];
        $fromform->numrows = 5;
        $fromform->numcolumns = 7;
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $fromform->accentpenalty = 0;
        return $fromform;
    }

    /**
     * Makes a normal crossword question with a sample image in question text.
     *
     * @return qtype_crossword_question
     */
    public function get_crossword_question_form_data_sampleimage() {
        $fromform = $this->get_crossword_question_form_data_normal();
        $fromform->correctfeedback = ['text' => 'Correct feedback <img src="@@PLUGINFILE@@/correctfbimg.jpg" />',
            'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback. <img src="@@PLUGINFILE@@/partialfbimg.jpg"',
            'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback. <img src="@@PLUGINFILE@@/incorrectfbimg.jpg"',
            'format' => FORMAT_HTML];
        $fromform->questiontext = [
            'text' => 'Cross word question text with sample image <img src="@@PLUGINFILE@@/questiontextimg.jpg" />',
            'format' => FORMAT_HTML
        ];
        $fromform->feedback = [
            [
                'text' => 'where is the Christ the Redeemer statue located in? <img src="@@PLUGINFILE@@/feedback.jpg" />',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->clue[0]['text'] = 'where is the Christ the Redeemer statue located in?' .
            '<img src="@@PLUGINFILE@@/clueimg.jpg" />';
        $fromform->feedback[0]['text'] = 'where is the Christ the Redeemer statue located in?' .
            '<img src="@@PLUGINFILE@@/feedback.jpg" />';
        return $fromform;
    }

    /**
     * Makes a unicode crossword question.
     *
     * @return qtype_crossword_question
     */
    public function make_crossword_question_unicode() {
        question_bank::load_question_definition_classes('crossword');
        $cw = new qtype_crossword_question();
        test_question_maker::initialise_a_question($cw);
        $cw->name = 'Cross word question unicode';
        $cw->questiontext = 'Cross word question text unicode.';
        $cw->correctfeedback = 'Cross word feedback unicode.';
        $cw->correctfeedbackformat = FORMAT_HTML;
        $cw->penalty = 1;
        $cw->defaultmark = 1;
        $cw->numrows = 4;
        $cw->numcolumns = 4;
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $cw->accentpenalty = 0;
        $cw->qtype = question_bank::get_qtype('crossword');
        $answerslist = [
            (object) [
                'id' => 1,
                'questionid' => 2,
                'clue' => '线索 1',
                'clueformat' => FORMAT_HTML,
                'answer' => '回答一',
                'startcolumn' => 0,
                'startrow' => 2,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 2,
                'questionid' => 2,
                'clue' => '线索 2',
                'clueformat' => FORMAT_HTML,
                'answer' => '回答两个',
                'startcolumn' => 0,
                'startrow' => 2,
                'orientation' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 3,
                'questionid' => 2,
                'clue' => '线索 3',
                'clueformat' => FORMAT_HTML,
                'answer' => '回答三',
                'startcolumn' => 1,
                'startrow' => 1,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
        ];

        foreach ($answerslist as $answer) {
            $cw->answers[] = new \qtype_crossword\answer(
                $answer->id,
                $answer->answer,
                $answer->clue,
                $answer->clueformat,
                $answer->orientation,
                $answer->startrow,
                $answer->startcolumn,
                $answer->feedback,
                $answer->feedbackformat,
            );
        }
        return $cw;
    }

    /**
     * Get a unicode crossword question form data.
     */
    public function get_crossword_question_form_data_unicode() {
        $fromform = new stdClass();
        $fromform->name = 'Cross word question unicode';
        $fromform->questiontext = ['text' => 'Crossword question text unicode', 'format' => FORMAT_HTML];
        $fromform->correctfeedback = ['text' => 'Correct feedback', 'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback.', 'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback.', 'format' => FORMAT_HTML];
        $fromform->penalty = 1;
        $fromform->defaultmark = 1;
        $fromform->answer = ['回答一', '回答两个', '回答三'];
        $fromform->clue = [
            [
                'text' => '线索 1',
                'format' => FORMAT_HTML
            ],
            [
                'text' => '线索 2',
                'format' => FORMAT_HTML
            ],
            [
                'text' => '线索 3',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->orientation = [1, 0, 1];
        $fromform->startrow = [2, 2, 1];
        $fromform->startcolumn = [0, 0, 1];
        $fromform->numrows = 4;
        $fromform->numcolumns = 4;
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $fromform->accentpenalty = 0;
        return $fromform;
    }

    /**
     * Makes a crossword question has two same answers but different code point.
     *
     * @return qtype_crossword_question
     */
    public function make_crossword_question_different_codepoint() {
        question_bank::load_question_definition_classes('crossword');
        $cw = new qtype_crossword_question();
        test_question_maker::initialise_a_question($cw);
        $cw->name = 'Cross word question different codepoint';
        $cw->questiontext = 'Cross word question text different codepoint.';
        $cw->correctfeedback = 'Cross word feedback different codepoint.';
        $cw->correctfeedbackformat = FORMAT_HTML;
        $cw->penalty = 1;
        $cw->defaultmark = 1;
        $cw->numrows = 6;
        $cw->numcolumns = 6;
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $cw->accentpenalty = 0;
        $cw->qtype = question_bank::get_qtype('crossword');
        $answerslist = [
            (object) [
                'id' => 1,
                'questionid' => 2,
                'clue' => 'Answer contains letter é has codepoint \u00e9',
                'clueformat' => FORMAT_HTML,
                'answer' => 'Amélie',
                'startcolumn' => 0,
                'startrow' => 3,
                'orientation' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 2,
                'questionid' => 2,
                'clue' => 'Answer contains letter é has codepoint \u0065\u0301',
                'clueformat' => FORMAT_HTML,
                'answer' => 'Amélie',
                'startcolumn' => 2,
                'startrow' => 1,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
        ];

        foreach ($answerslist as $answer) {
            $cw->answers[] = new \qtype_crossword\answer(
                $answer->id,
                $answer->answer,
                $answer->clue,
                $answer->clueformat,
                $answer->orientation,
                $answer->startrow,
                $answer->startcolumn,
                $answer->feedback,
                $answer->feedbackformat,
            );
        }
        return $cw;
    }

    /**
     * Get a different codepoint crossword question form data.
     */
    public function get_crossword_question_form_data_different_codepoint() {
        $fromform = new stdClass();
        $fromform->name = 'Cross word question different codepoint';
        $fromform->questiontext = ['text' => 'Crossword question text different codepoint', 'format' => FORMAT_HTML];
        $fromform->correctfeedback = ['text' => 'Correct feedback', 'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback.', 'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback.', 'format' => FORMAT_HTML];
        $fromform->penalty = 1;
        $fromform->defaultmark = 1;
        $fromform->answer = ['Amélie', 'Amélie'];
        $fromform->clue = [
            [
                'text' => 'Answer contains letter é has codepoint \u00e9',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Answer contains letter é has codepoint \u0065\u0301',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->orientation = [0, 1];
        $fromform->startrow = [3, 1];
        $fromform->startcolumn = [0, 2];
        $fromform->numrows = 6;
        $fromform->numcolumns = 6;
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $fromform->accentpenalty = 0;
        return $fromform;
    }

    /**
     * Makes a crossword question with clear incorrect responses option.
     *
     * @return qtype_crossword_question
     */
    public function make_crossword_question_clear_incorrect_response() {
        $cw = $this->make_crossword_question_normal();
        return $this->set_hints_for_question($cw);
    }

    /**
     * Get a crossword with the clear incorrect response options.
     *
     * @return qtype_crossword_question
     */
    public function get_crossword_question_form_data_clear_incorrect_response() {
        $fromform = $this->get_crossword_question_form_data_normal();
        return $this->set_multiple_tries_for_form_data($fromform, '0.3333333');
    }

    /**
     * Retrieve the context object.
     * @param \context $context the current context.
     *
     * @return object The context object.
     */
    public static function question_edit_contexts(\context $context): object {
        if (class_exists('\core_question\local\bank\question_edit_contexts')) {
            $contexts = new \core_question\local\bank\question_edit_contexts($context);
        } else {
            $contexts = new \question_edit_contexts($context);
        }
        return $contexts;
    }

    /**
     * Makes a normal crossword question with answer contain hyphen and spaces.
     *
     * The crossword layout is:
     *       T               G
     * D A V I D A T T E N B O R O U G H
     *       M               R
     *       B               D
     *       E               O
     *       R               N
     *       N               B
     *       E               R
     *       R               O
     *       S               W
     *       L               N
     *       E
     *       E
     *
     * @return qtype_crossword_question
     */
    public function make_crossword_question_normal_with_hyphen_and_space() {
        question_bank::load_question_definition_classes('crossword');
        $cw = new qtype_crossword_question();
        test_question_maker::initialise_a_question($cw);
        $cw->name = 'Cross word question';
        $cw->questiontext = 'Cross word question text.';
        $cw->correctfeedback = 'Cross word feedback.';
        $cw->correctfeedbackformat = FORMAT_HTML;
        $cw->penalty = 1;
        $cw->defaultmark = 1;
        $cw->numrows = 13;
        $cw->numcolumns = 17;
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $cw->accentpenalty = 0;
        $cw->qtype = question_bank::get_qtype('crossword');
        $answerslist = [
            (object) [
                'id' => 1,
                'questionid' => 1,
                'clue' => 'British broadcaster and naturalist, famous for his voice-overs of nature programmes?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'DAVID ATTENBOROUGH',
                'startcolumn' => 0,
                'startrow' => 1,
                'orientation' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 2,
                'questionid' => 1,
                'clue' => 'Former Prime Minister of the United Kingdom?',
                'answer' => 'GORDON BROWN',
                'clueformat' => FORMAT_HTML,
                'startcolumn' => 11,
                'startrow' => 0,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 3,
                'questionid' => 1,
                'clue' => 'Engineer, computer scientist and inventor of the World Wide Web?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'TIM BERNERS-LEE',
                'startcolumn' => 3,
                'startrow' => 0,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
        ];

        foreach ($answerslist as $answer) {
            $cw->answers[] = new \qtype_crossword\answer(
                $answer->id,
                $answer->answer,
                $answer->clue,
                $answer->clueformat,
                $answer->orientation,
                $answer->startrow,
                $answer->startcolumn,
                $answer->feedback,
                $answer->feedbackformat,
            );
        }
        return $cw;
    }

    /**
     * Makes a normal crossword question with answer contains hyphen and space.
     */
    public function get_crossword_question_form_data_normal_with_hyphen_and_space() {
        $fromform = new stdClass();
        $fromform->name = 'Cross word question';
        $fromform->questiontext = ['text' => 'Crossword question text', 'format' => FORMAT_HTML];
        $fromform->correctfeedback = ['text' => 'Correct feedback', 'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback.', 'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback.', 'format' => FORMAT_HTML];
        $fromform->penalty = 1;
        $fromform->defaultmark = 1;
        $fromform->answer = ['DAVID ATTENBOROUGH', 'GORDON BROWN', 'TIM BERNERS-LEE'];
        $fromform->clue = [
            [
                'text' => 'British broadcaster and naturalist, famous for his voice-overs of nature programmes?',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Former Prime Minister of the United Kingdom?',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Engineer, computer scientist and inventor of the World Wide Web?',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->orientation = [0, 1, 1];
        $fromform->startrow = [1, 0, 0];
        $fromform->startcolumn = [0, 11, 3];
        $fromform->numrows = 13;
        $fromform->numcolumns = 17;
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $fromform->accentpenalty = 0;
        return $fromform;
    }

    /**
     * Makes a normal crossword question do not accept wrong accents.
     *
     * The crossword layout is:
     *
     * P Â T É
     *     É
     *     L
     *     É
     *     P
     *     H
     *     O
     *     N
     *     E
     * @return qtype_crossword_question
     */
    public function make_crossword_question_not_accept_wrong_accents() {
        question_bank::load_question_definition_classes('crossword');
        $cw = new qtype_crossword_question();
        test_question_maker::initialise_a_question($cw);
        $cw->name = 'Cross word question contain accent';
        $cw->questiontext = 'Cross word question text.';
        $cw->correctfeedback = 'Cross word feedback.';
        $cw->correctfeedbackformat = FORMAT_HTML;
        $cw->penalty = 1;
        $cw->defaultmark = 1;
        $cw->numrows = 9;
        $cw->numcolumns = 4;
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $cw->accentpenalty = 0;
        $cw->qtype = question_bank::get_qtype('crossword');
        $answerslist = [
            (object) [
                'id' => 1,
                'questionid' => 1,
                'clue' => 'Des accompagnements à base de foie animal ?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'PÂTÉ',
                'startcolumn' => 0,
                'startrow' => 0,
                'orientation' => 0,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
            (object) [
                'id' => 2,
                'questionid' => 1,
                'clue' => 'Appareil utilisé pour passer des appels ?',
                'clueformat' => FORMAT_HTML,
                'answer' => 'TÉLÉPHONE',
                'startcolumn' => 2,
                'startrow' => 0,
                'orientation' => 1,
                'feedback' => '',
                'feedbackformat' => FORMAT_HTML,
            ],
        ];

        foreach ($answerslist as $answer) {
            $cw->answers[] = new \qtype_crossword\answer(
                $answer->id,
                $answer->answer,
                $answer->clue,
                $answer->clueformat,
                $answer->orientation,
                $answer->startrow,
                $answer->startcolumn,
                $answer->feedback,
                $answer->feedbackformat,
            );
        }
        return $cw;
    }

    /**
     * Makes a normal crossword question do not accept wrong accents.
     */
    public function get_crossword_question_form_data_not_accept_wrong_accents() {
        $fromform = new stdClass();
        $fromform->name = 'Cross word question';
        $fromform->questiontext = ['text' => 'Crossword question text', 'format' => FORMAT_HTML];
        $fromform->correctfeedback = ['text' => 'Correct feedback', 'format' => FORMAT_HTML];
        $fromform->partiallycorrectfeedback = ['text' => 'Partially correct feedback.', 'format' => FORMAT_HTML];
        $fromform->incorrectfeedback = ['text' => 'Incorrect feedback.', 'format' => FORMAT_HTML];
        $fromform->penalty = 0.2;
        $fromform->defaultmark = 1;
        $fromform->answer = ['PÂTÉ', 'TÉLÉPHONE'];
        $fromform->clue = [
            [
                'text' => 'Des accompagnements à base de foie animal ?',
                'format' => FORMAT_HTML
            ],
            [
                'text' => 'Appareil utilisé pour passer des appels ?',
                'format' => FORMAT_HTML
            ],
        ];
        $fromform->orientation = [0, 1];
        $fromform->startrow = [0, 0];
        $fromform->startcolumn = [0, 2];
        $fromform->numrows = 9;
        $fromform->numcolumns = 4;
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_STRICT;
        $fromform->accentpenalty = 0;
        return $fromform;
    }

    /**
     * Makes a normal crossword question accepts wrong accents but subtracts points.
     *
     * The crossword layout is:
     *
     * P Â T É
     *     É
     *     L
     *     É
     *     P
     *     H
     *     O
     *     N
     *     E
     * @return qtype_crossword_question
     */
    public function make_crossword_question_accept_wrong_accents_but_subtract_point() {
        $cw = $this->make_crossword_question_not_accept_wrong_accents();
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_PENALTY;
        $cw->accentpenalty = 0.25;
        return $this->set_hints_for_question($cw);
    }

    /**
     * Makes a normal crossword question accept wrong accents but subtracts points.
     */
    public function get_crossword_question_form_data_accept_wrong_accents_but_subtract_point() {
        $fromform = $this->get_crossword_question_form_data_not_accept_wrong_accents();
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_PENALTY;
        $fromform->accentpenalty = 0.25;
        return $this->set_multiple_tries_for_form_data($fromform, '0.1');
    }

    /**
     * Makes a normal crossword question accepts wrong accents but do not subtract points.
     *
     * The crossword layout is:
     *
     * P Â T É
     *     É
     *     L
     *     É
     *     P
     *     H
     *     O
     *     N
     *     E
     * @return qtype_crossword_question
     */
    public function make_crossword_question_accept_wrong_accents_but_not_subtract_point() {
        $cw = $this->make_crossword_question_not_accept_wrong_accents();
        $cw->accentgradingtype = qtype_crossword::ACCENT_GRADING_IGNORE;
        $cw->accentpenalty = 0;
        return $this->set_hints_for_question($cw);
    }

    /**
     * Makes a normal crossword question accept wrong accents but do not subtract points.
     */
    public function get_crossword_question_form_data_accept_wrong_accents_but_not_subtract_point() {
        $fromform = $this->get_crossword_question_form_data_not_accept_wrong_accents();
        $fromform->accentgradingtype = qtype_crossword::ACCENT_GRADING_IGNORE;
        $fromform->accentpenalty = 0;
        return $this->set_multiple_tries_for_form_data($fromform, '0.1');
    }

    /**
     * Set default hints for questions.
     *
     * @param qtype_crossword_question $cw Crossword question object.
     * @return qtype_crossword_question Crossword question object after setting hints.
     */
    private function set_hints_for_question(qtype_crossword_question $cw): qtype_crossword_question {
        $cw->hints = [
            new question_hint_with_parts(1, 'Hint 1.', FORMAT_HTML, true, true),
            new question_hint_with_parts(2, 'Hint 2.', FORMAT_HTML, true, true),
        ];
        return $cw;
    }

    /**
     * Set multiple tries for from data.
     *
     * @param stdClass $fromform Form data.
     * @param float $penalty Penalty points for each attempt.
     * @return stdClass Form data after setting data multiple try.
     */
    private function set_multiple_tries_for_form_data(stdClass $fromform, float $penalty): stdClass {
        $fromform->penalty = $penalty;
        $fromform->hint = [
            [
                'text' => 'You are wrong.',
                'format' => FORMAT_HTML,
            ],
            [
                'text' => 'You are wrong.',
                'format' => FORMAT_HTML,
            ],
        ];
        $fromform->hintshownumcorrect = [1, 1];
        $fromform->hintclearwrong = [1, 1];
        $fromform->hintoptions = [1, 1];
        return $fromform;
    }
}
