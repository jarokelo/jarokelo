/* global $, site, FB, ga */
(function($, site) {
    'use strict';

    return $.extend(site, {
        Helper: {
            purifyStringDOM: function(string) {
                return (string || '').replace(/(\r\n|\n|\r)/gm, '');
            },
            isIE: function(userAgent) {
                userAgent = userAgent || navigator.userAgent;
                return userAgent.indexOf('MSIE ') > -1 || userAgent.indexOf('Trident/') > -1 || userAgent.indexOf('Edge/') > -1;
            }
        }
    });
})(jQuery, site || {});
