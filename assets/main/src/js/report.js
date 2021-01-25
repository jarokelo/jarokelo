/* global $, site, autosize, google, baseUrl */
(function($, site) {
    'use strict';

    var $videoContainer;
    var $Source;
    var newInput;
    var videoItem;

    function createEmbed($this) {
        var videoUrl = $this.val();
        var type;
        var hash;
        var playerUrl;
        var width;
        var height;

        if ($videoContainer.find('.video-item').length >= 10) {
            return false;
        }

        var $videoItem = $this.closest('.video-item');
        if (!$videoItem.find('.video-preloader').length) {
            $videoItem.find('.add-video').after('<div class="video-preloader"></div>');
        }

        if (videoUrl === '') {
            $videoItem.find('.video-preloader').remove();
        }

        $.ajax({
            url: baseUrl + '/report/parse-video-url',
            type: 'post',
            dataType: 'json',
            data: {
                url: videoUrl.replace('http:', 'https:')
            },
            success: function(resp) {
                if (resp && resp.success) {
                    type = resp.videoData.type;
                    hash = resp.videoData.hash;
                    playerUrl = resp.videoData.videoUrl;
                    if (!(/https:\/\//i.test(playerUrl))) {
                        playerUrl = playerUrl.replace(/^http:\/\//i, 'https://');
                    }
                    width = resp.videoData.width;
                    height = resp.videoData.height;
                    var exists = $('#cont-' + type + '-' + hash).length !== 0;
                    if (exists) {
                        $videoItem.find('.add-video').val($this.data('val') || '');
                        $videoItem.find('.video-preloader').remove();
                        return false;
                    }
                    $this.data('val', videoUrl);
                    var $appendTo = $('');
                    if ($videoItem.find('.video-embed-container').length) {
                        $appendTo = $this.closest('.video-item').find('.video-embed-container');
                        $videoItem.find('.video-preloader').remove();
                    } else {
                        $appendTo = $videoItem.find('.video-preloader');
                        $videoContainer.append($(videoItem));
                    }
                    var $videoBox = $('<div id="cont-' + type + '-' + hash + '" class="video-embed-container"></div>');
                    var $iframe = $('<div class="video-wrapper"><iframe class="support-box_video" id="player-' + hash + '" width="' + width + '" height="' + height + '" src="' + playerUrl + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>');
                    $iframe.appendTo($videoBox);
                    var $remove = $('<button type="button" class="video-embed-remove"></button>').appendTo($videoBox);

                    $appendTo.replaceWith($videoBox);

                    $remove.on('click', function(e) {
                        e.preventDefault();
                        $videoItem.remove();
                        var $lastItem = $videoContainer.find('.video-item');
                        if ($lastItem.length === 1) {
                            $lastItem.find('.add-video').trigger('input');
                        }
                    });
                }
                return true;
            },
            error: function() {
            }
        });
        return true;
    }

    return $.extend(site, {
        Report: {
            mapData: [],
            steps: function(event, map) {
                var $step = $('[step]');
                var $videoContainer = $('.video-container');

                function active(target) {
                    $('.steps__icon').removeClass('steps__icon--active');
                    $('[step="' + target + '"]').css({ display: 'block' });
                    $('.steps [show-step="' + target + '"]').addClass('steps__icon--active');
                }

                active(1);

                $(document).on('click', '[show-step]', function() {
                    var $this = $(this);
                    var step = $this.attr('show-step');
                    $step.hide();

                    if (step === 'final') {
                        $step.addClass('step--final');
                        active(step);
                        $step.show();

                        if (!$videoContainer.children().length) {
                            $videoContainer.closest('.row').hide();
                        } else {
                            $videoContainer.closest('.row').show();
                        }
                    } else {
                        $videoContainer.closest('.row').show();
                        $step.removeClass('step--final');
                        active(step);
                    }

                    window.scrollTo(0, 0);

                    // force refresh google map
                    google.maps.event.trigger(map, 'resize');
                });

                $(document).on('click', '.step__helper', function() {
                    var $this = $(this);

                    $('<div>' + $this.closest('.step').find('.step__help:eq(0)').html() + '</div>')
                        .modal({
                            escapeClose: true,
                            clickClose: true,
                            showClose: true,
                            fadeDuration: 300,
                            closeText: ''
                        });
                });
            },
            init: function() {
                var timeout;
                $videoContainer = $('.video-container');
                $Source = $('#template-source');

                if ($('#report-create-form').length) {
                    newInput = $(site.Helper.purifyStringDOM($Source.data('input-template'))).find('input').clone().addClass('add-video').attr('type', 'text').attr('id', null)[0].outerHTML;
                    videoItem = '<div class="video-item"><label for="" class="label label--default">Link a videóra</label>' + newInput + '</div>';
                }

                // textarea autogrow
                autosize($('.autogrow-textarea'));

                // lightbox for pictures and videos
                $('.gallery, .comment__pictures').lightGallery({
                    animateThumb: false,
                    showThumbByDefault: false
                });

                // lightbox with owl carousel
                $('.owl-item.unwrap').unwrap();
                $('.owl-item').width($('.owl-stage-outer').width());
                $('.owl-stage, .owl-thumbs').lightGallery({
                    animateThumb: false,
                    showThumbByDefault: false,
                    exThumbImage: 'data-exthumbimage'
                });
                $('.owl-nav').children().height($('.owl-stage').height());
                $(window).on('resize init', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function() {
                        $('.owl-nav').children().height($('.owl-stage').height());
                        $('.report__thumbs__item').removeClass('active').first().addClass('active');
                    }, 200);
                }).trigger('init');

                if ($('.owl-stage .owl-item').length < 2) {
                    $('.owl-thumbs, .owl-nav').hide();
                }

                var init = false;
                $(document).on('click', '[init-video-upload]', function(e) {
                    e.preventDefault();
                    var $vl = $('#video-link');
                    var $vlp = $('#video-link-panel');
                    if (!init) {
                        init = true;
                        $vl.show();
                        if (window.matchMedia('(min-width: 1080px)').matches) {
                            $vlp.show();
                        }
                        $videoContainer.append($(videoItem));
                    }
                });
                var timeOut = null;
                $(document).on('input', '.add-video[type=text]', function() {
                    var $this = $(this);
                    clearTimeout(timeOut);
                    timeOut = setTimeout(function() {
                        createEmbed($this);
                    }, 300);
                });
                $(document).on('pjax:start', '#report-activity-list', function() {
                    if ($('#report-comment-form')[0]) {
                        $('#report-comment-form')[0].reset();
                    }
                });
                $(document).on('change', '#city-dropdown', function() {
                    var locField = $('#reportform-user_location');
                    locField.val($('option:selected', this).text() + ', ');
                    locField.focus();
                    $('#report-create-form button.show-on-map').click();
                    setTimeout(function() {
                        locField.val($('option:selected', '#city-dropdown').text() + ', ');
                        $('.pac-container').hide();
                        locField.focus();
                    }, 300);
                });
                $(document).on('keyup keypress change', '#reportform-user_location', function() {
                    var city = $(this).val().split(',')[0];
                    var select = $('#city-dropdown option');

                    $('#city-dropdown option').prop('selected', false);

                    $.each(select, function(k, v) {
                        var option = $(v);

                        if (option.text() === city) {
                            $('#city-dropdown option[value="' + option.val() + '"]').prop('selected', 'selected');
                        }
                    });
                });
                $('#reportform-user_location').val($('option:selected', $('#city-dropdown')).text() + ', ');
                $(document).on('change', '#report-search-form select, #front-search-form select', function() {
                    $(this).closest('form').submit();
                });

                $(document).on('mapReady', this.steps);

                $(document).on('change', '#report-map-search-form-city', function() {
                    var locField = $('#map-search-form-location');
                    locField.val($('option:selected', this).text() + ', ');
                });
                $(document).on('keyup keypress change', '#map-search-form-location', function() {
                    var city = $(this).val().split(',')[0];
                    var select = $('#report-map-search-form-city option');

                    $('#report-map-search-form-city option').prop('selected', false);

                    $.each(select, function(k, v) {
                        var option = $(v);

                        if (option.text() === city) {
                            $('#report-map-search-form-city option[value="' + option.val() + '"]').prop('selected', 'selected');
                        }
                    });
                });

                if (typeof $('#map-search-form-location').val() !== 'undefined') {
                    if ($('#map-search-form-location').val().length === 0) {
                        $('#map-search-form-location').val($('option:selected', $('#report-map-search-form-city')).text() + ', ');
                    }
                }
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    $(function() {
        site.Report.init();
    });

    if (typeof google !== 'undefined') {
        google.maps.event.addDomListener(window, 'load', function() {
            var input = document.getElementById('map-search-form-location');
            return new google.maps.places.Autocomplete(input);
        });
    }
});

