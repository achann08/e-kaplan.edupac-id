@qtype @qtype_crossword
Feature: Preview a Crossword question
  As a teacher
  In order to check my Crossword questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name          | template                                    |
      | Test questions   | crossword | crossword-001 | normal                                      |
      | Test questions   | crossword | crossword-002 | unicode                                     |
      | Test questions   | crossword | crossword-003 | different_codepoint                         |
      | Test questions   | crossword | crossword-004 | sampleimage                                 |
      | Test questions   | crossword | crossword-005 | clear_incorrect_response                    |
      | Test questions   | crossword | crossword-006 | normal_with_hyphen_and_space                |
      | Test questions   | crossword | crossword-007 | accept_wrong_accents_but_subtract_point     |
      | Test questions   | crossword | crossword-008 | accept_wrong_accents_but_not_subtract_point |
      | Test questions   | crossword | crossword-009 | not_accept_wrong_accents                    |

  @javascript
  Scenario: Preview a Crossword question and submit a correct response.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Answer 1: BRAZIL, Answer 2: PARIS, Answer 3: ITALY"

  @javascript
  Scenario: Preview a Crossword question with sample image.
    When I am on the "crossword-004" "core_question > preview" page logged in as teacher
    And "//img[contains(@src,'question/questiontext') and contains(@src,'questiontextimg.jpg')]" "xpath_element" should exist
    And "//img[contains(@src,'question/clue') and contains(@src,'clueimg.jpg')]" "xpath_element" should exist
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I press "Submit and finish"
    Then "//img[contains(@src,'question/correctfeedback') and contains(@src,'correctfbimg.jpg')]" "xpath_element" should exist
    And I press "Start again"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "NANNO"
    And I press "Submit and finish"
    And "//img[contains(@src,'question/partiallycorrectfeedback') and contains(@src,'partialfbimg.jpg')]" "xpath_element" should exist
    And I press "Start again"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "LONDON"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "HANOI"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "NANNO"
    And I press "Submit and finish"
    And "//img[contains(@src,'question/incorrectfeedback') and contains(@src,'incorrectfbimg.jpg')]" "xpath_element" should exist

  @javascript
  Scenario: Preview a Crossword question and submit an partially correct response.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "NANNO"
    And I press "Submit and finish"
    Then I should see "Partially correct feedback."
    And I should see "Answer 1: BRAZIL, Answer 2: PARIS, Answer 3: ITALY"

  @javascript
  Scenario: Preview a Crossword question and submit an incorrect response.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "LONDON"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "HANOI"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "NANNO"
    And I press "Submit and finish"
    Then I should see "Incorrect feedback."
    And I should see "Answer 1: BRAZIL, Answer 2: PARIS, Answer 3: ITALY"

  @javascript
  Scenario: Deleting characters from input clue area.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I select "2" characters from position "1" in the "1 Across. where is the Christ the Redeemer statue located in? Answer length 6"
    And I press the delete key
    And I select "3" characters from position "3" in the "3 Across. Where is the Leaning Tower of Pisa? Answer length 5"
    And I press the delete key
    Then the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" matches value "__AZIL"
    And the field "2 Down. Eiffel Tower is located in? Answer length 5" matches value "PARIS"
    And the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" matches value "IT___"

  @javascript
  Scenario: Deleting intersect characters from input clue area.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I select "3" characters from position "2" in the "2 Down. Eiffel Tower is located in? Answer length 5"
    And I press the delete key
    Then the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" matches value "BR_ZIL"
    And the field "2 Down. Eiffel Tower is located in? Answer length 5" matches value "P___S"
    And the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" matches value "_TALY"

  @javascript
  Scenario: Preview a Crossword question with unicode UTF-8 correct answer.
    When I am on the "crossword-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "回答一" in the crossword clue "1 Down. 线索 1 Answer length 3"
    And I enter unicode character "回答两个" in the crossword clue "2 Across. 线索 2 Answer length 4"
    And I enter unicode character "回答三" in the crossword clue "3 Down. 线索 3 Answer length 3"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Answer 1: 回答一, Answer 2: 回答两个, Answer 3: 回答三"

  @javascript
  Scenario: Preview a Crossword question with unicode UTF-8 answer and submit a partially correct response.
    When I am on the "crossword-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "回答一" in the crossword clue "1 Down. 线索 1 Answer length 3"
    And I enter unicode character "回答二" in the crossword clue "2 Across. 线索 2 Answer length 4"
    And I enter unicode character "回答三" in the crossword clue "3 Down. 线索 3 Answer length 3"
    And I press "Submit and finish"
    Then I should see "Partially correct feedback."
    And I should see "Answer 1: 回答一, Answer 2: 回答两个, Answer 3: 回答三"

  @javascript
  Scenario: Preview a Crossword question with unicode UTF-8 answer and submit an incorrect response.
    When I am on the "crossword-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "回答四" in the crossword clue "1 Down. 线索 1 Answer length 3"
    And I enter unicode character "回答五" in the crossword clue "2 Across. 线索 2 Answer length 4"
    And I enter unicode character "回答六" in the crossword clue "3 Down. 线索 3 Answer length 3"
    And I press "Submit and finish"
    Then I should see "Incorrect feedback."
    And I should see "Answer 1: 回答一, Answer 2: 回答两个, Answer 3: 回答三"

  @javascript
  Scenario: Preview a Crossword question has two same answers but different code point and submit a correct response.
    When I am on the "crossword-003" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "Amélie" in the crossword clue "1 Across. Answer contains letter é has codepoint \u00e9 Answer length 6"
    And I enter unicode character "Amélie" in the crossword clue "2 Down. Answer contains letter é has codepoint \u0065\u0301 Answer length 6"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Answer 1: AMÉLIE, Answer 2: AMÉLIE"

  @javascript
  Scenario: Preview a Crossword question has two same answers but different code point and submit a partially correct response.
    When I am on the "crossword-003" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "Amélie" in the crossword clue "1 Across. Answer contains letter é has codepoint \u00e9 Answer length 6"
    And I enter unicode character "Améliz" in the crossword clue "2 Down. Answer contains letter é has codepoint \u0065\u0301 Answer length 6"
    And I press "Submit and finish"
    Then I should see "Partially correct feedback."
    And I should see "Answer 1: AMÉLIE, Answer 2: AMÉLIE"

  @javascript
  Scenario: Preview a Crossword question has two same answers but different code point and submit an incorrect response.
    When I am on the "crossword-003" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I enter unicode character "Amelie" in the crossword clue "1 Across. Answer contains letter é has codepoint \u00e9 Answer length 6"
    And I enter unicode character "Amelie" in the crossword clue "2 Down. Answer contains letter é has codepoint \u0065\u0301 Answer length 6"
    And I press "Submit and finish"
    Then I should see "Incorrect feedback."
    And I should see "Answer 1: AMÉLIE, Answer 2: AMÉLIE"

  @javascript
  Scenario: Preview a Crossword question with clear incorrect responses option.
    When I am on the "crossword-005" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Interactive with multiple tries"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "PARIT"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I press "Check"
    And I press "Try again"
    Then the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" matches value "BRAZIL"
    And the field "2 Down. Eiffel Tower is located in? Answer length 5" matches value "_A_I_"
    And the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" matches value "ITALY"

  @javascript
  Scenario: Users can enter their answers with a leading space and the space will be replaced by an underscore.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Interactive with multiple tries"
    And I press "Start again with these options"
    And I set the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" to "BRAZIL"
    And I set the field "2 Down. Eiffel Tower is located in? Answer length 5" to "  RIS"
    And I set the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" to "ITALY"
    And I press "Submit and finish"
    Then I should see "Partially correct feedback."
    And the field "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" matches value "BR_ZIL"
    And the field "2 Down. Eiffel Tower is located in? Answer length 5" matches value "__RIS"
    And the field "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" matches value "ITALY"

  @javascript
  Scenario: For answers that contain spaces or hyphens, the answer hint will not count those characters.
    When I am on the "crossword-006" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Interactive with multiple tries"
    And I press "Start again with these options"
    Then I should see "(5, 12)"
    And I should see "(6, 5)"
    And I should see "(3, 7-3)"

  @javascript
  Scenario: Preview a Crossword question and submit a correct response with mobile input.
    When I am on the "crossword-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    # BRAZIL
    And I enter character "B" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "1"
    And I enter character "R" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "2"
    And I enter character "A" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "3"
    And I enter character "Z" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "4"
    And I enter character "I" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "5"
    And I enter character "L" in the crossword clue using mobile input "1 Across. where is the Christ the Redeemer statue located in? Answer length 6" in position "6"
    # PARIS
    And I enter character "P" in the crossword clue using mobile input "2 Down. Eiffel Tower is located in? Answer length 5" in position "1"
    And I enter character "A" in the crossword clue using mobile input "2 Down. Eiffel Tower is located in? Answer length 5" in position "2"
    And I enter character "R" in the crossword clue using mobile input "2 Down. Eiffel Tower is located in? Answer length 5" in position "3"
    And I enter character "I" in the crossword clue using mobile input "2 Down. Eiffel Tower is located in? Answer length 5" in position "4"
    And I enter character "S" in the crossword clue using mobile input "2 Down. Eiffel Tower is located in? Answer length 5" in position "5"
    # ITALY
    And I enter character "I" in the crossword clue using mobile input "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" in position "1"
    And I enter character "T" in the crossword clue using mobile input "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" in position "2"
    And I enter character "A" in the crossword clue using mobile input "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" in position "3"
    And I enter character "L" in the crossword clue using mobile input "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" in position "4"
    And I enter character "Y" in the crossword clue using mobile input "3 Across. Where is the Leaning Tower of Pisa? Answer length 5" in position "5"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Answer 1: BRAZIL, Answer 2: PARIS, Answer 3: ITALY"

  @javascript
  Scenario: When the answer option accept incorrect accents but subtracts point and user enters answer wrong accents.
    When I am on the "crossword-007" "core_question > preview" page logged in as teacher
    And I set the field "1 Across. Des accompagnements à base de foie animal ? Answer length 4" to "PATE"
    And I enter unicode character "TÉLÉPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Submit and finish"
    Then I should see "Partially correct"
    And I should see "Mark 1.75 out of 2.00"

  @javascript
  Scenario: When the answer option accept incorrect accents and teacher enters answer wrong accents.
    When I am on the "crossword-008" "core_question > preview" page logged in as teacher
    And I set the field "1 Across. Des accompagnements à base de foie animal ? Answer length 4" to "PATE"
    And I set the field "2 Down. Appareil utilisé pour passer des appels ? Answer length 9" to "TELEPHONE"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Mark 2.00 out of 2.00"

  @javascript
  Scenario: The teacher enters the wrong accents when the answer option does not allow the wrong accents.
    When I am on the "crossword-009" "core_question > preview" page logged in as teacher
    And I enter unicode character "PATE" in the crossword clue "1 Across. Des accompagnements à base de foie animal ? Answer length 4"
    And I enter unicode character "TELEPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Submit and finish"
    Then I should see "Incorrect feedback."
    And I should see "Mark 0.00 out of 2.00"

  @javascript
  Scenario: The teacher tries to answer a lot when the answer option allows incorrect accents, no points will be deducted.
    When I am on the "crossword-008" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Interactive with multiple tries"
    And I press "Start again with these options"
    And I enter unicode character "PATE" in the crossword clue "1 Across. Des accompagnements à base de foie animal ? Answer length 4"
    And I enter unicode character "TALAPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Check"
    And I press "Try again"
    And I enter unicode character "PATE" in the crossword clue "1 Across. Des accompagnements à base de foie animal ? Answer length 4"
    And I enter unicode character "TELEPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Submit and finish"
    Then I should see "Correct feedback"
    And I should see "Mark 1.80 out of 2.00"

  @javascript
  Scenario: The teacher tries to answer a lot when the answer option allows incorrect accents, points will be deducted.
    When I am on the "crossword-007" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Interactive with multiple tries"
    And I press "Start again with these options"
    And I enter unicode character "PATE" in the crossword clue "1 Across. Des accompagnements à base de foie animal ? Answer length 4"
    And I enter unicode character "TALAPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Check"
    And I press "Try again"
    And I enter unicode character "PATE" in the crossword clue "1 Across. Des accompagnements à base de foie animal ? Answer length 4"
    And I enter unicode character "TELEPHONE" in the crossword clue "2 Down. Appareil utilisé pour passer des appels ? Answer length 9"
    And I press "Submit and finish"
    Then I should see "Partially correct feedback."
    And I should see "Mark 1.30 out of 2.00"

  @javascript
  Scenario: User can enter alphanumeric characters continuously, the answer will automatically add special characters if any.
    When I am on the "crossword-006" "core_question > preview" page logged in as teacher
    And I set the field "1 Across. British broadcaster and naturalist, famous for his voice-overs of nature programmes? Answer length 5, 12" to "DAVIDATTENBOROUGH"
    And I set the field "2 Down. Former Prime Minister of the United Kingdom? Answer length 6, 5" to "GORDONBROWN"
    And I set the field "3 Down. Engineer, computer scientist and inventor of the World Wide Web? Answer length 3, 7-3" to "TIMBERNERSLEE"
    Then the field "1 Across. British broadcaster and naturalist, famous for his voice-overs of nature programmes? Answer length 5, 12" matches value "DAVID ATTENBOROUGH"
    And the field "2 Down. Former Prime Minister of the United Kingdom? Answer length 6, 5" matches value "GORDON BROWN"
    And the field "3 Down. Engineer, computer scientist and inventor of the World Wide Web? Answer length 3, 7-3" matches value "TIM BERNERS-LEE"
