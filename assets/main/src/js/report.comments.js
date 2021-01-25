/* global $, site, FB, ga */
(function($, site) {
    'use strict';

    var scrollTimer;
    var lastScrollFireTime = 0;
    var ie = site.Helper.isIE();
    var comments = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
        },
        cacheDOM: function() {
            this.sideCommentsTop = 0;
            this.sideCommentsHeight = 0;
            this.$sideComments = $('#comments-sidebar');
            this.$sideComments.data('height', this.$sideComments.outerHeight());
            this.$sideComments.data('width', this.$sideComments.outerWidth());
            this.$header = $('header.header');
            this.$reportDescription = $('#report-description');
        },
        bindEvents: function() {
            $(document).on('ready', this.loadScroll);
        },
        calcHeight: function() {
            var header = this.$header.outerHeight() - $(window).scrollTop();
            var calc = $(window).scrollTop() - (header > -1 ? header : 0) + $(window).height();
            return (comments.sideCommentsHeight + 20) > calc ? calc : (comments.sideCommentsHeight + 20);
        },
        headerDistanceDiff: function() {
            var top = this.$header.outerHeight() - $(window).scrollTop();
            return top > -1 ? top : 0;
        },
        calcScroll: function() {
            if (window.matchMedia('(min-width: 1080px)').matches && comments.$sideComments.length > 0) {
                comments.$sideComments.css({
                    position: ie ? '' : 'fixed',
                    top: comments.headerDistanceDiff(),
                    bottom: '',
                    height: comments.calcHeight(),
                    maxHeight: $(window).height(),
                    overflow: 'hidden'
                });

                if ((comments.$header.outerHeight() + comments.$reportDescription.outerHeight()) <= (comments.$sideComments.outerHeight() + comments.$sideComments.offset().top)) {
                    comments.$sideComments.css({
                        position: 'absolute',
                        top: '',
                        bottom: 0
                    });
                }
            }
        },
        handleScroll: function() {
            if (window.matchMedia('(min-width: 1080px)').matches && comments.$sideComments.length > 0) {
                if (ie) {
                    var minScrollTime = 100;
                    var now = new Date().getTime();

                    if (!scrollTimer) {
                        if (now - lastScrollFireTime > (3 * minScrollTime)) {
                            comments.calcScroll();
                            lastScrollFireTime = now;
                        }
                        scrollTimer = setTimeout(function() {
                            scrollTimer = null;
                            lastScrollFireTime = new Date().getTime();
                            comments.calcScroll();
                        }, minScrollTime);
                    }
                } else {
                    comments.calcScroll();
                }
            } else {
                comments.$sideComments.css({
                    position: '',
                    top: '',
                    bottom: '',
                    height: '',
                    width: '',
                    overflow: 'hidden'
                });
            }
        },
        loadScroll: function() {
            if (window.matchMedia('(min-width: 1080px)').matches && comments.$sideComments.length > 0) {
                if (!comments.sideCommentsTop) {
                    comments.sideCommentsTop = comments.$sideComments.offset().top;
                    comments.sideCommentsHeight = comments.$sideComments.outerHeight();
                }

                comments.$sideComments.mCustomScrollbar({
                    theme: 'dark',
                    scrollInertia: 250,
                    updateOnContentResize: true
                });

                comments.$sideComments.css({
                    position: 'absolute',
                    width: comments.$sideComments.data('width')
                });

                $(document).on('scroll init', comments.handleScroll).trigger('init');
                $(window).on('resize init', comments.handleScroll).trigger('init');
                $(window).on('resize', function() {
                    comments.$sideComments.mCustomScrollbar('update');
                });
            }
        }
    };

    comments.init();
})(jQuery, site);
