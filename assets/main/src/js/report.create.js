$(function() {
    var rc = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
            this.toggleErrorSummary();
            this.setDefaultAddressFieldsByMap();
        },
        cacheDOM: function() {
            this.mainErrorBlock = $('#report-create-form > .error__sum');
            this.form = $('#report-create-form');
            this.errorSelector = '.has-error';
            this.firstErrorSelector = $('.has-error').first();
            this.showOnMap = $('#report-create-form button.show-on-map:visible');
            this.$attachmentContainer = $('#draft-attachments');
        },
        bindEvents: function() {
            this.form.on('ajaxComplete', this.handleErrorSummary);
            $(document).on('click', '.remove-attachment', this.updateDraftAttachments);
            $('.user_location__clear').on('click', this.clearUserLocation);
        },
        handleErrorSummary: function(event, messages) {
            if (Object.keys(messages.responseJSON).length > 0 && $(rc.errorSelector).length > 0) {
                rc.mainErrorBlock.slideDown();
                $('html, body').animate({
                    scrollTop: $('.has-error').first().offset().top - 70
                }, 500);
            } else {
                rc.mainErrorBlock.slideUp();
            }
        },
        toggleErrorSummary: function() {
            if ($(rc.errorSelector).length > 0) {
                this.mainErrorBlock.slideDown();
            } else {
                this.mainErrorBlock.slideUp();
            }
        },
        setDefaultAddressFieldsByMap: function() {
            this.showOnMap.click();
        },
        updateDraftAttachments: function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).data('url'),
                type: 'post',
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success) {
                        rc.$attachmentContainer.hide().html(resp.html).fadeIn();
                    }
                    return true;
                },
                error: function() {
                }
            });
        },
        clearUserLocation: function(e) {
            e.preventDefault();
            var $userLocationField = $('#reportform-user_location');
            var currentValue = $userLocationField.val();
            var firstCommaLocation = currentValue.indexOf(',');
            if (firstCommaLocation > -1) {
                $userLocationField.val(currentValue.slice(0, firstCommaLocation + 1));
                $userLocationField.focus();
            }
        }
    };

    rc.init();
});
