define(['jquery', './dictionary', './floatingbutton'], function($, dictionary, floatingbutton) {
    $(document).ready(function() {
        floatingbutton.init();
        dictionary.init();
    });
});