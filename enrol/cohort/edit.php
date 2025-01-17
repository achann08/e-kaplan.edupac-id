<?php 

require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

function cohort_add_course($courseid, $cohortid) {
    global $DB;

    if (!enrol_is_enabled('cohort')) {
        // Not enabled.
        return false;
    }

    if ($DB->record_exists('enrol', array('courseid' => $courseid, 'enrol' => 'cohort'))) {
        // The course already has a cohort enrol method.
        return false;
    }

    // Get the cohort enrol plugin
    $enrol = enrol_get_plugin('cohort');

    // Get the course record.
    $course = $DB->get_record('course', array('id' => $courseid));

    // Add a cohort instance to the course.
    $instance = array();
    $instance['name'] = 'custom instance name - can be blank';
    $instance['status'] = ENROL_INSTANCE_ENABLED; // Enable it.
    $instance['customint1'] = $cohortid; // Used to store the cohort id.
    $instance['roleid'] = $enrol->get_config('roleid'); // Default role for cohort enrol which is usually student.
    $instance['customint2'] = 0; // Optional group id.
    $enrol->add_instance($course, $instance);

    // Sync the existing cohort members.
    $trace = new null_progress_trace();
    enrol_cohort_sync($trace, $course->id);
    $trace->finished();
}


?>