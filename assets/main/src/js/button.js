/* global $ */
/* eslint-disable valid-jsdoc */
var Button = (function($) {
    'use strict';

    /**
     * @param {string} form
     */
    var disableAfterSubmit = function(form) {
        if (typeof form !== 'string') {
            return;
        }

        $(form).on('beforeSubmit', function() {
            $('.disable-after-submit').prop('disabled', true);
            return true;
        });
    };

    return {
        disableAfterSubmit: disableAfterSubmit
    };
})(jQuery || {});
Button.inited = true;
