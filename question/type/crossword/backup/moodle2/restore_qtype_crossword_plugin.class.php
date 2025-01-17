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
 * Restore plugin class that provides the necessary information needed to restore one crossword qtype plugin.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_crossword_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level.
     */
    protected function define_question_plugin_structure(): array {

        $paths = [];

        // We used get_recommended_name() so this works.
        $elements = [
            'qtype_crossword' => '/crossword',
            'qtype_crossword_word' => '/words/word'
        ];

        foreach ($elements as $elename => $path) {
            $elepath = $this->get_pathfor($path);
            $paths[] = new restore_path_element($elename, $elepath);
        }

        return $paths; // And we return the interesting paths.
    }

    /**
     *
     * Process the qtype_crossword element.
     *
     * @param array $data
     */
    public function process_qtype_crossword(array $data): void {
        self::process_qtype_crossword_data_with_table_name($data, 'qtype_crossword_options');
    }

    /**
     *
     * Process the qtype_crossword_words element.
     *
     * @param array $data
     */
    public function process_qtype_crossword_word(array $data): void {
        if (!isset($data['clueformat'])) {
            $data['clueformat'] = FORMAT_HTML;
        }
        if (!isset($data['feedbackformat'])) {
            $data['feedbackformat'] = FORMAT_HTML;
        }
        self::process_qtype_crossword_data_with_table_name($data, 'qtype_crossword_words');
    }

    /**
     * Process the qtype crossword data with the table name.
     *
     * @param array $data XML data.
     * @param string $tablename Table name
     */
    private function process_qtype_crossword_data_with_table_name(array $data, string $tablename): void {
        global $DB;
        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $questioncreated = $this->get_mappingid('question_created',
            $this->get_old_parentid('question')) ? true : false;

        // If the question has been created by restore, we need to create its question_crossword too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->questionid = $this->get_new_parentid('question');
            // Insert record.
            $newitemid = $DB->insert_record($tablename, $data);
            // Create mapping.
            $this->set_mapping($tablename, $oldid, $newitemid);
        }
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder.
     */
    public static function define_decode_contents(): array {
        $contents = [];

        $contents[] = new restore_decode_content('qtype_crossword_options',
            ['correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'], 'qtype_crossword_options');
        $contents[] = new restore_decode_content('qtype_crossword_words', ['clue', 'feedback'], 'qtype_crossword_words');

        return $contents;
    }
}
