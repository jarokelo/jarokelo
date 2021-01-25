/* global $, FB, svg4everybody */
var site = (function($) {
    'use strict';
    return {
        // we can store here the current page's URL to share by clicking on the social buttons
        urlToShare: '',
        syncInput: function() {
            $('[sync]').each(function() {
                var $this = $(this);
                var $target = $($this.attr('sync'));
                var el = $target.prop('tagName').toLowerCase();

                switch (el) {
                    case 'textarea':
                        $target.on('input sync', function(e) {
                            var $origin = $(e.target || e.srcElement || e.originalTarget);
                            $this.text($origin.val());
                        });
                        break;
                    case 'input':
                        $target.on('input sync', function(e) {
                            var $origin = $(e.target || e.srcElement || e.originalTarget);
                            $this.val($origin.val());
                        });
                        break;
                    case 'select':
                        $target.on('input sync', function(e) {
                            var $origin = $(e.target || e.srcElement || e.originalTarget);
                            $this.val($origin.find(':selected').text());
                        });
                        break;
                    default:
                }

                $target.trigger('sync');
            });
        },
        message: function(options) {
            var config = $.extend(true, {
                text: '',
                ajaxComplete: '',
                type: 'success'
            }, options || {});

            function showMessage() {
                var alert = $('<div class="alert-flashes"><div class="alert alert-' + config.type + '" role="alert">' + config.text + '</div></div>');

                $('main').prepend(alert);
                site.hideAlert();
                $(document).trigger('scroll');
            }

            if (config.ajaxComplete) {
                $(document).on('ajaxComplete', function(event, messages) {
                    var target = messages.responseJSON[config.ajaxComplete];
                    if (target && target[0]) {
                        showMessage();
                    }
                });
            } else {
                showMessage();
            }
        },
        hideOnClick: function() {
            $('[click-hide]').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);

                $this.closest($this.attr('click-hide')).hide();
            });
        },
        hideAlert: function() {
            setTimeout(function() {
                $('.alert-flashes .alert').fadeOut();
            }, 4000);
        },
        togglePassword: function(scope) {
            $('[toggle-password]').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $scope = scope ? $this.closest(scope).find('input:first') : $this;

                if ($scope.attr('type') === 'text') {
                    $scope.attr('type', 'password');
                } else {
                    $scope.attr('type', 'text');
                }
            });
        },
        init: function() {
            site.hideOnClick();
            site.hideAlert();
            site.syncInput();
            site.togglePassword('.input-group');
            svg4everybody({});
            $('.owl-carousel').owlCarousel({
                items: 1,
                thumbs: true,
                thumbsPrerendered: true,
                dots: false,
                nav: true,
                navText: '',
                navContainer: '.owl-nav'
            });
        }
    };
})(jQuery);
$(document).ready(function() {
    site.init();
});
