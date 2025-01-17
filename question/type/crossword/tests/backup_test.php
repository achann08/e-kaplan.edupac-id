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

use qtype_crossword;
use qtype_crossword_test_helper;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");
require_once($CFG->dirroot . '/question/type/crossword/questiontype.php');
require_once($CFG->dirroot . '/question/type/crossword/tests/helper.php');

/**
 * Unit tests for backup/restore process in crossword qtype.
 *
 * @package qtype_crossword
 * @copyright 2023 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \restore_qtype_crossword_plugin
 * @covers \backup_qtype_crossword_plugin
 */
class backup_test extends \restore_date_testcase {

    /**
     * Load required libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/backup/util/includes/restore_includes.php");
    }

    /**
     * Restore the crossword backup file in the fixture folder base on filemame.
     *
     * @param string $filename backup file name in the fixture folder.
     * @param string $coursefullname course full name to be restored.
     * @param string $courseshortname course short name to be restored.
     */
    protected function restore_crossword_question_backup_file_to_course_shortname(
            string $filename, string $coursefullname, string $courseshortname): void {
        global $DB, $USER;
        $testfixture = __DIR__ . '/fixtures/' . $filename;

        // Extract our test fixture, ready to be restored.
        $backuptempdir = 'qtype_crossword';
        $backuppath = make_backup_temp_directory($backuptempdir);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname($testfixture, $backuppath);
        // Do the restore to new course with default settings.
        $categoryid = $DB->get_field('course_categories', 'MIN(id)', []);
        $courseid = \restore_dbops::create_new_course($coursefullname, $courseshortname, $categoryid);

        $controller = new \restore_controller($backuptempdir, $courseid, \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
            \backup::TARGET_NEW_COURSE);

        $controller->execute_precheck();
        $controller->execute_plan();
        $controller->destroy();
    }

    /**
     * Data provider for test_cw_backup_data().
     *
     * @coversNothing
     * @return array
     */
    public function test_cw_backup_data_provider(): array {

        return [
            'before upgrade feedback column' => [
                'filename' => 'crossword_pre_feedback_upgrade.mbz',
                'coursefullname' => 'before upgrade feedback column',
                'courseshortname' => 'bufc',
                'questionname' => 'crossword-001',
                'words' => [
                    [
                        'clue' => 'where is the Christ the Redeemer statue located in?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'BRAZIL'
                    ],
                    [
                        'clue' => 'Eiffel Tower is located in?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'PARIS'
                    ],
                    [
                        'clue' => 'Where is the Leaning Tower of Pisa?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'ITALY'
                    ]
                ],
                'version' => 4,
            ],
            'after upgrade feedback column' => [
                'filename' => 'crossword_after_feedback_upgrade.mbz',
                'coursefullname' => 'after upgrade feedback column',
                'courseshortname' => 'aufc',
                'questionname' => 'crossword-001',
                'words' => [
                    [
                        'clue' => '<p>where is the Christ the Redeemer statue located in?</p>',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => '<p dir="ltr" style="text-align: left;">You are correct.</p>',
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'BRAZIL'
                    ],
                    [
                        'clue' => '<p>Eiffel Tower is located in?</p>',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => '<p dir="ltr" style="text-align: left;">You are correct.<br></p>',
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'PARIS'
                    ],
                    [
                        'clue' => '<p>Where is the Leaning Tower of Pisa?</p>',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => '<p dir="ltr" style="text-align: left;">You are correct.<br></p>',
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'ITALY'
                    ]
                ],
                'version' => 4,
            ],
            'before upgrade feedback column 3.11' => [
                'filename' => 'crossword_before_feedback_upgrade 3.11.mbz',
                'coursefullname' => 'before upgrade feedback column 3.11',
                'courseshortname' => 'bufc311',
                'questionname' => 'crossword-001',
                'words' => [
                    [
                        'clue' => 'where is the Christ the Redeemer statue located in?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'BRAZIL'
                    ],
                    [
                        'clue' => 'Eiffel Tower is located in?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'PARIS'
                    ],
                    [
                        'clue' => 'Where is the Leaning Tower of Pisa?',
                        'clueformat' => FORMAT_HTML,
                        'feedback' => null,
                        'feedbackformat' => FORMAT_HTML,
                        'answer' => 'ITALY'
                    ]
                ],
                'version' => 3,
            ],
        ];
    }

    /**
     * Test crossword old backup data
     *
     * @dataProvider test_cw_backup_data_provider
     * @param string $filename file name of the backup file.
     * @param string $coursefullname course full name.
     * @param string $courseshortname course short name
     * @param string $questionname question name to check after restore.
     * @param array $expectedwords word data to be checked after restore.
     * @param int $version skip the test if backup version higher than current major version.
     */
    public function test_cw_backup_data(string $filename, string $coursefullname, string $courseshortname,
        string $questionname, array $expectedwords, int $version): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        // The sample backup files used in this test can only be restored into the same
        // or later Moodle versions, so skip the test if necessary.
        if (self::get_moodle_version_major() < $version) {
            $this->markTestSkipped();
        }
        // Check question with question name is not exist before restore.
        $this->assertFalse($DB->record_exists('question', ['name' => $questionname]));

        $this->restore_crossword_question_backup_file_to_course_shortname(
                $filename, $coursefullname, $courseshortname);
        // Assume there is only one crossword question in the DB after restore.
        $questionid = $DB->get_field('question', 'id', ['qtype' => 'crossword'], MUST_EXIST);
        $q = \question_bank::load_question($questionid);
        $qtype = new qtype_crossword();
        $qtype->get_question_options($q);
        // Verify question exist after restore and question word options is correct.
        $this->assertEquals($questionname, $q->name);
        $count = 0;
        foreach ($q->options->words as $word) {
            $this->assertEquals($expectedwords[$count]['clue'], $word->clue);
            $this->assertEquals($expectedwords[$count]['clueformat'], $word->clueformat);
            $this->assertEquals($expectedwords[$count]['feedback'], $word->feedback);
            $this->assertEquals($expectedwords[$count]['feedbackformat'], $word->feedbackformat);
            $this->assertEquals($expectedwords[$count]['answer'], $word->answer);
            $count++;
        }
    }


    /**
     * Test backup/restore question type crossword.
     *
     * @dataProvider test_backup_restore_course_with_cw_provider
     * @param string $crosswordtemplate Crossword template.
     */
    public function test_backup_restore_course_with_cw(string $crosswordtemplate) {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $crosswordformdata = \test_question_maker::get_question_form_data('crossword', $crosswordtemplate);
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(['contextid' => \context_course::instance($course->id)->id]);
        $crosswordformdata->category = "{$qcat->id},{$qcat->contextid}";
        $question = $qgen->create_question('crossword', null, (array) $crosswordformdata);
        $cwquestion = \question_bank::load_question($question->id);
        $this->backup_and_restore($course);
        $this->assertEquals(2, $DB->count_records('question', ['name' => $crosswordformdata->name]));

        // Delete the old course.
        delete_course($course, false);

        // Get new question.
        $newquestion = $DB->get_records('question', ['name' => $crosswordformdata->name], 'id');
        $this->assertEquals(1, count($newquestion));
        $newcwquestion = \question_bank::load_question(array_pop($newquestion)->id);

        $this->assertEquals($crosswordformdata->name, $newcwquestion->name);
        $this->assertEquals($cwquestion->questiontext, $newcwquestion->questiontext);
        $this->assertEquals($cwquestion->correctfeedback, $newcwquestion->correctfeedback);
        $this->assertEquals($crosswordformdata->accentgradingtype, $newcwquestion->accentgradingtype);
        $this->assertEqualsWithDelta($crosswordformdata->accentpenalty, $newcwquestion->accentpenalty,
            \question_testcase::GRADE_DELTA);

        for ($i = 0; $i < count($newcwquestion->answers); $i++) {
            $this->assertEquals($cwquestion->answers[$i]->answer, $newcwquestion->answers[$i]->answer);
            $this->assertEquals($cwquestion->answers[$i]->clue, $newcwquestion->answers[$i]->clue);
            $this->assertEquals($cwquestion->answers[$i]->clueformat, $newcwquestion->answers[$i]->clueformat);
            $this->assertEquals($cwquestion->answers[$i]->orientation, $newcwquestion->answers[$i]->orientation);
            $this->assertEquals($cwquestion->answers[$i]->startrow, $newcwquestion->answers[$i]->startrow);
            $this->assertEquals($cwquestion->answers[$i]->startcolumn, $newcwquestion->answers[$i]->startcolumn);
            $this->assertEquals($cwquestion->answers[$i]->feedback, $newcwquestion->answers[$i]->feedback);
            $this->assertEquals($cwquestion->answers[$i]->feedbackformat, $newcwquestion->answers[$i]->feedbackformat);
        }
    }

    /**
     * Data provider for test_backup_restore_course_with_cw().
     *
     * @coversNothing
     * @return array
     */
    public function test_backup_restore_course_with_cw_provider(): array {

        return [
            'Normal crossword' => [
                'template' => 'normal',
            ],
            'Crossword with accent grade type is strict' => [
                'template' => 'not_accept_wrong_accents',
            ],
            'Crossword with accent grade type is penalty' => [
                'template' => 'accept_wrong_accents_but_subtract_point',
            ],
            'Crossword with accent grade type is ignore' => [
                'template' => 'accept_wrong_accents_but_not_subtract_point',
            ],
        ];
    }

    /**
     * Get moodle version.
     *
     * @return int major moodle version number.
     */
    private static function get_moodle_version_major(): int {
        global $CFG;
        $versionarray = explode('.', $CFG->release);
        return (int) $versionarray[0];
    }
}
