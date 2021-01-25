/* global $, site, FB, ga */
(function($, site) {
    'use strict';
    function trackSocial(socialNetwork, socialTarget) {
        if (typeof ga !== 'undefined') {
            ga('send', {
                hitType: 'social',
                socialNetwork: socialNetwork,
                socialAction: 'share',
                socialTarget: socialTarget
            });
        }
    }

    return $.extend(site, {
        Share: {
            init: function() {
                /* Facebook share button init */
                $('.button--facebook--share').click(function(e) {
                    e.preventDefault();
                    trackSocial('Facebook', site.urlToShare);
                    FB.ui({
                        method: 'share',
                        href: site.urlToShare
                    });
                });
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Share.init();
});
