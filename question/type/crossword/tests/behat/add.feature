@qtype @qtype_crossword
Feature: Test creating a Crossword question
  As a teacher
  In order to test my students
  I need to be able to create a Crossword question

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

  Scenario: Create a Crossword question with correct answer.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                      | crossword-001               |
      | Question text                      | Crossword question text     |
      | Number of rows                     | 3                           |
      | Number of columns                  | 3                           |
      | id_clue_0                          | Clue 1                      |
      | id_clue_1                          | Clue 2                      |
      | id_clue_2                          | Clue 3                      |
      | id_answer_0                        | AAA                         |
      | id_answer_1                        | BBB                         |
      | id_answer_2                        | CCC                         |
      | id_startrow_0                      | 1                           |
      | id_startrow_1                      | 2                           |
      | id_startrow_2                      | 3                           |
      | id_startcolumn_0                   | 0                           |
      | id_startcolumn_1                   | 0                           |
      | id_startcolumn_2                   | 0                           |
      | For any correct response           | Correct feedback            |
      | For any partially correct response | Partially correct feedback. |
      | For any incorrect response         | Incorrect feedback.         |
    Then I should see "crossword-001"

  Scenario: Create a Crossword question with unicode UTF-8 correct answer.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                      | crossword-002               |
      | Question text                      | 填字游戏问题文本                    |
      | Number of rows                     | 4                           |
      | Number of columns                  | 4                           |
      | id_clue_0                          | 线索 1                        |
      | id_clue_1                          | 线索 2                        |
      | id_clue_2                          | 线索 3                        |
      | id_answer_0                        | 回答一                         |
      | id_answer_1                        | 回答两个                        |
      | id_answer_2                        | 回答三                         |
      | id_startrow_0                      | 2                           |
      | id_startrow_1                      | 2                           |
      | id_startrow_2                      | 1                           |
      | id_startcolumn_0                   | 0                           |
      | id_startcolumn_1                   | 0                           |
      | id_startcolumn_2                   | 1                           |
      | For any correct response           | Correct feedback            |
      | id_orientation_0                   | 1                           |
      | id_orientation_1                   | 0                           |
      | id_orientation_2                   | 1                           |
      | For any partially correct response | Partially correct feedback. |
      | For any incorrect response         | Incorrect feedback.         |
    Then I should see "crossword-002"

  Scenario: Create a Crossword question has two same answers but different code point.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                      | crossword-003                                        |
      | Question text                      | Crossword question text                              |
      | Number of rows                     | 6                                                    |
      | Number of columns                  | 6                                                    |
      | id_clue_0                          | Answer contains letter é has codepoint \u00e9        |
      | id_clue_1                          | Answer contains letter é has codepoint \u0065\u0301 |
      | id_answer_0                        | Amélie                                               |
      | id_answer_1                        | Amélie                                              |
      | id_startrow_0                      | 3                                                    |
      | id_startrow_1                      | 1                                                    |
      | id_startcolumn_0                   | 0                                                    |
      | id_startcolumn_1                   | 2                                                    |
      | For any correct response           | Correct feedback                                     |
      | id_orientation_0                   | 0                                                    |
      | id_orientation_1                   | 1                                                    |
      | For any partially correct response | Partially correct feedback.                          |
      | For any incorrect response         | Incorrect feedback.                                  |
    Then I should see "crossword-003"

  Scenario: Create a Crossword question with the answer option do not accept wrong accent.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                      | crossword-004                               |
      | Question text                      | Crossword question text                     |
      | Number of rows                     | 9                                           |
      | Number of columns                  | 4                                           |
      | Accented letters                   | strict                                      |
      | id_clue_0                          | Des accompagnements à base de foie animal ? |
      | id_clue_1                          | Appareil utilisé pour passer des appels ?   |
      | id_answer_0                        | PÂTÉ                                        |
      | id_answer_1                        | TÉLÉPHONE                                   |
      | id_startrow_0                      | 0                                           |
      | id_startrow_1                      | 0                                           |
      | id_startcolumn_0                   | 0                                           |
      | id_startcolumn_1                   | 2                                           |
      | For any correct response           | Correct feedback                            |
      | id_orientation_0                   | 0                                           |
      | id_orientation_1                   | 1                                           |
      | For any partially correct response | Partially correct feedback.                 |
      | For any incorrect response         | Incorrect feedback.                         |
    Then I should see "crossword-004"

  Scenario: Create a Crossword question with an answer that accepts the wrong accents but subtracts points.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                            | crossword-005                               |
      | Question text                            | Crossword question text                     |
      | Number of rows                           | 9                                           |
      | Number of columns                        | 4                                           |
      | Accented letters                         | penalty                                     |
      | Grade for answers with incorrect accents | 0.5                                         |
      | id_clue_0                                | Des accompagnements à base de foie animal ? |
      | id_clue_1                                | Appareil utilisé pour passer des appels ?   |
      | id_answer_0                              | PÂTÉ                                        |
      | id_answer_1                              | TÉLÉPHONE                                   |
      | id_startrow_0                            | 0                                           |
      | id_startrow_1                            | 0                                           |
      | id_startcolumn_0                         | 0                                           |
      | id_startcolumn_1                         | 2                                           |
      | For any correct response                 | Correct feedback                            |
      | id_orientation_0                         | 0                                           |
      | id_orientation_1                         | 1                                           |
      | For any partially correct response       | Partially correct feedback.                 |
      | For any incorrect response               | Incorrect feedback.                         |
    Then I should see "crossword-005"

  Scenario: Create a Crossword question with an answer that accepts the wrong accents and do not subtracts points.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Crossword" question filling the form with:
      | Question name                      | crossword-006                               |
      | Question text                      | Crossword question text                     |
      | Number of rows                     | 9                                           |
      | Number of columns                  | 4                                           |
      | Accented letters                   | ignore                                      |
      | id_clue_0                          | Des accompagnements à base de foie animal ? |
      | id_clue_1                          | Appareil utilisé pour passer des appels ?   |
      | id_answer_0                        | PÂTÉ                                        |
      | id_answer_1                        | TÉLÉPHONE                                   |
      | id_startrow_0                      | 0                                           |
      | id_startrow_1                      | 0                                           |
      | id_startcolumn_0                   | 0                                           |
      | id_startcolumn_1                   | 2                                           |
      | For any correct response           | Correct feedback                            |
      | id_orientation_0                   | 0                                           |
      | id_orientation_1                   | 1                                           |
      | For any partially correct response | Partially correct feedback.                 |
      | For any incorrect response         | Incorrect feedback.                         |
    Then I should see "crossword-006"
