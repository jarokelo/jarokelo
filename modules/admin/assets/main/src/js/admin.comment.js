/* global $, admin */
(function($, admin) {
    return $.extend(admin, {
        Comment: {
            init: function() {
                if ($('.comment__text').length) {
                    $('.comment__text').each(function() {
                        if ($(this).text().length > 160) {
                            $(this).siblings('.comment__more').show();
                        }
                    });
                }
                $(document).on('click', '.comment__more', function() {
                    var $this = $(this);
                    $this.siblings('.comment__text').toggleClass('comment__text--full');
                    $this.hide();
                    $this.siblings('.comment__less').show();
                });
                $(document).on('click', '.comment__less', function() {
                    var $this = $(this);
                    $this.siblings('.comment__text').toggleClass('comment__text--full');
                    $this.hide();
                    $this.siblings('.comment__more').show();
                });
            }
        }
    });
})($, admin || {});
$(document).ready(function() {
    admin.Comment.init();
});
