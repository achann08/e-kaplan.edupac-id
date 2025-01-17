<?php
class block_simple_floatingbutton extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_simple_floatingbutton');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        // Load the JavaScript module using index.js
        $this->page->requires->js_call_amd('block_simple_floatingbutton/index', 'init');
        $this->page->requires->css('/blocks/simple_floatingbutton/css/build/floatingbutton.min.css');
        
        //load bootstrap icons
        $this->page->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css'));

        return $this->content;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function applicable_formats() {
        return array(
            'all' => true,
            'course-view' => true,
            'mod-quiz' => true, // Allow block in quizzes
            'site' => true,
            'my' => true
        );
    }

    public function specialization() {
        // Include the styles.css file
        $this->page->requires->css('/blocks/simple_floatingbutton/styles.css');
    }
}