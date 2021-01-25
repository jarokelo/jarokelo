/* script for expand / collapse content blocks */
/* global $, site */

(function($, site) {
    'use strict';

    var toggleBtn = $('.content-toggle');
    var wrapper = $('.expandable-wrapper');
    var content = $('.expandable-content');

    function expand() {
        wrapper.addClass('is-expanded');
        toggleBtn.addClass('js-expanded-context');
        wrapper.height(content.outerHeight(true) + 50);
    }

    function collapse() {
        wrapper.removeClass('is-expanded');
        toggleBtn.removeClass('js-expanded-context');
        wrapper.height(0);
    }

    function toggle() {
        if (!wrapper.hasClass('is-expanded')) {
            expand();
        } else {
            collapse();
        }
    }

    return $.extend(site, {
        Collapse: {
            init: function() {
                toggleBtn.on('click', toggle);
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Collapse.init();
});
