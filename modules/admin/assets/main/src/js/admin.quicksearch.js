/* global $, admin */
(function($, admin) {
    function hideBox() {
        var $dropdown = $('#quick-search-dropdown');
        var $container = $('.quick-search-container');
        $dropdown.addClass('hidden');
        $container.html('');
    }
    return $.extend(admin, {
        QuickSearch: {
            init: function() {
                $(document).on('keyup', '#quick-search input', function() {
                    var $dropdown = $('#quick-search-dropdown');
                    var $container = $('.quick-search-container');
                    var $this = $(this);
                    var val = $this.val();
                    if (val.length === 0) {
                        hideBox();
                        return true;
                    }
                    if (val.length < 3) {
                        return true;
                    }
                    var timeOut = $dropdown.data('timeout');
                    if (timeOut) {
                        clearTimeout(timeOut);
                    }
                    $dropdown.data('timeout', setTimeout(function() {
                        $dropdown.data('timeout', null);
                        $.get($dropdown.data('url'), $this.closest('form').serialize(), function(data) {
                            if (data && data.success) {
                                $container.html(data.html);
                                $container.find('.quick-search-row').click(function(event) {
                                    document.location.href = $(event.currentTarget).data('href');
                                });
                                $dropdown.removeClass('hidden');
                            } else {
                                hideBox();
                            }
                        });
                    }, 350));
                    return true;
                });
                $(document).on('blur', '#quick-search input', function() {
                    setTimeout(function() {
                        hideBox();
                    }, 350);
                });
            }
        }
    });
})($, admin || {});
$(document).ready(function() {
    admin.QuickSearch.init();
});
