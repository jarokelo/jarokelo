/* global $, site */
(function($, site) {
    'use strict';
    var defaults = {
        $$common: {
            init: true,
            removeText: true,
            label: '', // label text
            css: '', // extra css to template
            append: 'wrap'
        },
        radio: {
            selector: '[type="radio"]',
            prefix: 'radio-',
            template: '<div class="radio"></div>',
            check: '<div class="radio__check"></div>',
            deselectable: false,
            classes: {
                label: 'radio__label',
                checked: 'radio--checked',
                hover: 'radio--hover'
            }
        },
        checkbox: {
            selector: '[type="checkbox"]',
            prefix: 'checkbox-',
            template: '<div class="checkbox"></div>',
            check: '<div class="checkbox__check"></div>',
            deselectable: true,
            classes: {
                label: 'checkbox__label',
                checked: 'checkbox--checked',
                hover: 'checkbox--hover'
            }
        }
    };
    var InputGroup = {};

    function Input(el, options, type) {
        if (!type && !defaults[type]) {
            return;
        }
        var time = new Date().getTime();
        var name = el.getAttribute('name');
        if (!name) {
            name = type + '_' + time;
        }
        var id = el.getAttribute('id');
        if (!id) {
            id = type + '_' + time;
            el.setAttribute('id', id);
        }
        this.options = $.extend(true, {}, defaults.$$common, defaults[type], options || {});
        this.el = el;
        this.$el = $(this.el);
        this.type = type;
        this.checked = this.el.checked;
        this.group = name;
        (InputGroup[name] = (InputGroup[name] || [])).push(el);
        this.$template = $(this.options.template);
        this.$template.data(type, this);
        if (this.checked) {
            this.$template.addClass(this.options.classes.checked);
        }
        if (this.options.init) {
            this.hide();
            this.insertTemplate();
            this.bindChange();
        }
    }

    Input.prototype.hide = function() {
        this.$el.hide();
    };
    Input.prototype.insertTemplate = function() {
        var $input = this.$el;
        if (this.options.css) {
            this.$template.addClass(this.options.css);
        }
        switch (this.options.append) {
            case 'after':
                $input.after(this.$template);
                break;
            case 'before':
                $input.before(this.$template);
                break;
            case 'wrap':
                this.$template = $input.wrap(this.$template).parent();
                if (this.options.removeText) {
                    this.$template.parent().contents().filter(function() {
                        return this.nodeType === 3;
                    }).remove();
                }
                break;
            default:
                var $append = $(this.options.append);
                if (!$append.length) {
                    throw Error('Invalid append');
                }
                $append.append(this.$template);
        }
        this.$template.find(defaults[this.type].selector).after(this.options.check);
        if (this.options.label) {
            this.$template.after('<label for="' + $input.attr('id') + '" class="' + this.options.classes.label + '">' + this.options.label + '</label>');
        }
    };
    Input.prototype.bindChange = function() {
        var _this = this;
        var $template = _this.$template;
        var $input = _this.$el;
        var $label = $input.closest('label');
        if (!_this.$template) {
            throw Error('Missing template');
        }
        function click(event) {
            event.preventDefault();
            event.stopPropagation();
            $input.trigger('change:checked');
        }

        function mouseenter() {
            _this.$template.addClass(_this.options.classes.hover);
        }

        function mouseleave() {
            _this.$template.removeClass(_this.options.classes.hover);
        }

        function change() {
            InputGroup[_this.group].map(function(input) {
                if (_this.el === input) {
                    if (_this.options.deselectable) {
                        _this.checked = !_this.checked;
                        setTimeout(function() {
                            $input.prop('checked', _this.checked);
                            if (_this.checked) {
                                _this.$template.addClass(_this.options.classes.checked);
                            } else {
                                _this.$template.removeClass(_this.options.classes.checked);
                            }
                        }, 0);
                    } else {
                        /* eslint-disable no-lonely-if */
                        if (!_this.checked) {
                            _this.checked = true;
                            setTimeout(function() {
                                $input.prop('checked', true);
                                _this.$template.addClass(_this.options.classes.checked);
                            }, 0);
                        }
                    }
                    $input.trigger('change');
                    // deselect other inputs under same group
                } else {
                    var $otherInput = $(input);
                    var otherInstance = $otherInput.data(_this.type);
                    otherInstance.checked = false;
                    setTimeout(function() {
                        $otherInput.prop('checked', false);
                        otherInstance.$template.removeClass(_this.options.classes.checked);
                    }, 0);
                    $otherInput.trigger('change');
                }
            });
        }

        $input.on('click', click);
        $input.on('change:checked', change);
        $template.on('click', click);
        $template.on('mouseenter', mouseenter);
        $template.on('mouseleave', mouseleave);
        $label.on('mouseenter', mouseenter);
        $label.on('mouseleave', mouseleave);
        $label.on('click', click);
    };
    // add to jQuery
    $.fn.radio = $.fn.checkbox = function(options) {
        return this.each(function() {
            var $this = $(this);
            var type = $this.attr('type');
            var autoDiscover = $this.attr(defaults[type].prefix + 'autodiscover');
            var enabled = autoDiscover ? !!+autoDiscover === true : true;

            if (enabled && defaults[type] && !$this.data(type)) {
                var config = $.extend(options, {
                    append: $this.attr(defaults[type].prefix + 'append'),
                    label: $this.attr(defaults[type].prefix + 'label'),
                    css: $this.attr(defaults[type].prefix + 'css')
                });
                return $this.data(type, new Input(this, config, type));
            }
            return true;
        });
    };
    return $.extend(site, {
        Checkbox: {
            init: function() {
                $('[type="checkbox"]').checkbox();
            }
        },
        Radio: {
            init: function() {
                $('[type="radio"]').radio();
            }
        }
    });
})(jQuery, site || {});
$(site.Checkbox.init);
$(site.Radio.init);
