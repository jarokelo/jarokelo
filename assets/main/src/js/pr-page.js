$(function() {
    $(window).scroll(function() {
        var sticky = $('.sticky-header');
        var scroll = $(window).scrollTop();
        var margin = $('.sticky-header__after');

        if ($(window).width() > 991) {
            if (scroll >= 90) {
                sticky.addClass('sticky-header-fixed');
                margin.addClass('margin-top');
            } else {
                sticky.removeClass('sticky-header-fixed');
                margin.removeClass('margin-top');
            }
        }
    });
});
