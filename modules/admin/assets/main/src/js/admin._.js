/* global $, keyboardJS, JarokeloMap */
var admin = (function($) {
    function modalIsOpen() {
        return $('body').hasClass('modal-open');
    }

    function inputHasFocus() {
        return $('input').is(':focus');
    }

    function initActivity() {
        $(document).on('click', '.tr-activity', function(e) {
            var $origin = $(e.target || e.srcElement || e.originalTarget);

            var $this = $(this);

            if (!$origin.attr('href') && !$origin.hasClass('glyphicon-ok')) {
                window.open($this.find('.view-report').attr('href'), '_self');
            }

            if ($origin.hasClass('approve-report')) {
                return;
            }

            if ($this.find('.assign-report').length > 0) {
                $this.find('.assign-report').trigger('click');
            }
        });
    }

    function initApprove() {
        $(document).on('click', '.approve-report', function(e) {
            e.preventDefault();
            var buttonTextLoading = 'Loading...';
            var buttonTextError = '<i class="glyphicon glyphicon-alert"></i>';
            var $link = $(this);
            $link.html(buttonTextLoading);
            var url = $link.attr('href') || '#';

            $.ajax({
                url: baseUrl + url,
                method: 'GET',
                dataType: 'json'
            }).done(function(data) {
                var $tr = $link.closest('tr');
                var $tds = $tr.find('td');
                if (data.success) {
                    $tds.slideUp();
                    $tr.slideUp();
                } else {
                    $tr.addClass('alert-danger');
                    $link.html(buttonTextError);
                }
            });
        });
    }

    function initCommentApprove() {
        $(document).on('click', '.approve-comment', function(e) {
            e.preventDefault();
            var buttonTextLoading = 'Loading...';
            var buttonTextError = '<i class="glyphicon glyphicon-alert"></i>';
            var $link = $(this);
            $link.html(buttonTextLoading);
            var url = $link.attr('href') || '#';

            $.ajax({
                url: baseUrl + url,
                method: 'GET',
                dataType: 'json'
            }).done(function(data) {
                var $div = $link.closest('[data-key]');

                if (data.success) {
                    $div.slideUp();
                } else {
                    $div.addClass('alert-danger');
                    $link.html(buttonTextError);
                }
            });
        });
    }

    function initHotkeys() {
        // open hotkeys help modal - everywhere
        function hotkeysModal() {
            if (!modalIsOpen() && !inputHasFocus()) {
                $('#hotkeysModal').modal();
            }
        }

        keyboardJS.bind('?', hotkeysModal);
        keyboardJS.bind('shift + ,', hotkeysModal);

        // report-related hotkeys only on the report view page
        if ($('.report-full').length > 0) {
            var mapping = {
                's': '.btn-change-status',            // change status
                'e': '.btn-edit',                     // edit
                'm': '.btn-send-report',              // send report
                'u': '.btn-upload-answer',            // upload answer
                'x': '.btn-delete',                   // delete report
                'o': '#report-unique-name-link'       // open public link
            };
            $.each(mapping, function(key, selector) {
                keyboardJS.bind(key, function() {
                    if (!modalIsOpen() && !inputHasFocus()) {
                        $(selector)[0].click();
                    }
                });
            });
        }
        /*
         // report-related hotkeys only on the report update page
         if ($('.update-report-form').length > 0) {
         keyboardJS.bind('c', function() {
         $('.btn-compare-content')[0].click();
         });
         }
         */
    }

    return {
        init: function() {
            // init tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // init hotkeys
            initHotkeys();
            initActivity();
            initApprove();
            initCommentApprove();

            // there is a bug with Google Maps, the embedded map doesn't show up in a modal
            // after the modal was shown, hidden and shown again
            $('.modal').on('shown.bs.modal', function() {
                if (window.mapInitData) {
                    JarokeloMap.showPlace();
                }
            });
        }
    };
})($);
$(document).ready(function() {
    admin.init();
    Dropzone.autoDiscover = false;
});
