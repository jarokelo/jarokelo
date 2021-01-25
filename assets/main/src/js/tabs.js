/* global $, site */
(function($, site) {
    'use strict';

    return $.extend(site, {
        Tabs: {
            init: function() {
                var active = 'report__tabs__header__link--active';
                var $tab = $('.report__tabs__header__item [href^=#]');
                var $tabs = $('[tab]');

                $tab.each(function() {
                    var $this = $(this);
                    var $target = $('[tab="' + $this.attr('href').substring(1) + '"]');

                    if ($target.length) {
                        $this.on('click', function(e) {
                            e.preventDefault();
                            $tab.removeClass(active);
                            $tabs.hide();
                            $this.addClass(active);
                            $target.show();
                        });
                    }
                });
            }
        }
    });
})(jQuery, site || {});

$(document).ready(function() {
    site.Tabs.init();
});
