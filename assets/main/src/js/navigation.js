(function($) {
    'use strict';
    var selector = '.js--toggle-navigation';
    var navigationSelector = '.navigation';
    var activeClass = 'navigation--active';
    var bodyActiveClass = 'body--navigation-opened';
    var $navigationElement;
    var $body = $('body');

    function init() {
        $navigationElement = $(navigationSelector);

        $(document).on('click', selector, function(event) {
            event.preventDefault();

            $navigationElement.toggleClass(activeClass);
            $body.toggleClass(bodyActiveClass);
        });
    }

    init();
})(jQuery);
