$(function() {
    var rs = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
        },
        cacheDOM: function() {
            this.toc = $('.toc');
            this.toc.data('height', this.toc.outerHeight());
            this.toc.data('width', this.toc.outerWidth());
            this.header = $('header.header');
            this.footer = $('footer.footer');
        },
        bindEvents: function() {
            $(document).on('scroll ready', this.handleScroll);
            $(window).on('resize', this.handleScroll);
        },
        handleScroll: function() {
            if (rs.footer.length === 0) {
                return;
            }

            var windowTop = $(window).scrollTop();
            var limit = rs.footer.offset().top - rs.toc.data('height') - 30;

            if (window.matchMedia('(min-width: 1080px)').matches) {
                if (windowTop < rs.header.height()) {
                    rs.toc.css({
                        position: '',
                        width: ''
                    });
                } else {
                    rs.toc.css({
                        position: 'fixed',
                        width: rs.toc.data('width'),
                        top: 0
                    });
                }

                if (limit < windowTop) {
                    var diff = limit - windowTop;
                    rs.toc.css({
                        top: diff
                    });
                }
            } else {
                rs.toc.css({
                    position: ''
                });
            }
        }
    };

    rs.init();
});
