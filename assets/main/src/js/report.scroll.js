$(function() {
    var rs = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
        },
        cacheDOM: function() {
            this.mainErrorBlock = $('#report-create-form > .error__sum');
            this.header = $('header');
            this.errorSelector = '.has-error';
        },
        bindEvents: function() {
            $(document).scroll(this.handleScroll);
        },
        handleScroll: function() {
            if (window.matchMedia('(min-width: 1080px)').matches) {
                if ($(rs.errorSelector).length > 0 && $(window).scrollTop() < rs.header.height()) {
                    rs.mainErrorBlock.css({
                        position: 'absolute',
                        top: rs.header.height() + 25
                    });
                } else {
                    rs.mainErrorBlock.css({
                        position: 'fixed',
                        top: 0
                    });
                }
            } else {
                rs.mainErrorBlock.css({
                    position: 'fixed',
                    top: rs.header.outerHeight() - 1
                });
            }
        }
    };

    rs.init();
});
