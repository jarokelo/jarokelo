/* global $, admin */
(function($, admin) {
    return $.extend(admin, {
        City: {
            init: function() {
                // Rule modal
                $(document).on('select2:select', '.load-institution-contacts', function(e) {
                    var $this = $(this);
                    var origUrl = $this.data('url');
                    $this.data('url', origUrl.replace('ph', e.params.data.id));
                    $('.show-on-contact-load').addClass('hidden');
                    $('.institution-note').addClass('hidden');
                    $('.contact-container').html('');
                    $('.institution-note-container').html('');
                    admin.Helper.queryHtmlContent($this, e, function(data) {
                        $this.data('url', origUrl);
                        $('.show-on-contact-load').removeClass('hidden');
                        if (data) {
                            data.note = data.note || '';
                            if (data.note === '') {
                                return;
                            }
                            $('.institution-note').removeClass('hidden');
                            $('.institution-note-container').html(data.note);
                        }
                    });
                });
            }
        }
    });
})($, admin || {});

$(document).ready(function() {
    admin.City.init();
});
