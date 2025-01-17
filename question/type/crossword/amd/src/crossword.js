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
 * JavaScript to make crossword question.
 *
 * @module qtype_crossword/crossword
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {CrosswordGrid} from 'qtype_crossword/crossword_grid';

/**
 * Get list of words object from moodle form to display in the preview section.
 *
 * @return {array} List of the words object. E.g: {answer: PARIS, clue: sample clue, no: word number in the form}
 */
const getWordsFromForm = function() {
    const alphaRegex = /^[a-z]+/;
    const numberAnswer = document.querySelectorAll('[id^="fitem_id_answer"]').length;
    let words = [];

    if (numberAnswer > 0) {
        for (let no = 0; no < numberAnswer; no++) {
            const coordinateEl = document.querySelector('#fgroup_id_coodinateoptions_' + no);
            const answerEl = document.querySelector('#fitem_id_answer_' + no);
            const clueEl = document.querySelector('#fitem_id_clue_' + no);
            let word = {};
            word.no = no + 1;

            if (!coordinateEl || !answerEl || !clueEl) {
                continue;
            }

            coordinateEl.querySelectorAll('select').forEach(selectEl => {
                const name = selectEl.name.match(alphaRegex)?.pop();
                word[name] = selectEl.selectedIndex;
            });

            word.answer = answerEl.querySelector('input[id^="id_answer"]').value.normalize('NFKC');
            let clueData = clueEl.querySelector('textarea[id^="id_clue_"]').value.trim();
            // If it is a HTML empty content, set clue to empty.
            if (htmlIsEmpty(clueData)) {
                clueData = '';
            }
            word.clue = clueData;
            words.push(word);
        }
    }

    return words;
};

const EMPTY_EDITOR_CONTENT = [
    // For FF and Chrome.
    '<p></p>',
    '<p><br></p>',
    '<br>',
    '<p dir="rtl" style="text-align: right;"></p>',
    '<p dir="rtl" style="text-align: right;"><br></p>',
    '<p dir="ltr" style="text-align: left;"></p>',
    '<p dir="ltr" style="text-align: left;"><br></p>',
    // For IE 9 and 10.
    '<p>&nbsp;</p>',
    '<p><br>&nbsp;</p>',
    '<p dir="rtl" style="text-align: right;">&nbsp;</p>',
    '<p dir="rtl" style="text-align: right;"><br>&nbsp;</p>',
    '<p dir="ltr" style="text-align: left;">&nbsp;</p>',
    '<p dir="ltr" style="text-align: left;"><br>&nbsp;</p>'
];

/**
 * Check the HTML content is empty or not.
 *
 * @param {String} htmlContent HTML content include tags. E.g: <p>Sample html</p>
 * @return {boolean} return true if HTML content is consider empty.
 */
export const htmlIsEmpty = (htmlContent) => {
    return EMPTY_EDITOR_CONTENT.includes(htmlContent);
};

/**
 * Handle action attempt crossword.
 *
 * @param {Object} options The crossword settings.
 */
export const attempt = (options) => {
    const crossword = new CrosswordGrid(options);
    crossword.buildCrossword();
};

/**
 * Handle action preview crossword.'
 *
 * @param {Object} options The crossword settings.
 */
export const preview = (options) => {
    const element = document.querySelector(options.element);
    if (element) {
        element.removeAttribute('disabled');
        element.addEventListener('click', function(event) {
            event.preventDefault();
            const columnEl = document.querySelector('select[name="numcolumns"]');
            const rowEl = document.querySelector('select[name="numrows"]');
            const words = getWordsFromForm(options.target);
            const settings = {...options,
                words,
                colsNum: columnEl.options[columnEl.selectedIndex].text,
                rowsNum: rowEl.options[rowEl.selectedIndex].text
            };
            const crossword = new CrosswordGrid(settings);
            crossword.previewCrossword();
        });
    }
};
