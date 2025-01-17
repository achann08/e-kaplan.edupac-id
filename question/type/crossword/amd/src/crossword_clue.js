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
 * Crossword clue class, handle any action relative to clue.
 *
 * @module qtype_crossword/crossword_clue
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {CrosswordQuestion} from 'qtype_crossword/crossword_question';

export class CrosswordClue extends CrosswordQuestion {

    /**
     * Constructor.
     *
     * @param {Object} options The settings for crossword.
     */
    constructor(options) {
        super(options);
    }

    /**
     * Set up for clue section.
     */
    setUpClue() {
        let {words, readonly} = this.options;
        const clueEls = this.options.crosswordEl
            .closest('.qtype_crossword-grid-wrapper')
            .querySelectorAll('.contain-clue .wrap-clue');
        clueEls.forEach(el => {
            const questionId = el.dataset.questionid;
            let word = words.find(o => o.number === parseInt(questionId));
            if (word) {
                const inputEl = el.querySelector('input');
                // Retrieve special characters list.
                const ignoreIndexes = this.getIgnoreIndexByAnswerNumber(word.number, false);
                const wordString = this.makeUnderscore(word.length - inputEl.value.length);
                // Add special characters to the answer, then set it to the answer input.
                inputEl.value += this.mapAnswerAndSpecialLetter(wordString, ignoreIndexes[0]);
                if (!readonly) {
                    inputEl.disabled = false;
                }
                // Add event for input.
                this.addEventForClueInput(inputEl, word);
            }
        });
    }

    /**
     * Add event to word input element.
     *
     * @param {Element} el The input element.
     * @param {String} word The word data.
     */
    addEventForClueInput(el, word) {
        const {readonly} = this.options;
        let startSelection = 0;
        if (readonly) {
            return;
        }
        el.addEventListener('click', (e) => {
            const {words} = this.options;
            const wordNumber = e.target.closest('.wrap-clue').dataset.questionid;
            const wordObj = words.find(o => o.number === parseInt(wordNumber));
            let startIndex = e.target.selectionStart;
            if (startIndex >= word.length) {
                startIndex = word.length - 1;
            }
            // Based on the selected letter index on the answer index,
            // we will find the corresponding crossword cell index.
            startIndex = this.findCellIndexFromAnswerIndex(wordObj, startIndex);
            this.focusCellByStartIndex(startIndex, word);
            this.focusClue();
            this.setStickyClue();
        });

        el.addEventListener('focus', (e) => {
            e.target.dispatchEvent(new Event('click'));
        });
        el.addEventListener('beforeinput', (e) => {
            if (e.inputType === 'insertText' && e.data) {
                this.handleInsertedCharacterToElement(e, e.data);
            }
        });

        el.addEventListener('input', (e) => {
            if (e.inputType === 'deleteContentBackward') {
                this.handleAndSyncDeletedStringToElement(e.target, e.target.value);
            }
        });

        el.addEventListener('keypress', (e) => {
            e.preventDefault();
            this.handleInsertedCharacterToElement(e, e.key);
        });

        el.addEventListener('compositionstart', (evt) => {
            const selection = evt.target.selectionStart;
            startSelection = selection;
        });

        el.addEventListener('compositionend', (evt) => {
            evt.preventDefault();
            evt.stopPropagation();
            const {wordNumber} = this.options;
            const selection = evt.target.selectionStart;
            let key = evt.data.normalize('NFKC');
            let currentSelection = startSelection;
            evt.target.setSelectionRange(selection, selection);
            key.split('').forEach(char => {
                const result = this.handleTypingData(evt, wordNumber, word, currentSelection, char);
                if (result) {
                    currentSelection++;
                }
            });
        });

        el.addEventListener('keyup', (event) => {
            event.preventDefault();
            const {words, wordNumber} = this.options;
            const {key, target} = event;
            let {value} = target;
            let isValidKey = false;
            let maxLength = parseInt(target.getAttribute('maxlength'));
            if ([this.ARROW_LEFT, this.ARROW_RIGHT].includes(key)) {
                isValidKey = true;
                const word = words.find(o => o.number === parseInt(wordNumber));
                // Based on the selected letter index of the answer input,
                // we will find the cell with the corresponding letter index attribute.
                const startIndex = this.findCellIndexFromAnswerIndex(word, target.selectionStart, false);
                const gEl = this.options.crosswordEl
                    .querySelector(`g[data-word*='(${wordNumber})'][data-letterindex='${startIndex}']`);
                if (gEl) {
                    this.toggleHighlight(word, gEl);
                }
            }
            if (key === this.DELETE || key === this.BACKSPACE) {
                this.handleAndSyncDeletedStringToElement(target, value);
            }

            if (key === this.END || key === this.HOME || key === this.ARROW_UP || key === this.ARROW_DOWN) {
                isValidKey = true;
                let startIndex = 0;
                const word = words.find(o => o.number === parseInt(wordNumber));
                if (!word) {
                    return;
                }
                if (key === this.END || key === this.ARROW_DOWN) {
                    startIndex = word.length - 1;
                }
                this.syncFocusCellAndInput(target, startIndex);
            }

            if (!isValidKey && startSelection >= maxLength) {
                event.target.value = value.slice(0, maxLength);
            }
        });

        el.addEventListener('paste', (event) => {
            event.preventDefault();
            const {words, wordNumber} = this.options;
            const word = words.find(o => o.number === parseInt(wordNumber));
            let selection = event.target.selectionStart;
            let value = (event.clipboardData || window.clipboardData).getData('text');
            let ignoreIndexes = this.getIgnoreIndexByAnswerNumber(word.number);
            // Remove invalid characters, normarlize NFKC.
            value = this.replaceText(value).normalize('NFKC');
            if (value === '') {
                return;
            }
            let letterIndex = 1;
            value.split('').every(char => {
                // Stop function If the character overflows.
                if (letterIndex > word.length - ignoreIndexes.length) {
                    return false;
                }
                const result = this.handleTypingData(event, wordNumber, word, selection, char);
                letterIndex++;
                // Find the valid index.
                if (result) {
                    for (let index = selection + 1; index < word.length; index++) {
                        if (!ignoreIndexes.includes(index)) {
                            selection = index;
                            break;
                        }
                    }
                }
                return true;
            });
        });

        el.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key.toLowerCase() === this.Z_KEY) {
                e.preventDefault();
            }
            if (e.key === this.ENTER) {
                e.preventDefault();
            }
        });

        el.addEventListener('cut', (event) => {
            const selectString = document.getSelection().toString();
            const startIndex = event.target.selectionStart;
            let {value} = event.target;
            value = value.substring(0, startIndex) +
                value.substring(startIndex + selectString.length) +
                this.makeUnderscore(selectString.length);
            event.target.value = value;
            event.clipboardData.setData('text/plain', selectString);
            event.preventDefault();
            event.target.setSelectionRange(startIndex, startIndex);
            // In case the user cuts off the entire answer, we need to update the crossword grid.
            this.syncLettersByText(value, false);
        });
    }

    /**
     * Handle typing data.
     *
     * @param {Object} evt Event data.
     * @param {Number} wordNumber The word number.
     * @param {Object} word The word object.
     * @param {Number} selectionIndex The position of cursor selection.
     * @param {String} char The character.
     *
     * @return {Boolean} True if the data is valid.
     */
    handleTypingData(evt, wordNumber, word, selectionIndex, char) {
        const [count, gEl] = this.findTheClosestCell(wordNumber, word, selectionIndex);
        if (this.replaceText(char) === '') {
            return false;
        }
        if (gEl) {
            gEl.querySelector('text.crossword-cell-text').innerHTML = char.toUpperCase();
            this.bindDataToClueInput(gEl, char.toUpperCase());
        }

        // Go to next letter.
        const [letterIndex, nexEl] = this.findTheClosestCell(wordNumber, word, count + 1);
        if (nexEl) {
            this.toggleHighlight(word, nexEl);
            evt.target.setSelectionRange(letterIndex, letterIndex);
        }
        return true;
    }

    /**
     * Focus cell base on the start index.
     *
     * @param {Element} startIndex The start index.
     * @param {String} word The word data.
     */
    focusCellByStartIndex(startIndex, word) {
        let position = this.calculatePosition(word, startIndex);
        const rect = this.options.crosswordEl.querySelector(`g rect[x='${position.x}'][y='${position.y}']`);
        if (rect) {
            this.options.wordNumber = word.number;
            this.toggleHighlight(word, rect.closest('g'));
            this.updateLetterIndexForCells(word);
        }
    }

    /**
     * Focus crossword cell from the start index.
     *
     * @param {Element} target The element.
     * @param {Number} startIndex The start index.
     */
    syncFocusCellAndInput(target, startIndex) {
        const {wordNumber} = this.options;
        const gEl = this.options.crosswordEl.querySelector(`g[data-word*='(${wordNumber})'][data-letterindex='${startIndex}']`);
        target.setSelectionRange(startIndex, startIndex);
        if (gEl) {
            this.toggleFocus(gEl);
        }
    }

    /**
     * Toggle the focus cell.
     *
     * @param {Element} gEl The word letter.
     */
    toggleFocus(gEl) {
        const focused = this.options.crosswordEl.querySelector('g rect.crossword-cell-focussed');
        if (focused) {
            focused.classList.remove('crossword-cell-focussed');
            focused.classList.add('crossword-cell-highlighted');
        }
        gEl.querySelector('rect').classList.add('crossword-cell-focussed');
    }

    /**
     *
     * Add underscore to deleted string and sync it to crossword clue input.
     *
     * @param {Element} target The element target
     * @param {String} value the string input after we deleted single or multiples character.
     */
    handleAndSyncDeletedStringToElement(target, value) {
        const {words, wordNumber} = this.options;
        const word = words.find(o => o.number === parseInt(wordNumber));
        if (!word) {
            return;
        }
        let startIndex = target.selectionStart;
        const selectionLength = word.length - value.length;
        const underScore = this.makeUnderscore(selectionLength);
        // Insert underscore to deleted string.
        target.value = [value.slice(0, startIndex), underScore, value.slice(startIndex)].join('');
        // In case the user deletes the entire answer we need to update the crossword grid.
        this.syncLettersByText(target.value, false);
        this.syncFocusCellAndInput(target, startIndex);
    }

    /**
     * Insert the character to clue input.
     *
     * @param {Object} event Event data.
     * @param {String} value the character we are inserted to the clue input.
     */
    handleInsertedCharacterToElement(event, value) {
        const {words, wordNumber} = this.options;
        const word = words.find(o => o.number === parseInt(wordNumber));
        let startIndex = event.target.selectionStart;
        value = this.replaceText(value).normalize('NFKC');
        if (value === '') {
            return;
        }
        this.handleTypingData(event, wordNumber, word, startIndex, value);
    }
}
