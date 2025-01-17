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

namespace qtype_crossword;
use Normalizer;
use \qtype_crossword_question;
/**
 * Static utilities.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_crossword
 * @copyright 2022, The Open University
 */
class util {

    /**
     * Normalise a UTF-8 string to FORM_C, avoiding the pitfalls in PHP's
     * normalizer_normalize function.
     * @param string $string The input string.
     * @param int $normalizeform The form normalize. Default is FORM_KC.
     * @return string The normalised string.
     */
    public static function safe_normalize(string $string, int $normalizeform = Normalizer::FORM_KC): string {
        if ($string === '') {
            return '';
        }

        $normalised = normalizer_normalize($string, $normalizeform);
        if ($normalised === false) {
            // An error occurred in normalizer_normalize, but we have no idea what.
            debugging('Failed to normalise string: ' . $string, DEBUG_DEVELOPER);
            return $string; // Return the original string, since it is the best we have.
        }

        return $normalised;
    }

    /**
     * Remove the work-break characters '-' and ' ' from an answer.
     *
     * @param string Full answer.
     * @return string Answer with just the letters remaining.
     */
    public static function remove_break_characters(string $text): string {
        // Remove hyphen and space from text.
        return preg_replace('/-|\s/', '', $text);
    }

    /**
     * Remove accent character in text. Eg: Français -> Francais.
     *
     * @param string $string The input string.
     * @return string The normal string without accents.
     */
    public static function remove_accent(string $string): string {
        return preg_replace('/\p{Mn}/u', '', self::safe_normalize($string, Normalizer::FORM_KD));
    }
}
