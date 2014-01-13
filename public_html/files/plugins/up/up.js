jQuery(function () {
jQuery(window).scroll(function () {
    if (jQuery(this).scrollTop() > 100)
        jQuery('a#move_up').fadeIn(600);
    else
        jQuery('a#move_up').fadeOut(600);
});
jQuery('a#move_up').click(function () {
    jQuery('body,html').animate({
        scrollTop: 0
    }, 600);
    return false;
});
});
