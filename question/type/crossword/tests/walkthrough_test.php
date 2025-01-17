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
 * Unit tests for the crossword question_type.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    /**
     * Test attempt question with mode deferredfeedback.
     *
     * @covers \start_attempt_at_question
     */
    public function test_deferred_feedback(): void {

        // Create a normal crossword question.
        $q = \test_question_maker::make_question('crossword', 'normal');
        $this->start_attempt_at_question($q, 'deferredfeedback', 3);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Save in incomplete answer.
        $this->process_submission(['sub0' => '', 'sub1' => '', 'sub2' => 'ITALY']);

        // Verify.
        $this->check_current_state(\question_state::$invalid);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Save a correct answer.
        $this->process_submission(['sub0' => 'BRAZIL', 'sub1' => 'PARIS', 'sub2' => 'ITALY']);
        // Now submit all and finish.
        $this->finish();

        // Verify.
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
            $this->get_contains_mark_summary(3),
            $this->get_contains_general_feedback_expectation($q),
            $this->get_does_not_contain_validation_error_expectation());
    }

    /**
     * Test attempt question with mode interactive.
     *
     * @covers \start_attempt_at_question
     */
    public function test_interactive(): void {

        // Create a normal crossword question.
        $q = \test_question_maker::make_question('crossword', 'normal');
        $this->start_attempt_at_question($q, 'interactive', 3);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Save in incomplete answer.
        $this->process_submission(['sub0' => '', 'sub1' => '', 'sub2' => 'ITALY']);

        // Verify.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_current_output(
            $this->get_contains_marked_out_of_summary(),
            $this->get_does_not_contain_feedback_expectation(),
            $this->get_does_not_contain_validation_error_expectation());

        // Save a correct answer.
        $this->process_submission(['sub0' => 'BRAZIL', 'sub1' => 'PARIS', 'sub2' => 'ITALY']);
        // Now submit all and finish.
        $this->finish();

        // Verify.
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(3);
        $this->check_current_output(
            $this->get_contains_mark_summary(3),
            $this->get_contains_general_feedback_expectation($q),
            $this->get_does_not_contain_validation_error_expectation());
    }
}
