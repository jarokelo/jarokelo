/* global $, admin */
(function($, admin) {
    return $.extend(admin, {
        Modal: {
            init: function() {
                // Modal functions
                $(document).on('click', '.btn-modal-content', function(e) {
                    var $this = $(this);
                    return admin.Helper.queryHtmlContent($this, e, function() {
                        $($this.data('modal')).modal('show');
                    });
                });
                // Pjax functions
                $(document).on('change', '.change-pjax-submit input, .change-pjax-submit select', function() {
                    $(this).closest('.change-pjax-submit').submit();
                });
                $(document).on('keyup', '.change-pjax-submit input[type=text]', function() {
                    $(this).closest('.change-pjax-submit').submit();
                });
                $(document).on('pjax:end', '.pjax-hide-modal', function(e, xhr, options) {
                    var target = $(options.target).attr('id');
                    var dataTarget = $(this).data('pjax-target');
                    if (dataTarget && target !== dataTarget) {
                        return;
                    }
                    $($(this).data('modal')).modal('hide');
                });
                $(document).on('pjax:end', '#city-streets', function(e, xhr, options) {
                    var target = $(options.target).attr('id');
                    if (target !== 'street-create-ajax') {
                        return;
                    }
                    $('#street-grid-view-search').submit();
                });
                $(document).on('pjax:end', '.pjax-reload-other-pjax', function(e, xhr, settings) {
                    $.pjax.reload({
                        url: settings.requestUrl,
                        container: $(this).data('reload-pjax-selector'),
                        async: false,
                        push: false,
                        replace: false,
                        scrollTo: false,
                        timeout: 1000
                    });
                });
                $(document).on('click', '.btn-pjax-research', function(e) {
                    var $this = $(this);
                    $this.data('url', $this.attr('href'));
                    return admin.Helper.postData($this, null, e, function() {
                        $($this.data('search-form')).submit();
                        $($this.data('modal')).modal('hide');
                    });
                });
                $(document).on('click', '.btn-pjax-delete-confirm', function(e) {
                    var $this = $(this);
                    $this.data('url', $this.attr('href'));
                    return admin.Helper.postData($this, null, e, function() {
                        $.pjax({container: $this.data('pjax-container')});
                        $($this.data('modal')).modal('hide');
                    });
                });
                $(document).on('click', '.btn-reload-pjax-submit', function(e) {
                    var $this = $(this);
                    var $form = $this.closest('form');
                    if ($this.data('form')) {
                        $form = $($this.data('form'));
                    }
                    return admin.Helper.postData($this, $form.serializeArray(), e, function() {
                        $.pjax({container: $this.data('pjax-container')});
                        if ($this.data('modal')) {
                            $($this.data('modal')).modal('hide');
                        }
                    });
                });
            }
        }
    });
})($, admin || {});
$(document).ready(function() {
    admin.Modal.init();
});
