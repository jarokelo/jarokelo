$(function() {
    var statScroll = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
        },
        cacheDOM: function() {
            this.container = $('.statistics');
            this.header = $('header');
        },
        bindEvents: function() {
            $(document).ready(this.scrollTo);
        },
        scrollTo: function() {
            if (!window.matchMedia('(min-width: 1080px)').matches && $(statScroll.container).length) {
                $('html, body').animate({
                    scrollTop: $(statScroll.container).offset().top - statScroll.header.outerHeight()
                }, 700);
            }
        }
    };

    statScroll.init();
});
