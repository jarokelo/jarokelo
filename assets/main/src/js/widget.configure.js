/* global $, site */
(function($, site) {
    'use strict';

    return $.extend(site, {
        WidgetConfigure: {
            init: function() {
                $(document).on('change', '#customize-widget-form', function() {
                    $('#customize-widget-form').submit();
                });
            }
        }
    });
})(jQuery, site || {});

$(document).ready(function() {
    site.WidgetConfigure.init();
});
