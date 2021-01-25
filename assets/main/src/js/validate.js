/* global $, site */
(function($, site) {
    'use strict';
    var defaults = {
        init: true,
        selector: '[validate]',
        prefix: 'validate-',
        css: '',
        scope: 'this',
        preset: {
            email: /\S+@\S+\.\S+/
        },
        classes: {
            valid: 'validate--valid',
            invalid: 'validate--invalid'
        }
    };

    function Validate(el, options, $source) {
        this.el = el;
        this.$el = $(this.el);
        this.$source = $source;
        this.$scope = $('');
        this.validations = [];
        this.options = $.extend(true, {}, defaults, options);
        if (this.options.init) {
            this.validateScope();
            this.bindEvents();
        }
    }

    Validate.prototype.validateScope = function() {
        var _this = this;
        if (_this.$source) {
            _this.$scope = _this.$source;
        } else {
            switch (_this.options.scope) {
                case 'this':
                    this.$scope = _this.$el;
                    break;
                case 'next':
                    this.$scope = _this.$el.next();
                    break;
                case 'parent':
                    this.$scope = _this.$el.parent();
                    break;
                default:
                    this.$scope = _this.$el.closest(_this.options.scope);
                    if (!_this.$scope.length) {
                        throw Error('Invalid scope');
                    }
            }
        }
        _this.$scope.find('[valid]').each(function() {
            _this.attachValidations($(this));
        });
    };
    Validate.prototype.getValidation = function(validationType) {
        var _this = this;
        var validation = /.*/;
        switch (validationType) {
            case 'email':
                validation = _this.options.preset.email;
                break;
            default:
                validation = validationType instanceof RegExp ? validationType : new RegExp(validationType);
        }
        return validation;
    };
    Validate.prototype.attachValidations = function($valid) {
        var _this = this;
        var valid = $valid.attr('valid');
        $valid.each(function() {
            var $this = $(this);
            $this.data('validate-valid', _this.getValidation(valid));
            $this.data('validate-scope', $valid[0] === _this.el ? _this.$scope : $this);
            _this.validations.push($this);
        });
    };
    Validate.prototype.bindEvents = function() {
        var _this = this;
        this.$el.on('input', function() {
            var $this = $(this);
            var val = $this.val();
            _this.validations.forEach(function($value) {
                var isValid = $value.data('validate-valid').test(val);
                var $scope = $value.data('validate-scope');
                if (isValid) {
                    $scope.addClass(_this.options.classes.valid);
                    $scope.removeClass(_this.options.classes.invalid);
                } else {
                    $scope.removeClass(_this.options.classes.valid);
                    $scope.addClass(_this.options.classes.invalid);
                }
            });
        });
    };
    // add to jQuery
    $.fn.validate = function(options) {
        return this.each(function() {
            var $this = $(this);
            var validate = $this.attr('validate');
            var $target = $(validate);
            var config = $.extend(options, {
                css: $this.attr(defaults.prefix + 'css'),
                scope: $this.attr(defaults.prefix + 'scope')
            });
            if ($target.length) {
                return $target.each(function() {
                    if (!$(this).data('validate')) {
                        return $(this).data('validate', new Validate(this, config, $this));
                    }
                    return true;
                });
            }
            if (!$this.data('validate')) {
                return $this.data('validate', new Validate(this, config));
            }
            return true;
        });
    };
    return $.extend(site, {
        Validate: {
            init: function() {
                $('input[validate]').validate({
                    scope: '.input-group',
                    classes: {
                        valid: 'input--valid',
                        invalid: 'input--invalid'
                    }
                });
                $('[validate]').validate();
            }
        }
    });
})(jQuery, site || {});
$(site.Validate.init);
