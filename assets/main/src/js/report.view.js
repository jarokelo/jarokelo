/* global loaderMessage, bootbox, site */
$(function() {
    var reportView = {
        init: function() {
            this.bindEvents();
        },
        bindEvents: function() {
            $(document).on('click', '.ajax-modal[data-url]', this.handler);
            $(document).on('click', 'button.comment__more', this.showMore);
        },
        showMore: function() {
            var $this = $(this);
            var commentID = $this.data('id');
            var span = $('.more-sign[data-id="' + commentID + '"]');
            var restComment = $('.comment__rest-part[data-id="' + commentID + '"]');

            if (!$this.hasClass('ajax-modal')) {
                $this.remove();
                span.remove();
            }
            restComment.fadeIn();
        },
        handler: function(e) {
            e.preventDefault();
            var $this = $(this);
            var url = $this.data('url');
            var confirmMsg = $this.data('confirm-message');

            var ajaxCall = function() {
                $.ajax({
                    url: url,
                    dataType: 'json',
                    beforeSend: function() {
                        site.Loader.show();
                    },
                    success: function(data) {
                        $('<div>' + data.html + '</div>')
                            .modal({
                                escapeClose: true,
                                clickClose: true,
                                showClose: true,
                                fadeDuration: 200,
                                modalClass: 'modal modal--offset',
                                closeText: ''
                            })
                            .on($.modal.CLOSE, function() {
                                $this.fadeIn();
                            });
                    },
                    error: function(err) {
                        console.log(err);
                    }
                });
            };

            // Show message before ajaxcall
            if (confirmMsg) {
                bootbox.confirm({
                    message: confirmMsg,
                    buttons: {
                        confirm: {
                            label: 'OK'
                        },
                        cancel: {
                            label: 'Cancel'
                        }
                    },
                    callback: function(confirmed) {
                        if (confirmed) {
                            ajaxCall();
                        }
                    }
                });
                return false;
            }
            ajaxCall();

            return false;
        }
    };

    reportView.init();
});
