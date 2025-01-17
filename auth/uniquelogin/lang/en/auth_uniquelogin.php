<?php
$string['pluginname'] = 'Unique Login';
$string['auth_uniquelogintitle'] = 'Unique login';
$string['auth_uniquelogerror'] = 'There is already an active session so it is not possible to login.';
$string['auth_uniquelogindescription'] = 'This plugin ensures thar each user only has one active session.<br /><br />Every time a user makes a successful login, all other sessions belonging to that user will be terminated.<br><br /><div style="font-weight: bold;">Note 1: For this plugin to work, the user sessions must be stored on the database. This configuration is set in <a href="settings.php?section=sessionhandling">Sessions.</a></div><br />';
$string['aplly_to_admin'] = 'Apply to Administrators';
$string['configaplly_to_admin'] = 'Apply the unique login restriction to users with Administrator role in the system context.';
$string['aplly_to_teacher'] = 'Apply to Teachers';
$string['configaplly_to_teacher'] = 'Apply the unique login restriction to users with Teacher role in any Moodle course.';
?>
