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
 * Unit tests for the crossword question_type definition class
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_crossword;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Unit tests for the crossword question_type definition class
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_type_test extends \question_testcase {
    /** @var \qtype_crossword instance of the question type class to test. */
    protected $qtype;

    protected function setUp(): void {
        $this->qtype = \question_bank::get_qtype('crossword');
    }

    /**
     * Test the export function.
     *
     * @covers \qtype_crossword::export_to_xml
     */
    public function test_export_to_xml(): void {
        $qdata =
            (object)[
                'id' => '8862',
                'category' => '1299,2005',
                'parent' => '0',
                'name' => 'Test crossword',
                'questiontext' => '<p dir="ltr" style="text-align: left;">Crossword question text</p>',
                'questiontextformat' => '1',
                'generalfeedback' => '<p dir="ltr" style="text-align: left;">Crossword general feedback<br></p>',
                'generalfeedbackformat' => '1',
                'penalty' => 0.3333333,
                'defaultmark' => 1,
                'qtype' => 'crossword',
                'length' => '1',
                'stamp' => 'localhost+220715023425+tJIpOv',
                'version' => 'localhst+191209172943+f8G7pL',
                'hidden' => '0',
                'timecreated' => '1657852465',
                'timemodified' => '1657852465',
                'createdby' => '2',
                'modifiedby' => '2',
                'idnumber' => null,
                'options' =>
                    (object)[
                        'id' => '126',
                        'questionid' => '8862',
                        'correctfeedback' => 'Your answer is correct.',
                        'correctfeedbackformat' => '1',
                        'partiallycorrectfeedback' => 'Your answer is partially correct.',
                        'partiallycorrectfeedbackformat' => '1',
                        'incorrectfeedback' => 'Your answer is incorrect.',
                        'incorrectfeedbackformat' => '1',
                        'numrows' => 0,
                        'numcolumns' => 0,
                        'accentgradingtype' => 'penalty',
                        'accentpenalty' => 0.2,
                        'shownumcorrect' => 1,
                        'words' => [
                            (object)[
                                'id' => 1,
                                'questionid' => 8862,
                                'answer' => 'AAA',
                                'clue' => 'Clue 1',
                                'clueformat' => FORMAT_HTML,
                                'orientation' => 0,
                                'startrow' => 0,
                                'startcolumn' => 0,
                                'feedback' => '<b>Feedback data</b>',
                                'feedbackformat' => FORMAT_PLAIN,
                            ] ,
                            (object)[
                                'id' => 2,
                                'questionid' => 8862,
                                'answer' => 'BBB',
                                'clue' => 'Clue 2',
                                'clueformat' => FORMAT_HTML,
                                'orientation' => 0,
                                'startrow' => 1,
                                'startcolumn' => 0,
                            ] ,
                            (object)[
                                'id' => 2,
                                'questionid' => 8862,
                                'answer' => 'CCC',
                                'clue' => 'Clue 3',
                                'clueformat' => FORMAT_HTML,
                                'orientation' => 0,
                                'startrow' => 2,
                                'startcolumn' => 0,
                            ] ,
                        ]
                    ],
                'hints' => [
                    (object)[
                        'id' => 1,
                        'questionid' => 8862,
                        'hint' => 'Hint 1',
                        'hintformat' => FORMAT_HTML,
                        'shownumcorrect' => 0,
                        'clearwrong' => 0
                    ],
                    (object)[
                        'id' => 2,
                        'questionid' => 8862,
                        'hint' => 'Hint 2',
                        'hintformat' => FORMAT_HTML,
                        'shownumcorrect' => 1,
                        'clearwrong' => 1
                    ],
                ],
                'returnurl' => '/question/edit.php?courseid=35&cat=1299%2C2005&recurse=1&showhidden=1&qbshowtext=0',
                'makecopy' => 0,
                'courseid' => '35',
                'inpopup' => 0,
                'contextid' => 91
            ];

        $exporter = new \qformat_xml();
        $xml = $exporter->writequestion($qdata);
        $expectedxml =
            '<!-- question: 8862  -->
  <question type="crossword">
    <name>
      <text>Test crossword</text>
    </name>
    <questiontext format="html">
      <text><![CDATA[<p dir="ltr" style="text-align: left;">Crossword question text</p>]]></text>
    </questiontext>
    <generalfeedback format="html">
      <text><![CDATA[<p dir="ltr" style="text-align: left;">Crossword general feedback<br></p>]]></text>
    </generalfeedback>
    <defaultgrade>1</defaultgrade>
    <penalty>0.3333333</penalty>
    <hidden>0</hidden>
    <idnumber></idnumber>
    <numrows>0</numrows>
    <numcolumns>0</numcolumns>
    <accentgradingtype>penalty</accentgradingtype>
    <accentpenalty>0.2</accentpenalty>
    <word>
      <answer>AAA</answer>
      <clue format="html">
        <text>Clue 1</text>
      </clue>
      <orientation>0</orientation>
      <startrow>0</startrow>
      <startcolumn>0</startcolumn>
      <feedback format="plain_text">
        <text><![CDATA[<b>Feedback data</b>]]></text>
      </feedback>
    </word>
    <word>
      <answer>BBB</answer>
      <clue format="html">
        <text>Clue 2</text>
      </clue>
      <orientation>0</orientation>
      <startrow>1</startrow>
      <startcolumn>0</startcolumn>
      <feedback format="html">
        <text></text>
      </feedback>
    </word>
    <word>
      <answer>CCC</answer>
      <clue format="html">
        <text>Clue 3</text>
      </clue>
      <orientation>0</orientation>
      <startrow>2</startrow>
      <startcolumn>0</startcolumn>
      <feedback format="html">
        <text></text>
      </feedback>
    </word>
    <correctfeedback format="html">
      <text>Your answer is correct.</text>
    </correctfeedback>
    <partiallycorrectfeedback format="html">
      <text>Your answer is partially correct.</text>
    </partiallycorrectfeedback>
    <incorrectfeedback format="html">
      <text>Your answer is incorrect.</text>
    </incorrectfeedback>
    <shownumcorrect/>
    <hint format="html">
      <text>Hint 1</text>
    </hint>
    <hint format="html">
      <text>Hint 2</text>
      <shownumcorrect/>
      <clearwrong/>
    </hint>
  </question>
';

        // Hack so the test passes in both 3.5 and 3.6.
        if (strpos($xml, 'idnumber') === false) {
            $expectedxml = str_replace("    <idnumber></idnumber>\n", '', $expectedxml);
        }

        // Hack so the test passes in both 3.8 and 3.9.
        if (strpos($xml, 'showstandardinstruction') === false) {
            $expectedxml = str_replace("    <showstandardinstruction>1</showstandardinstruction>\n", '', $expectedxml);
        }

        $this->assertEquals($expectedxml, $xml);
    }
}
