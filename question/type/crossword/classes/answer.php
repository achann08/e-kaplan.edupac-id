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

namespace qtype_crossword;

/**
 * This defines a structured class to hold crossword question answers.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_crossword
 * @copyright 2021, The Open University
 */
class answer {

    // Class properties.

    /** @var int The answers id value. */
    public $answerid;

    /** @var string The answers value. */
    public $answer;

    /** @var string The clues value. */
    public $clue;

    /** @var int The clues format. E.g: FORMAT_HTML */
    public $clueformat;

    /** @var bool true = down, false = across. */
    public $orientation;

    /** @var string The startrow value. */
    public $startrow;

    /** @var string The startcolumn value. */
    public $startcolumn;

    /** @var string The feedback value. */
    public $feedback;

    /** @var int The feedback format. E.g: FORMAT_HTML */
    public $feedbackformat;

    /**
     * Answer constructor.
     * @param int $answerid
     * @param string $answer
     * @param string $clue
     * @param int $clueformat
     * @param string $orientation
     * @param string $startrow
     * @param string $startcolumn
     * @param null|string $feedback
     * @param null|int $feedbackformat
     */
    public function __construct(int $answerid, string $answer, string $clue, int $clueformat, string $orientation,
        string $startrow, string $startcolumn, ?string $feedback, ?int $feedbackformat) {
        $this->answerid = $answerid;
        $this->answer = util::safe_normalize($answer);
        $this->clue = $clue;
        $this->clueformat = $clueformat;
        $this->orientation = (bool) $orientation;
        $this->startrow = $startrow;
        $this->startcolumn = $startcolumn;
        $this->feedback = $feedback;
        $this->feedbackformat = $feedbackformat;
    }

    /**
     * Check the correctness of the answer,
     * Remove the underscore character with a space before comparing it.
     *
     * @param string $response The answer need to be checked, maybe contain underscore characters.
     * @return bool The result after check, True if correct.
     */
    public function is_correct(string $response): bool {
        return $this->answer === str_replace('_', ' ', $response);
    }

    /**
     * Check the input answer has the same letter but different accent, but is not completely right.
     *
     * @param string $response The answer need to be checked, maybe contain underscore characters.
     * @return bool The result after check, True if only different accent.
     */
    public function is_wrong_accents(string $response): bool {

        if ($this->is_correct($response)) {
            return false;
        }
        $answerinput = \qtype_crossword\util::remove_accent(str_replace('_', ' ', $response));
        $answerdata = \qtype_crossword\util::remove_accent($this->answer);

        return $answerinput === $answerdata;
    }

    /**
     * Generate answer length hint; e.g: With the answer: TIM BERNERS-LEE
     * the answer hint will be: 3, 7-3 and ignorecharcterindex will be ['space' => [3], 'hyphen' => [11]]
     * Besides that will return a list of special characters with the existing index in the answer
     *
     * @return array The list contains the hint and the index of special characters in the answer.
     */
    public function generate_answer_hint(): array {
        $count = 0;
        $answerhint = '';
        $ignorecharcterindex = [];
        // Allow space and hyphen only.
        $listspecialcharacters = ['space' => ' ', 'hyphen' => '-'];
        // Retrieve the answer length (answers that still contain spaces and hyphens).
        $length = \core_text::strlen($this->answer);
        // Loop the answer by letter.
        for ($index = 0; $index < $length; $index++) {
            // Get the answer's letter by index (include unicode characters).
            $letter = \core_text::substr($this->answer, $index, 1);
            // In case the character is a space or a hyphen, we need to handle it further.
            if (in_array($letter, array_values($listspecialcharacters))) {
                // Get type of the special character.
                // It should return 'space' or 'hyphen'.
                $character = array_search($letter, $listspecialcharacters);
                if ($character < -1) {
                    continue;
                }
                // Store index of special character.
                $ignorecharcterindex[$character][] = $index;
                // Prevents the value 0 when double spaces/hyphen exist.
                // E.g: The result should be 1, 2 instead of 1, 0, 2.
                if ($count > 0) {
                    // Generate answer hint.
                    // Replace space with comma.
                    $answerhint .= $count . str_replace(' ', ', ', $letter);
                }
                // Need to reset $count for the next letter.
                $count = 0;
                continue;
            }
            $count++;
            // In the last loop if there is still count we need to append.
            if ($index === $length - 1 && $count > 0) {
                $answerhint .= $count;
            }
        }
        return [$answerhint, $ignorecharcterindex];
    }
}
