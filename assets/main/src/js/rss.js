/* global $, site */
(function($, site) {
    'use strict';

    return $.extend(site, {
        Rss: {
            init: function() {
                $(document).on('change', '#rss-submit-form select', function() {
                    $('#rss-submit-form').submit();
                });
            }
        }
    });
})(jQuery, site || {});

$(document).ready(function() {
    site.Rss.init();
});
