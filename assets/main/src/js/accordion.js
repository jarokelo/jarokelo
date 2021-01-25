/* global $, site */
(function($, site) {
    'use strict';
    var defaults = {
        init: true,
        open: false,
        classes: {
            toggle: 'accordion__title',
            toggleActive: 'accordion__title--active',
            content: 'accordion__content',
            active: 'accordion__content--active'
        }
    };

    function Accordion(el, options) {
        this.el = el;
        this.$el = $(this.el);
        this.options = $.extend(true, {}, defaults, options || {});
        this.open = !this.options.open;
        this.$toggle = this.$el.find('.' + this.options.classes.toggle);
        this.$content = this.$el.find('.' + this.options.classes.content);
        if (this.options.open) {
            this.$toggle.addClass(this.options.classes.toggleActive);
            this.$content.addClass(this.options.classes.active);
        }
        if (this.options.init) {
            this.bind();
        }
    }

    Accordion.prototype.bind = function() {
        var _this = this;

        function click() {
            _this.open = !_this.open;
            if (_this.open) {
                _this.$toggle.removeClass(_this.options.classes.toggleActive);
                _this.$content.removeClass(_this.options.classes.active);
            } else {
                _this.$toggle.addClass(_this.options.classes.toggleActive);
                _this.$content.addClass(_this.options.classes.active);
            }
        }

        _this.$toggle.on('click', click);
    };
    // add to jQuery
    $.fn.accordion = function(options) {
        return this.each(function() {
            var $this = $(this);
            if (!$this.data('accordion')) {
                var config = $.extend(options, {});
                return $this.data('accordion', new Accordion(this, config));
            }
            return true;
        });
    };
    return $.extend(site, {
        Accordion: {
            init: function() {
                $('.accordion').accordion({});
            }
        }
    });
})(jQuery, site || {});
$(site.Accordion.init);
