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
 * Plugin strings are defined here.
 *
 * @package qtype_crossword
 * @copyright 2022 The Open University
 * @license  https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accentgradingignore'] = 'Just grade the letters and ignore any accents';
$string['accentgradingpenalty'] = 'Partial mark if the letters are correct but one or more accents are wrong';
$string['accentgradingstrict'] = 'Accented letters must completely match or the answer is wrong';
$string['accentletters'] = 'Accented letters';
$string['accentpenalty'] = 'Grade for answers with incorrect accents';
$string['across'] = 'Across';
$string['addmorewordblanks'] = 'Blanks for {no} more words';
$string['answer'] = 'Answer';
$string['answeroptions'] = 'Answer options';
$string['celltitle'] = 'Row {row}, Column {column}. {number} {orientation}. {clue}, letter {letter} of {count}';
$string['clue'] = 'Clue';
$string['correctansweris'] = 'The correct answer is: {$a}';
$string['correctanswersare'] = 'The correct answers are: {$a}';
$string['down'] = 'Down';
$string['inputlabel'] = '{$a->number} {$a->orientation}. {$a->clue} Answer length {$a->length}';
$string['mustbealphanumeric'] = 'The answer must be alphanumeric characters only';
$string['notenoughwords'] = 'This type of question requires at least {$a} word';
$string['numberofcolumns'] = 'Number of columns';
$string['numberofrows'] = 'Number of rows';
$string['orientation'] = 'Orientation';
$string['overflowposition'] = 'The word start or end position is outside the defined grid size.';
$string['pleaseananswerallparts'] = 'Please answer all parts of the question.';
$string['pleaseenterclueandanswer'] = 'You must enter both answer and clue for word {$a}.';
$string['pluginname'] = 'Crossword';
$string['pluginname_help'] = 'A simple text-based crossword question. Currently requires manual design of the word grid.';
$string['pluginnameadding'] = 'Adding a Crossword question';
$string['pluginnameediting'] = 'Editing a Crossword question';
$string['pluginnamesummary'] = 'A simple text-based crossword question. Currently requires manual design of the word grid.';
$string['preview'] = 'Preview';
$string['privacy:metadata'] = 'The Crossword plugin does not store any personal data.';
$string['refresh'] = 'Refresh preview';
$string['startcolumn'] = 'Column index';
$string['startrow'] = 'Row index';
$string['updateform'] = 'Update the form';
$string['wordhdrhelper_help'] = '<p>As the crossword is generated from the word list, you can either generate a single crossword layout for all users, or use the \'Shuffle crossword layout on new attempt\' option to generate a new layout for each new attempt per student (word combinations allowing).</p>
<p>Add your words and clues using the text fields. If you want a specific word fixed on the grid, tick \'Fix word on grid\' and specify its orientation and placement.</p>
<p>Most characters are supported in this question type, from A-Z, 0-9, diacritics and currency symbols etc. Any curly quotation marks or apostrophes will be converted or interpreted as \'straight\' versions for ease of input and auto-marking.</p>
<p>Add more words by selecting the \'Blanks for 3 more words\' button. Any blank words will be removed when the question is saved.</p>';
$string['wordno'] = 'Word {$a}';
$string['words'] = 'Words';
$string['words_help'] = 'Helping';
$string['wrongadjacentcharacter'] = 'Two or more consecutive new word breaks detected. Please use a maximum of one between individual words. Note that this does not limit the number of new words in the answer itself.';
$string['wrongintersection'] = 'The letter at the intersection of two words do not match. The word cannot be placed here.';
$string['wrongpositionhyphencharacter'] = 'Please do not add a hyphen before or after the last alphanumeric character.';
$string['wrongpositionspacecharacter'] = 'Please do not add a space before or after the last alphanumeric character.';
$string['yougotnright'] = '{$a->num} of your answers are correct.';
$string['yougot1right'] = '1 of your answers is correct.';
