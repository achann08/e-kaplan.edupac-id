<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configcheckbox('auth_uniquelogin_aplly_to_admin', get_string('aplly_to_admin', 'auth_uniquelogin'),
                       get_string('configaplly_to_admin', 'auth_uniquelogin'), 0));
                       
	$settings->add(new admin_setting_configcheckbox('auth_uniquelogin_aplly_to_teacher', get_string('aplly_to_teacher', 'auth_uniquelogin'),
                       get_string('configaplly_to_teacher', 'auth_uniquelogin'), 0));
}
