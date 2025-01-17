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
 * Essay question type upgrade code.
 *
 * @package    qtype
 * @subpackage crossword
 * @copyright  2022 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the crossword question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_crossword_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022101000) {

        // Changing precision of field clue on table qtype_crossword_words to (1333).
        $table = new xmldb_table('qtype_crossword_words');
        $field = new xmldb_field('clue', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null, 'questionid');

        // Launch change of precision for field clue.
        $dbman->change_field_precision($table, $field);

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2022101000, 'qtype', 'crossword');
    }

    if ($oldversion < 2023011000) {

        // Define field feedback to be added to qtype_crossword_words.
        $table = new xmldb_table('qtype_crossword_words');
        $field = new xmldb_field('feedback', XMLDB_TYPE_TEXT, null, null, null, null, null, 'orientation');

        // Conditionally launch add field feedback.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023011000, 'qtype', 'crossword');
    }

    if ($oldversion < 2023011001) {

        // Changing type of field clue on table qtype_crossword_words to text.
        $table = new xmldb_table('qtype_crossword_words');
        $field = new xmldb_field('clue', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'questionid');

        // Launch change of type for field clue.
        $dbman->change_field_type($table, $field);

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023011001, 'qtype', 'crossword');
    }

    if ($oldversion < 2023011002) {

        // Define field clueformat to be added to qtype_crossword_words.
        $table = new xmldb_table('qtype_crossword_words');
        $field = new xmldb_field('clueformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'clue');

        // Conditionally launch add field clueformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023011002, 'qtype', 'crossword');
    }

    if ($oldversion < 2023011003) {

        // Define field feedbackformat to be added to qtype_crossword_words.
        $table = new xmldb_table('qtype_crossword_words');
        $field = new xmldb_field('feedbackformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'feedback');

        // Conditionally launch add field feedbackformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023011003, 'qtype', 'crossword');
    }

    if ($oldversion < 2023032900) {

        // Define field accentgradingtype to be added to qtype_crossword_options.
        $table = new xmldb_table('qtype_crossword_options');
        $field = new xmldb_field('accentgradingtype', XMLDB_TYPE_CHAR, '200',
            null, XMLDB_NOTNULL, null, 'strict', 'numcolumns');

        // Conditionally launch add field accentgradingtype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023032900, 'qtype', 'crossword');
    }

    if ($oldversion < 2023032901) {

        // Define field accentpenalty to be added to qtype_crossword_options.
        $table = new xmldb_table('qtype_crossword_options');
        $field = new xmldb_field('accentpenalty', XMLDB_TYPE_NUMBER, '12, 7',
            null, XMLDB_NOTNULL, null, '0.5', 'accentgradingtype');

        // Conditionally launch add field accentpenalty.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Crossword savepoint reached.
        upgrade_plugin_savepoint(true, 2023032901, 'qtype', 'crossword');
    }

    return true;
}
