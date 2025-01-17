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
 * Provides the information to backup crossword questions.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_qtype_crossword_plugin extends backup_qtype_plugin {

    /**
     * Returns the qtype information to attach to question element.
     */
    protected function define_question_plugin_structure(): backup_plugin_element {

        // Define the virtual plugin element with the condition to fulfill.
        $plugin = $this->get_plugin_element(null, '../../qtype', 'crossword');

        // Create one standard named plugin element (the visible container).
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect the visible container ASAP.
        $plugin->add_child($pluginwrapper);

        // Now create the qtype own structures.
        $crossword = new backup_nested_element('crossword', ['id'], ['correctfeedback',
            'correctfeedbackformat', 'numrows', 'numcolumns', 'accentgradingtype', 'accentpenalty',
            'partiallycorrectfeedback', 'partiallycorrectfeedbackformat',
            'incorrectfeedback', 'incorrectfeedbackformat', 'shownumcorrect']);

        // Define the elements.
        $words = new backup_nested_element('words');
        $word = new backup_nested_element('word', ['id'], ['answer', 'clue', 'clueformat', 'orientation', 'startrow',
            'startcolumn', 'feedback', 'feedbackformat']);
        $words->add_child($word);
        $pluginwrapper->add_child($crossword);
        $pluginwrapper->add_child($words);

        // Set source to populate the data.
        $word->set_source_table('qtype_crossword_words', ['questionid' => backup::VAR_PARENTID]);
        $crossword->set_source_table('qtype_crossword_options', ['questionid' => backup::VAR_PARENTID]);

        return $plugin;
    }

    public static function get_qtype_fileareas() {
        return [
            'clue' => 'qtype_crossword_words',
            'feedback' => 'qtype_crossword_words'
        ];
    }
}
