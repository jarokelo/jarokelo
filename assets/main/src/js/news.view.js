/* global loaderMessage, bootbox, site */
$(function() {
    var newsView = {
        init: function() {
            this.bindEvents();
        },
        bindEvents: function() {
            $(document).on('click', '.ajax-news-modal[data-url]', this.handler);
        },
        handler: function(e) {
            e.preventDefault();
            var $this = $(this);
            var url = $this.data('url');

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
                                modalClass: 'modal',
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

            ajaxCall();

            return false;
        }
    };

    newsView.init();
});
