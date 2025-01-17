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
 * CrosswordGrid class handle every function relative to grid.
 *
 * @module qtype_crossword/crossword_grid
 * @copyright 2022 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {CrosswordQuestion} from 'qtype_crossword/crossword_question';
import {CrosswordClue} from './crossword_clue';

export class CrosswordGrid extends CrosswordQuestion {

    /**
     * Constructor.
     *
     * @param {Object} options The settings for crossword.
     */
    constructor(options) {
        super(options);
    }

    /**
     * Build the background table.
     */
    buildBackgroundTable() {
        let {colsNum, rowsNum, previewSetting} = this.options;
        let style = previewSetting;

        // Create table element.
        const tableEl = document.createElement('table');

        // Preview mode will add one more columns and row to add the coordinate helper.
        colsNum++;
        rowsNum++;

        tableEl.className = 'crossword-grid';
        // Set the background color.
        tableEl.style.backgroundColor = style.backgroundColor;

        for (let i = 0; i < rowsNum; i++) {
            const rowEl = document.createElement('tr');
            rowEl.className = 'grid-row';
            for (let j = 0; j < colsNum; j++) {
                // Create square.
                let squareEl = document.createElement('td');
                squareEl.className = 'grid-square';
                squareEl.style.borderColor = style.borderColor;
                squareEl.style.color = style.color;

                if (i === 0 && j === 0) {
                    squareEl.classList.add('cell-white');
                }

                // Adding alphanumeric.
                if (i === 0 && j !== 0) {
                    squareEl.innerText = this.getColumnLabel(j - 1);
                    squareEl.classList.add('square-indicate-horizontal');
                }
                if (i !== 0 && j === 0) {
                    squareEl.innerText = i;
                    squareEl.classList.add('square-indicate-vertical');
                }
                rowEl.append(squareEl);
            }
            tableEl.append(rowEl);
        }
        this.tableEl = tableEl;
        this.options.crosswordEl.innerHTML = tableEl.outerHTML;
    }

    /**
     * Add each cell into table.
     */
    addCell() {
        let {words, previewSetting, rowsNum, colsNum} = this.options;
        // Don't draw empty words.
        if (words.length === 0) {
            return;
        }
        for (let i = 0; i < words.length; i++) {
            const answer = words[i].answer.trim().replace(/-|\s/g, '');
            let row = words[i].startrow + 1;
            let column = words[i].startcolumn + 1;
            let answerLength = answer.length;
            let realLength = answerLength + words[i].startcolumn;
            let allowLength = parseInt(colsNum);
            let invalidWord = words[i].clue.trim() === '';
            // Add more columns and row for preview.
            row++;
            column++;

            if (!invalidWord) {
                invalidWord = this.isContainSpecialCharacters(answer) ||
                    this.checkCorrectnessAnswer(words[i].answer);
            }

            if (words[i].orientation) {
                realLength = answerLength + words[i].startrow;
                allowLength = parseInt(rowsNum);
            }

            for (let j = 0; j < answer.length; j++) {
                const number = i + 1;
                const squareEl = document.querySelector('.grid-row:nth-child(' + row + ') .grid-square:nth-child(' + column + ')');
                if (!squareEl) {
                    continue;
                }

                // Paint white background.
                squareEl.classList.add('background-white');

                if (j === 0) {
                    const labelEl = squareEl.querySelector('.word-label');
                    if (!labelEl) {
                        let spanEl = document.createElement('span');
                        spanEl.className = 'word-label';
                        spanEl.innerText = words[i]?.no ?? number;
                        squareEl.append(spanEl);
                    } else {
                        let label = labelEl.innerText;
                        label += ', ' + words[i]?.no ?? number;
                        labelEl.innerText = label;
                    }
                }
                const letter = answer[j].toUpperCase().trim() ?? '';
                const contentEl = squareEl.querySelector('span.word-content');
                if (!contentEl) {
                    let spanEl = document.createElement('span');
                    spanEl.className = 'word-content';
                    spanEl.innerText = letter;
                    squareEl.append(spanEl);
                } else {
                    let text = '';
                    const innerText = contentEl.innerText;
                    if (innerText.search(letter) < 0) {
                        text = innerText + ' | ' + letter;
                        squareEl.style.backgroundColor = previewSetting.conflictColor;
                        contentEl.innerText = text;
                    }
                }

                if (invalidWord || realLength > allowLength) {
                    squareEl.style.backgroundColor = previewSetting.conflictColor;
                }

                if (words[i].orientation) {
                    row++;
                } else {
                    column++;
                }
            }
        }
    }

    /**
     * Show the crossword preview.
     */
    previewCrossword() {
        // Build the background table.
        this.buildBackgroundTable();
        // Fill the cell into the table.
        this.addCell();
    }

    /**
     * Build crossword for attempt.
     */
    buildCrossword() {
        const options = this.options;
        // Setup size of crossword.
        this.options = {...options, width: options.colsNum * 31, height: options.rowsNum * 31};
        // Set up for clue input: maxlength, aria-label.
        const crosswordClue = new CrosswordClue(this.options);
        crosswordClue.setUpClue();
        // Draw crossword by SVG to support high contrast mode.
        this.drawCrosswordSVG();
        // Sync data between clue section and crossword cell.
        this.syncDataForInit();
        // Add event when resized screen.
        this.addEventResizeScreen();
    }

    /**
     * Draw crossword by SVG element.
     */
    drawCrosswordSVG() {
        const options = this.options;
        const crosswordEl = this.options.crosswordEl;

        if (!crosswordEl) {
            return;
        }

        // Create background.
        let svg = this.createElementNSFrom(
            'svg',
            {
                'class': 'crossword-grid',
                viewBox: `0 0 ${options.width} ${options.height}`
            }
        );

        // Create black background.
        const rectEl = this.createElementNSFrom(
            'rect',
            {
                'class': 'crossword-grid-background',
                x: 0,
                y: 0,
                width: options.width,
                height: options.height
            }
        );
        svg.append(rectEl);

        // Create svg body.
        svg = this.createCrosswordBody(svg);

        // Set size for crossword.
        svg = this.setSizeForCrossword(svg);

        // Add horizontal and vertical line.
        svg = this.setBorder(svg);
        // Create an input, by default, it will be hidden.
        const inputContainEl = this.createElementFrom(
            'div',
            {
                'class': 'crossword-hidden-input-wrapper'
            }
        );
        const inputEl = this.createElementFrom(
            'input',
            {
                type: 'text',
                'class': 'crossword-hidden-input',
                maxlength: 1,
                autocomplete: 'off',
                spellcheck: false,
                autocorrect: 'off'
            }
        );
        // Add event for word input.
        this.addEventForWordInput(inputEl);
        inputContainEl.append(inputEl);

        if (options.colsNum >= 15) {
            svg.classList.add('adjust-small-crossword');
        }

        if (options.colsNum >= 20) {
            svg.classList.add('adjust-crossword');
        }
        crosswordEl.append(svg, inputContainEl);
    }

    /**
     * Creates an element with the specified namespace URI and qualified name.
     *
     * @param {String} type
     * @param {Object} attributes
     *
     * @return {Element} The return element.
     */
    createElementNSFrom(type, attributes = {}) {
        const element = document.createElementNS('http://www.w3.org/2000/svg', type);
        for (let key in attributes) {
            element.setAttributeNS(null, key, attributes[key]);
        }
        return element;
    }

    /**
     * Create element with attributes.
     *
     * @param {String} type
     * @param {Object} attributes The attribute list.
     * @return {Element} The return element.
     */
    createElementFrom(type, attributes = {}) {
        const element = document.createElement(type);
        for (let key in attributes) {
            element.setAttribute(key, attributes[key]);
        }
        return element;
    }

    /**
     * Calculate position and add cell into the crossword.
     *
     * @param {Element} svg  The svg element.
     * @return {Element} The svg element.
     */
    createCrosswordBody(svg) {
        const {words, cellWidth, cellHeight} = this.options;
        let count = 0;
        for (let i in words) {
            const word = words[i];
            const ignoreList = this.getIgnoreIndexByAnswerNumber(word.number);
            for (let key = 0; key < word.length - ignoreList.length; key++) {
                // Prepare attributes for g.
                const customAttribute = {
                    'data-startrow': word.startRow,
                    'data-startcolumn': word.startColumn,
                    'data-letterindex': key,
                    'data-word': '(' + word.number + ')',
                    'data-code': 'A' + count
                };
                // Calculate the letter position.
                const position = this.calculatePosition(word, parseInt(key));
                // Create rect element with these position.
                const rectEl = this.createElementNSFrom(
                    'rect',
                    {
                        ...position,
                        width: cellWidth,
                        height: cellHeight,
                        'class': 'crossword-cell'
                    }
                );
                // Create g element with the attributes.
                let g = this.createElementNSFrom('g', {...customAttribute});
                // Get exist ting rect element.
                const existingRectElement = svg.querySelector(`rect.crossword-cell[x='${position.x}'][y='${position.y}']`);
                // Create text element to hold the letter.
                const textEl = this.createElementNSFrom(
                    'text',
                    {
                        'class': 'crossword-cell-text',
                        x: position.x + cellWidth / 2,
                        y: position.y + cellHeight / 2 + 1,
                        'text-anchor': 'middle',
                        'alignment-baseline': 'middle',
                    }
                );
                // Check if cell is not drawn.
                if (!existingRectElement) {
                    // Create cell.
                    g.append(rectEl);
                    // If it's the first cell of word.
                    // Draw word number.
                    if (parseInt(key) === 0) {
                        g = this.appendCellNumber(g, position, word.number);
                    }
                    g.append(textEl);
                    // Add event for cell.
                    this.addEventForG(g);
                    count++;
                    svg.append(g);
                } else {
                    let existingNumberElement = existingRectElement.closest('g').querySelector('text.crossword-cell-number');
                    let currentWord = existingRectElement.closest('g').dataset.word;
                    let g;
                    existingRectElement.closest('g').dataset.word = currentWord + '(' + word.number + ')';
                    if (parseInt(key) !== 0) {
                        continue;
                    }
                    if (existingNumberElement) {
                        // Append word number, if this cell is existed another one.
                        existingNumberElement.append(', ' + word.number);
                    } else {
                        // Create new word number.
                        g = existingRectElement.closest('g');
                        this.appendCellNumber(g, position, word.number);
                    }
                }
            }
        }
        return svg;
    }

    /**
     * Set horizontal and vertical line for grid.
     *
     * @param {Element} svg The svg element.
     * @return {Element} The svg element after appended border.
     */
    setBorder(svg) {
        const {colsNum, rowsNum, cellWidth, cellHeight, width, height} = this.options;

        for (let i = 0; i <= rowsNum; i++) {
            let strokeWidth = 1;
            if (i === 0 || i === rowsNum) {
                strokeWidth = 2;
            }
            const horizontalLine = this.createElementNSFrom('line', {
                x1: 0,
                y1: i * cellHeight,
                x2: width,
                y2: i * cellHeight,
                stroke: '#000',
                'stroke-width': strokeWidth,
            });
            svg.appendChild(horizontalLine);
        }

        for (let i = 0; i <= colsNum; i++) {
            let strokeWidth = 1;
            if (i === 0 || i === colsNum) {
                strokeWidth = 2;
            }
            const verticalLine = this.createElementNSFrom('line', {
                x1: i * cellWidth,
                y1: 0,
                x2: i * cellWidth,
                y2: height,
                stroke: '#000',
                'stroke-width': strokeWidth,
            });
            svg.appendChild(verticalLine);
        }

        return svg;
    }

    /**
     * Create word number for the cell.
     *
     * @param {Element} g The g element.
     * @param {Object} position The coordinates of letter.
     * @param {Number} wordNumber The word number.
     *
     * @return {Element} The g element.
     */
    appendCellNumber(g, position, wordNumber) {
        // Update position.
        const x = position.x + 2;
        const y = position.y + 10;
        let textNumber = this.createElementNSFrom(
            'text',
            {
                x,
                y,
                'class': 'crossword-cell-number'
            }
        );
        textNumber.append(wordNumber);
        g.append(textNumber);
        return g;
    }

    /**
     * Add event to the g element.
     *
     * @param {Element} g The g element.
     */
    addEventForG(g) {
        const {readonly} = this.options;
        if (readonly) {
            return;
        }
        // Handle event click.
        g.addEventListener('click', (e) => {
            const inputWrapperEl = this.options.crosswordEl.querySelector('.crossword-hidden-input-wrapper');
            const inputEl = inputWrapperEl.querySelector('input');
            let element = e.target;
            // Make sure select g.
            if (element.tagName !== 'g') {
                element = element.closest('g');
            }
            this.handleWordSelect(element);
            inputEl.dataset.code = element.dataset.code;
            inputEl.value = '';
            this.updatePositionForCellInput(element.querySelector('rect'));
            inputEl.focus();
        });
    }

    /**
     * Handle action when click on cell.
     *
     * @param {Element} gEl The g element.
     */
    handleWordSelect(gEl) {
        const currentCell = gEl.dataset.code;
        let words = gEl.dataset.word;
        let focus = -1;
        let {coordinates, wordNumber} = this.options;

        // Detect word number.
        words = words.match(/(\d+)/g);

        // Detect word number based on event click.
        // The focus variable is the new word number.
        if (currentCell === coordinates) {
            const indexCell = words.indexOf(wordNumber);
            if (words[indexCell + 1] !== undefined) {
                focus = words[indexCell + 1];
            } else {
                focus = words[0];
            }
        } else {
            // Update new coordinates.
            this.options.coordinates = currentCell;
            if (wordNumber < 0) {
                this.options.wordNumber = words[0];
            }
            if (words.includes(wordNumber)) {
                focus = wordNumber;
            } else {
                focus = words[0];
            }
        }
        // Update word number.
        this.options.wordNumber = focus;
        const word = this.options.words.find(o => o.number === parseInt(focus));
        if (!word) {
            return;
        }
        // Sorting and Updating letter index.
        this.updateLetterIndexForCells(word);
        // Toggle highlight and focused.
        this.toggleHighlight(word, gEl);
        // Focus the clue.
        this.focusClue();
        // Update sticky clue for mobile version.
        this.setStickyClue();
    }

    /**
     * Set size and position for cell input.
     *
     * @param {Element} [rectEl=null] Rect element.
     */
    updatePositionForCellInput(rectEl = null) {
        if (rectEl === null) {
            rectEl = this.options.crosswordEl.querySelector('rect.crossword-cell-focussed');
        }
        if (rectEl) {
            const rect = rectEl.getBoundingClientRect();
            const parentEl = this.options.crosswordEl.querySelector('.crossword-grid').getBoundingClientRect();
            const inputWrapperEl = this.options.crosswordEl.querySelector('.crossword-hidden-input-wrapper');
            let top = rect.top - parentEl.top;
            if (top < 1) {
                top = 0;
            }
            inputWrapperEl.style.cssText = `
                display: block; top: ${top + 2}px;
                left: ${rect.left - parentEl.left + 2}px;
                width: ${rect.width - 3}px;
                height: ${rect.height - 3}px
            `;
        }
    }

    /**
     * Handle insert text event (for keyboard and non-keyboard events).
     *
     * @param {Object} event Event data.
     * @param {String} value the character we are inserted to the clue grid.
     */
    handleInsertTextEventForGridInput(event, value) {
        const {wordNumber, words} = this.options;
        const inputEl = event.target;
        const code = inputEl.dataset.code;
        const upperText = value.toUpperCase();
        if (this.replaceText(value) === '') {
            return;
        }
        // Filter value.
        if (code) {
            const textEl = this.options.crosswordEl.querySelector(`g[data-code='${code}'] text.crossword-cell-text`);
            if (!textEl) {
                return;
            }
            textEl.innerHTML = upperText;
            const letterIndex = parseInt(textEl.closest('g').dataset.letterindex);
            const wordObj = words.find(word => word.number === parseInt(wordNumber));
            const nextCellEl = this.findTheClosestCell(wordNumber, wordObj, letterIndex + 1).pop() ?? null;
            // Interact with clue.
            this.bindDataToClueInput(textEl.closest('g'), value);
            if (nextCellEl) {
                nextCellEl.dispatchEvent(new Event('click'));
            }
        }
    }

    /**
     * Add event to word input element.
     *
     * @param {Element} inputEl The input element.
     */
    addEventForWordInput(inputEl) {
        const {readonly} = this.options;
        if (readonly) {
            return;
        }
        inputEl.addEventListener('input', (e) => {
            e.preventDefault();
            if (e.inputType === 'insertText') {
                this.handleInsertTextEventForGridInput(e, e.data);
            }
            return true;
        });

        inputEl.addEventListener('keypress', (e) => {
            e.preventDefault();
            this.handleInsertTextEventForGridInput(e, e.key);
            return true;
        });

        inputEl.addEventListener('compositionend', (evt) => {
            evt.preventDefault();
            evt.stopPropagation();
            const {wordNumber, words} = this.options;
            const wordObj = words.find(word => word.number === parseInt(wordNumber));
            let key = evt.data.toUpperCase();
            const code = evt.target.dataset.code;
            if (this.replaceText(key) === '') {
                return false;
            }
            if (code) {
                let chars = key.split('');
                const gEl = this.options.crosswordEl.querySelector(`g[data-code='${code}']`);
                if (!gEl) {
                    return false;
                }
                const letterIndex = parseInt(gEl.dataset.letterindex);
                let index = 0;
                for (let char of chars) {
                    if (this.replaceText(char) === '') {
                        continue;
                    }
                    const cellEl = this.findTheClosestCell(wordNumber, wordObj, letterIndex + index).pop() ?? null;
                    // Interact with clue.
                    if (cellEl) {
                        cellEl.querySelector('text.crossword-cell-text').innerHTML = char;
                        this.bindDataToClueInput(cellEl, char);
                        cellEl.dispatchEvent(new Event('click'));
                        index++;
                    }
                }

                const nextCellEl = this.findTheClosestCell(wordNumber, wordObj, letterIndex + chars.length).pop() ?? null;
                if (nextCellEl) {
                    nextCellEl.dispatchEvent(new Event('click'));
                }
            }
            return true;
        });

        inputEl.addEventListener('keyup', (event) => {
            event.preventDefault();
            const {wordNumber, cellWidth, cellHeight, words} = this.options;
            const {key, target} = event;
            const code = target.dataset.code;
            const gEl = this.options.crosswordEl.querySelector(`g[data-code='${code}']`);
            const word = words.find(o => o.number === parseInt(wordNumber));
            const letterIndex = this.findTheClosestCell(wordNumber, word,
                parseInt(gEl.dataset.letterindex) - 1, false)[0];
            const previousCell = this.options.crosswordEl.querySelector(
                `g[data-word*='(${wordNumber})'][data-letterindex='${letterIndex}']`
            );
            const textEl = gEl.querySelector('text.crossword-cell-text');
            let x = parseInt(gEl.querySelector('rect').getAttributeNS(null, 'x'));
            let y = parseInt(gEl.querySelector('rect').getAttributeNS(null, 'y'));
            if (key === this.DELETE || key === this.BACKSPACE) {
                if (textEl.innerHTML === '') {
                    if (previousCell) {
                        previousCell.dispatchEvent(new Event('click'));
                    }
                } else {
                    textEl.innerHTML = '';
                    this.bindDataToClueInput(gEl, '_');
                }
            }
            if ([this.ARROW_UP, this.ARROW_DOWN, this.ARROW_LEFT, this.ARROW_RIGHT].includes(key)) {
                if (key === this.ARROW_UP) {
                    y -= cellHeight;
                }
                if (key === this.ARROW_DOWN) {
                    y += cellHeight;
                }
                if (key === this.ARROW_LEFT) {
                    x -= cellWidth;
                }
                if (key === this.ARROW_RIGHT) {
                    x += cellWidth;
                }
                const nextCell = this.options.crosswordEl.querySelector(`g rect[x='${x}'][y='${y}']`);
                if (nextCell) {
                    nextCell.closest('g').dispatchEvent(new Event('click'));
                }
            }
        });

        inputEl.addEventListener('click', (e) => {
            const inputEl = e.target;
            const code = inputEl.dataset.code;
            const gEl = this.options.crosswordEl.querySelector(`g[data-code='${code}']`);
            this.handleWordSelect(gEl);
        });

        inputEl.addEventListener('keydown', (e) => {
            let {key} = e;
            key = key.toLowerCase();
            if (e.ctrlKey) {
                if (
                    key === this.Z_KEY ||
                    key === this.A_KEY
                ) {
                    e.preventDefault();
                }
            }

            if (e.key === this.ENTER) {
                e.preventDefault();
            }
        });

        inputEl.addEventListener('paste', (e) => {
            e.preventDefault();
        });
    }

    /**
     * Add event to resize the screen width.
     */
    addEventResizeScreen() {
        window.addEventListener('resize', () => {
            this.updatePositionForCellInput();
        });
    }
}
