/* global $, admin, google, key */
(function($, admin) {
    'use strict';
    function initFunctions() {
        // lightbox for pictures and videos
        $('.gallery').lightGallery({
            selector: '.lightbox',
            animateThumb: false,
            showThumbByDefault: false
        });

        // Status change modal
        $(document).on('change', '#statuschange-status', function() {
            admin.Helper.statusChange($(this).val());
        });
        // Init the form elements
        admin.Helper.statusChange($('#statuschange-status').val());
        // Street modal
        $(document).on('change', '#street-postcode', function() {
            var val = $(this).val();
            if (typeof val !== 'string') {
                return;
            }
            var $select = $('#street-district_id');
            var optionVal = $select.find('option[data-number=' + parseInt(val.substr(1, 2), 10) + ']').val();
            $select.val(optionVal).trigger('change');
        });

        // Load institution note on the Report edit page, when changing institution
        $(document).on('select2:select', '.load-institution-note', function(e) {
            var $this = $(this);
            var origUrl = $this.data('url');
            $this.data('url', origUrl.replace('ph', e.params.data.id));
            $('.institution-note').addClass('hidden');
            $('.institution-note-container').html('');
            admin.Helper.queryHtmlContent($this, e, function(data) {
                $this.data('url', origUrl);
                if (data) {
                    data.note = data.note || '';
                    if (data.note === '') {
                        return;
                    }
                    $('.institution-note').removeClass('hidden');
                    $('.institution-note-container').html(data.note + '<br><br>');
                }
            });
        });
        $(document).on('click', '.btn-toggle-comment', function(e) {
            var $this = $(this);
            admin.Helper.queryHtmlContent($this, e, function(data) {
                if (data) {
                    data.label = data.label || '';
                    if (data.label === '') {
                        return;
                    }
                    $this.html(data.label);
                }
            });
        });
        $(document).on('click', '#send-form-add-contact', function(e) {
            var $this = $(this);
            admin.Helper.queryHtmlContent($(this), e, function(data) {
                if (data) {
                    data.fieldHtml = data.fieldHtml || '';
                    if (data.fieldHtml === '') {
                        return;
                    }
                    var $modalBody = $this.closest('.modal-body');
                    var $newElem = $(data.fieldHtml);
                    if ($modalBody.hasClass('test-send')) {
                        $newElem.find('input[type=hidden]').val(1);
                    } else {
                        $newElem.find('input[type=hidden]').val(0);
                    }
                    $modalBody.find('.extra-contacts').append($newElem);
                }
            });
        });
        $(document).on('click', '.remove-extra-contact', function(e) {
            if (e) {
                e.preventDefault();
            }
            $(this).closest('div.extra-contact').remove();
        });
        $(document).on('click', '#toggle-test-send', function(e) {
            if (e) {
                e.preventDefault();
            }
            var $testCont = $('.test-send');
            var $realCont = $('.real-send');
            if ($testCont.hasClass('hidden')) {
                $realCont.addClass('hidden');
                $testCont.removeClass('hidden');
                $('#sendform-test').val(1);
            } else {
                $testCont.addClass('hidden');
                $realCont.removeClass('hidden');
                $('#sendform-test').val(0);
            }
        });
        $(document).on('click', '.au-delete-button-static', function(e) {
            if (e) {
                e.preventDefault();
            }
            $(this).closest('.attachment-entry').remove();
        });
        $(document).on('click', '#next', function(e) {
            if (e) {
                e.preventDefault();
            }
            $('#step1').addClass('hidden');
            $('#step2').removeClass('hidden');
            $('#next').addClass('hidden');
            $('#submit').removeClass('hidden');
        });
    }

    return $.extend(admin, {
        Report: {
            mapData: [],
            init: function() {
                initFunctions();
            },
            initMap: function() {
                $.extend(this.mapData, {
                    draggable: !('ontouchend' in document),
                    scrollwheel: false
                });

                var map = new google.maps.Map($('.report__map')[0], this.mapData);

                /* eslint-disable no-unused-vars */
                var marker = new google.maps.Marker({
                    title: this.mapData.title,
                    position: {
                        lat: this.mapData.center.lat,
                        lng: this.mapData.center.lng
                    },
                    map: map
                });
            }
        }
    });
})($, admin || {});
$(document).ready(function() {
    admin.Report.init();
});
