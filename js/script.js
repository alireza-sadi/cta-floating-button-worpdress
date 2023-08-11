// JavaScript for controlling button behavior is already in the main plugin file.

jQuery(document).ready(function($) {
    var floatingButton = $('#floating-button');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 30) {
            floatingButton.addClass('visible');
        } else {
            floatingButton.removeClass('visible');
        }
    });
});
