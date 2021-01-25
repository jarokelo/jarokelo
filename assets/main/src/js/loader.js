/* global $, site, loaderMessage */
(function($, site) {
    'use strict';

    function formLoaderOverlay() {
        if (!$('overlay').length) {
            $(document)
                .find('body')
                .append('<div class="overlay">' +
                    '<div class="loader">' + loaderMessage + '</div>' +
                    '</div>');
        }
        $('.overlay').fadeIn();
    }

    function closeOverlay() {
        $(document)
            .find('.overlay')
            .fadeOut(function() {
                $(this).remove();
            });
    }

    return $.extend(site, {
        Loader: {
            init: function() {
                $(document).on('submit', 'form.init-loader', formLoaderOverlay);
                $(document).on('click', 'a.init-loader', formLoaderOverlay);
                $(document).on('click', '.init-loader-button', formLoaderOverlay);
                $(document).on('click', '.overlay', closeOverlay);
                $(document).on('ajaxComplete', closeOverlay);
                $(document).on('pjax:end', closeOverlay);
            },
            show: formLoaderOverlay
        }
    });
})(jQuery, site || {});

$(document).ready(function() {
    site.Loader.init();
});
