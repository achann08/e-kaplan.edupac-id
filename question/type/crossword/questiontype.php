<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Question type class for crossword is defined here.
 *
 * @package     qtype_crossword
 * @copyright   2022 The Open University
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/questionlib.php');

/**
 * Class that represents a crossword question type.
 *
 * The class loads, saves and deletes questions of the type crossword
 * to and from the database and provides methods to help with editing questions
 * of this type. It can also provide the implementation for import and export
 * in various formats.
 */
class qtype_crossword extends question_type {

    /** @const array The word fields list */
    private const WORD_FIELDS = ['answer', 'clue', 'orientation', 'startrow', 'startcolumn', 'feedback'];

    /** @const string The answer must be completely correct and must not be accents wrong */
    const ACCENT_GRADING_STRICT = 'strict';

    /** @const string Accents errors are allowed, but points will be deducted. */
    const ACCENT_GRADING_PENALTY = 'penalty';

    /** @const string Accents errors are allowed and the points will not be deducted. */
    const ACCENT_GRADING_IGNORE = 'ignore';

    public function get_question_options($question): bool {
        global $DB;
        parent::get_question_options($question);
        $question->options = $DB->get_record('qtype_crossword_options', ['questionid' => $question->id]);
        if ($question->options === false) {
            // If this has happened, then we have a problem.
            // For the user to be able to edit or delete this question, we need options.
            debugging("Question ID {$question->id} was missing an options record. Using default.", DEBUG_DEVELOPER);

            $question->options = $this->create_default_options($question);
        }
        $question->options->words = $DB->get_records('qtype_crossword_words',
            ['questionid' => $question->id], 'id ASC');
        return true;
    }

    /**
     * Create a default options object for the provided question.
     *
     * @param object $question The queston we are working with.
     * @return object The options object.
     */
    protected function create_default_options($question): object {
        // Create a default question options record.
        $options = new stdClass();
        $options->questionid = $question->id;

        // Get the default strings and just set the format.
        $options->correctfeedback = get_string('correctfeedbackdefault', 'question');
        $options->correctfeedbackformat = FORMAT_HTML;
        $options->partiallycorrectfeedback = get_string('partiallycorrectfeedbackdefault', 'question');;
        $options->partiallycorrectfeedbackformat = FORMAT_HTML;
        $options->incorrectfeedback = get_string('incorrectfeedbackdefault', 'question');
        $options->incorrectfeedbackformat = FORMAT_HTML;
        $options->shownumcorrect = 1;
        $options->numrows = 10;
        $options->numcolumns = 10;
        $options->accentgradingtype = self::ACCENT_GRADING_STRICT;
        $options->accentpenalty = 0.5;
        return $options;
    }

    public function save_question($question, $form) {
        // For MVP version, default mark will be set automatically.
        $marks = 0;
        for ($i = 0; $i < count($form->answer); $i++) {
            if (trim($form->answer[$i]) === '' || trim($form->clue[$i]['text']) === '') {
                continue;
            }
            $marks++;
        }
        $form->defaultmark = $marks;
        return parent::save_question($question, $form);
    }

    public function save_question_options($question) {
        global $DB;
        $context = $question->context;
        $result = new stdClass();

        // Old words.
        $oldwords = $DB->get_records('qtype_crossword_words',
            ['questionid' => $question->id], 'id ASC');

        $numwords = count($question->answer);

        // Following hack to check at least 1 words exist.
        $answercount = 0;
        for ($i = 0; $i < $numwords; $i++) {
            if ($question->answer[$i] !== '' && $question->clue[$i] !== '') {
                $answercount++;
            }
        }

        if ($answercount < 1) { // Check there are at lest 1 word for crossword.
            $result->error = get_string('notenoughwords', 'qtype_crossword', '1');
            return $result;
        }

        // Insert all the new words.
        for ($i = 0; $i < $numwords; $i++) {
            if (trim($question->answer[$i]) === '' || trim($question->clue[$i]['text']) === '') {
                continue;
            }
            // Update an existing word if possible.
            $word = array_shift($oldwords);
            if (!$word) {
                $word = new stdClass();
                $word->questionid = $question->id;
                $word->answer = '';
                $word->clue = '';
                $word->clueformat = FORMAT_HTML;
                $word->orientation = 0;
                $word->startrow = 0;
                $word->startcolumn = 0;
                $word->feedback = '';
                $word->feedbackformat = FORMAT_HTML;
                $word->id = $DB->insert_record('qtype_crossword_words', $word);
            }
            $word->answer = trim(mb_strtoupper($question->answer[$i]));
            if (isset($question->feedback[$i])) {
                $word->feedback = $this->import_or_save_files($question->feedback[$i],
                    $context, 'question', 'feedback', $word->id);
                $word->feedbackformat = $question->feedback[$i]['format'];
            }
            if (isset($question->clue[$i])) {
                $word->clue = $this->import_or_save_files($question->clue[$i],
                    $context, 'question', 'clue', $word->id);
                $word->clueformat = $question->clue[$i]['format'];
            }
            $word->orientation = $question->orientation[$i];
            $word->startrow = $question->startrow[$i];
            $word->startcolumn = $question->startcolumn[$i];
            $DB->update_record('qtype_crossword_words', $word);
        }
        // Remove remain words.
        $fs = get_file_storage();
        if ($oldwords) {
            $ids = array_map(function($word){
                return $word->id;
            }, $oldwords);
            list($idssql, $idsparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_QM);
            $fs->delete_area_files_select($context->id, 'question', 'feedback', "id $idssql", $idsparams);
            $fs->delete_area_files_select($context->id, 'question', 'clue', "id $idssql", $idsparams);
            $DB->delete_records_select('qtype_crossword_words', "id $idssql", $idsparams);
        }
        $options = $DB->get_record('qtype_crossword_options', ['questionid' => $question->id]);
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $question->id;
            $options->correctfeedback = '';
            $options->partiallycorrectfeedback = '';
            $options->incorrectfeedback = '';
            $options->numrows = 10;
            $options->numcolumns = 10;
            $options->accentgradingtype = self::ACCENT_GRADING_STRICT;
            $options->accentpenalty = 0.5;
            $options->id = $DB->insert_record('qtype_crossword_options', $options);
        }

        $options->numrows = $question->numrows;
        $options->numcolumns = $question->numcolumns;
        $options->accentgradingtype = $question->accentgradingtype;
        $options->accentpenalty = $question->accentpenalty ?? 0.5;
        $options = $this->save_combined_feedback_helper($options, $question, $context, true);
        $DB->update_record('qtype_crossword_options', $options);
        $this->save_hints($question, true);
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_crossword_options', ['questionid' => $questionid]);
        $DB->delete_records('qtype_crossword_words', ['questionid' => $questionid]);
        parent::delete_question($questionid, $contextid);
    }

    protected function make_hint($hint) {
        return question_hint_with_parts::load_from_record($hint);
    }

    protected function initialise_question_instance($question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $this->initialise_combined_feedback($question, $questiondata, true);
        foreach ($questiondata->options->words as $answer) {
            $question->answers[] = new \qtype_crossword\answer(
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
        $question->numrows = (int) $questiondata->options->numrows;
        $question->numcolumns = (int) $questiondata->options->numcolumns;
        $question->accentgradingtype = $questiondata->options->accentgradingtype;
        $question->accentpenalty = (float) $questiondata->options->accentpenalty;
    }

    public function export_to_xml($question, qformat_xml $format, $extra = null): string {
        $expout = parent::export_to_xml($question, $format, $extra);
        $expout .= '    <numrows>' . $format->xml_escape($question->options->numrows) . "</numrows>\n";
        $expout .= '    <numcolumns>' . $format->xml_escape($question->options->numcolumns) . "</numcolumns>\n";
        $expout .= '    <accentgradingtype>' . $format->xml_escape($question->options->accentgradingtype)
            . "</accentgradingtype>\n";
        $expout .= '    <accentpenalty>' . $format->xml_escape($question->options->accentpenalty)
            . "</accentpenalty>\n";
        $fs = get_file_storage();
        foreach ($question->options->words as $word => $value) {
            $expout .= "    <word>\n";
            foreach (self::WORD_FIELDS as $xmlfield) {
                if ($xmlfield === 'clue' || $xmlfield === 'feedback') {
                    if (!isset($value->{$xmlfield})) {
                        $value->{$xmlfield} = '';
                    }
                    $formatfield = $xmlfield . 'format';
                    if (!isset($value->{$formatfield})) {
                        $value->{$formatfield} = FORMAT_HTML;
                    }
                    $files = $fs->get_area_files($question->contextid, 'question', $xmlfield, $value->id);
                    $expout .= "      <{$xmlfield} {$format->format($value->{$formatfield})}>\n";
                    $expout .= '        ' . $format->writetext($value->{$xmlfield});
                    $expout .= $format->write_files($files);
                    $expout .= "      </{$xmlfield}>\n";
                } else {
                    $exportedvalue = $format->xml_escape($value->{$xmlfield});
                    $expout .= "      <$xmlfield>{$exportedvalue}</$xmlfield>\n";
                }
            }
            $expout .= "    </word>\n";
        }
        $expout .= $format->write_combined_feedback($question->options, $question->id, $question->contextid);
        return $expout;
    }

    public function import_from_xml($data, $question, qformat_xml $format, $extra = null): ?object {
        if (!isset($data['#']['word'])) {
            return null;
        }
        $question = $format->import_headers($data);
        $question->qtype = 'crossword';
        $question->numrows = $format->getpath($data, ['#', 'numrows', 0, '#'], '', true);
        $question->numcolumns = $format->getpath($data, ['#', 'numcolumns', 0, '#'], '', true);
        $question->accentgradingtype = $format->getpath($data, ['#', 'accentgradingtype', 0, '#'], '', true);
        $question->accentpenalty = $format->getpath($data, ['#', 'accentpenalty', 0, '#'], '', true);
        foreach ($data['#']['word'] as $word) {
            foreach (self::WORD_FIELDS as $field) {
                if ($field === 'clue' || $field === 'feedback') {
                    if (isset($word['#'][$field][0])) {
                        $question->{$field}[] = $format->import_text_with_files($word['#'][$field][0], [], '',
                            $question->questiontextformat);
                    }
                } else {
                    $question->{$field}[] = $format->getpath($word, ['#', $field, 0, '#'], '', true);
                }
            }
        }
        $format->import_combined_feedback($question, $data);
        $format->import_hints($question, $data, true, false, $format->get_format($question->questiontextformat));
        return $question;
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);

        $words = $DB->get_records_menu('qtype_crossword_words',
                ['questionid' => $questionid], 'id', 'id,1');
        foreach ($words as $wordid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid,
                    $newcontextid, 'qtype_crossword', 'clue', $wordid);
            $fs->move_area_files_to_new_context($oldcontextid,
                    $newcontextid, 'qtype_crossword', 'feedback', $wordid);
        }

        $this->move_files_in_combined_feedback($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);

        $words = $DB->get_records_menu('qtype_crossword_words',
                ['questionid' => $questionid], 'id', 'id,1');
        foreach ($words as $wordid => $notused) {
            $fs->delete_area_files($contextid, 'qtype_crossword', 'clue', $wordid);
            $fs->delete_area_files($contextid, 'qtype_crossword', 'feedback', $wordid);
        }

        $this->delete_files_in_combined_feedback($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }
}
