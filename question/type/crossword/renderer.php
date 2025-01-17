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
 * Crossword question renderer class.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Generates the output for crossword questions.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_crossword_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa,
        question_display_options $options): string {
        /** @var qtype_crossword_question $question */
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        $data = [];
        $orientationvalue = [
            get_string('across', 'qtype_crossword'),
            get_string('down', 'qtype_crossword')
        ];
        $binddata = [
            'colsNum' => $question->numcolumns + 3,
            'rowsNum' => $question->numrows + 3,
            'isPreview' => false,
            'title' => get_string('celltitle', 'qtype_crossword'),
            'orientation' => $orientationvalue,
            'readonly' => false
        ];
        $data['questiontext'] = $question->format_questiontext($qa);
        foreach ($question->answers as $key => $answer) {
            $orientation = $answer->orientation ? 'down' : 'across';
            $fieldname = 'sub' . $key;
            [$lengthdisplay, $ignoreindex] = $answer->generate_answer_hint();
            $answerlengthwithbreaks = core_text::strlen($answer->answer);
            $inputname = $qa->get_qt_field_name($fieldname);
            $inputvalue = $qa->get_last_qt_var($fieldname);
            $number = $key + 1;
            $feedback = '';
            if ($options->generalfeedback) {
                $feedback = $question->format_text($answer->feedback, $answer->feedbackformat,
                    $qa, 'question', 'feedback', $answer->answerid);
            }
            $clue = $question->format_text($answer->clue, $answer->clueformat, $qa, 'question', 'clue', $answer->answerid);
            $label = get_string(
                'inputlabel',
                'qtype_crossword',
                (object) [
                    'number' => $number,
                    'orientation' => $orientationvalue[$answer->orientation],
                    'clue' => html_to_text($clue, 0, false),
                    'length' => $lengthdisplay,
                ]
            );

            $attributes = "name=$inputname id=$inputname maxlength=$answerlengthwithbreaks";

            $inputdata = [
                'number' => $number,
                'clue' => $clue,
                'feedback' => $feedback,
                'lengthDisplay' => $lengthdisplay,
                'length' => $answerlengthwithbreaks,
                'value' => $inputvalue,
                'attributes' => $attributes,
                'label' => $label,
                'id' => $inputname,
                'orientation' => (int) $answer->orientation,
                'startRow' => (int) $answer->startrow,
                'startColumn' => (int) $answer->startcolumn,
                'ignoreIndexes' => json_encode($ignoreindex),
            ];

            if ($options->readonly) {
                $binddata['readonly'] = true;
                $inputdata['attributes'] .= ' readonly=true';
            }

            // Calculate fraction.
            $responseword = $response[$fieldname] ?? '';
            if ($responseword) {
                $fraction = $question->calculate_fraction_for_answer($answer, $responseword);
            } else {
                $fraction = 0;
            }

            if ($options->correctness) {
                $inputdata['classes'] = $this->feedback_class($fraction);
                $inputdata['feedbackimage'] = $this->feedback_image($fraction);
            }

            $data[$orientation][] = $inputdata;
        }

        if ($qa->get_state() === question_state::$invalid) {
            $data['invalidquestion'] = $question->get_validation_error($qa->get_last_qt_data());
        }

        $result = $this->render_from_template('qtype_crossword/crossword_clues', $data);

        $this->page->requires->js_call_amd('qtype_crossword/crossword', 'attempt', [$binddata]);
        return $result;
    }

    public function specific_feedback(question_attempt $qa): string {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa): string {
        $question = $qa->get_question();
        $right = [];
        foreach ($question->answers as $ansid => $ans) {
            $right[] = $question->make_html_inline($question->format_text($ans->answer, 1,
                $qa, 'question', 'answer', $ansid));
        }
        return $this->correct_choices($right);
    }

    /**
     * Function returns string based on number of correct answers
     * @param array $right An Array of correct responses to the current question
     * @return string based on number of correct responses
     */
    protected function correct_choices(array $right): ?string {
        // Return appropriate string for single/multiple correct answer(s).
        $stringright = '';

        if (count($right) < 1) {
            return '';
        }

        foreach ($right as $key => $value) {
            $stringright .= get_string('answer', 'qtype_crossword') . ' ' . ($key + 1) .': '. $value;
            if ($key !== count($right) - 1) {
                $stringright .= ', ';
            }
        }

        if (count($right) === 1) {
            return get_string('correctansweris', 'qtype_crossword',
                $stringright);
        }

        return get_string('correctanswersare', 'qtype_crossword',
            $stringright);
    }

    protected function num_parts_correct(question_attempt $qa): ?string {
        $a = new stdClass();
        [$a->num, $a->outof] = $qa->get_question()->get_num_parts_right($qa->get_last_qt_data());
        if (is_null($a->outof)) {
            return '';
        } else if ($a->num === 1) {
            return get_string('yougot1right', 'qtype_crossword');
        } else {
            return get_string('yougotnright', 'qtype_crossword', $a);
        }
    }
}
