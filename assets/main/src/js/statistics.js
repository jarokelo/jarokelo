/* global $, site, autosize */
(function($, site) {
    'use strict';

    return $.extend(site, {
        Statistics: {
            init: function() {
                $(document).on('change', '#statistics-city-filter-form select, #statistics-city-category-filter-form select, #statistics-institution-filter-form select, #statistics-institution-category-filter-form select', function() {
                    $(this).closest('form').submit();
                });
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Statistics.init();
});
