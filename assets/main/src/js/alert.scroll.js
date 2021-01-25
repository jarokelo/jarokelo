$(function() {
    var alertScroll = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
        },
        cacheDOM: function() {
            this.header = $('header');
        },
        bindEvents: function() {
            $(document).scroll(this.handleScroll);
        },
        handleScroll: function() {
            var alertBlock = $('.alert-flashes').first();
            if (window.matchMedia('(min-width: 1080px)').matches) {
                if ($(alertBlock).length > 0 && $(window).scrollTop() < alertScroll.header.outerHeight()) {
                    alertBlock.css({
                        top: alertScroll.header.outerHeight() - $(window).scrollTop()
                    });
                } else {
                    alertBlock.css({
                        top: 0
                    });
                }
            } else {
                alertBlock.css({
                    top: alertScroll.header.outerHeight()
                });
            }
        }
    };

    alertScroll.init();
});
