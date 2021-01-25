/* global $, site */
(function($, site) {
    'use strict';
    return $.extend(site, {
        Donate: {
            init: function() {
                var donateForm = $('#paypal-donate-form');
                var subscribeForm = $('#paypal-subscribe-form');
                var donateFormVal = donateForm.find('input[name="amount"]');
                var subscribeFormVal = subscribeForm.find('input[name="a3"]');
                var donateFrequency = $('#donate-frequency');
                var donateBtn = $('#donate-box-paypal .button--donate');
                var donateOtherAmount = $('#paypal-other-amount');
                var donateAmount = $('#paypal-other-amount').find('input[name="amount"]');
                var submitBtn = $('#paypal-donate-submit');

                $('.donationbox').on('click', function() {
                    var self = this;
                    donateFormVal.val($(self).data('value'));
                    donateForm.submit();
                });

                var active = donateBtn.filter(function(i, value) {
                    return $(value).hasClass('active');
                });
                var activeVal = active.data('value');
                donateAmount.val(activeVal);

                donateBtn.on('click', function() {
                    var $this = $(this);
                    var value = $this.data('value');

                    if ($this.hasClass('button-donate-other')) {
                        donateAmount.val('');
                        donateOtherAmount.show();
                    } else {
                        donateOtherAmount.hide();
                        donateAmount.val(value);
                    }

                    donateAmount.trigger('input');

                    subscribeFormVal.val(donateAmount.val());
                    donateFormVal.val(donateAmount.val());

                    // highlight active button
                    donateBtn.removeClass('active');
                    $this.addClass('active');
                });

                donateAmount.on('input', function() {
                    var $this = $(this);

                    $this.val(parseInt($this.val(), 10) || '');

                    if ($this.val() > 0) {
                        submitBtn.prop('disabled', false);
                    } else {
                        submitBtn.prop('disabled', true);
                    }
                });

                submitBtn.on('click', function(e) {
                    e.preventDefault();

                    if (+donateAmount.val() > 0) {
                        if (donateFrequency.find(':checked').val() === 'monthly') {
                            subscribeFormVal.val(donateAmount.val());
                            subscribeForm.submit();
                        } else {
                            donateFormVal.val(donateAmount.val());
                            donateForm.submit();
                        }
                    }
                });
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Donate.init();
});
