/* global $, site */
/* eslint-disable */
(function($, site) {
    var progress = window.progress || 0;
    var width = window.item_width;
    var gap = window.item_gap;

    'use strict';
    return $.extend(site, {
        Progress: {
            init: function () {
                var percent = Math.max(0, Math.min(100, progress));

                // Két szín közötti átmenetet képez
                function lerpColor(a, b, amount) {
                    var ah = parseInt(a.replace(/#/g, ''), 16),
                        ar = ah >> 16, ag = ah >> 8 & 0xff, ab = ah & 0xff,
                        bh = parseInt(b.replace(/#/g, ''), 16),
                        br = bh >> 16, bg = bh >> 8 & 0xff, bb = bh & 0xff,
                        rr = ar + amount * (br - ar),
                        rg = ag + amount * (bg - ag),
                        rb = ab + amount * (bb - ab);
                    return '#' + ((1 << 24) + (rr << 16) + (rg << 8) + rb | 0).toString(16).slice(1);
                }

                function regenProgressBars() {
                    var startColor = "a11418";
                    var endColor = "fcbb33";
                    var target;

                    $('.progressbar').each(function (key, el) {
                        if ($(el).css("display") === "block" && !target) {
                            target = $(el);
                        }
                    });
                    var barOrigin = $('<div style="position: absolute; height:50px;"></div>');
                    var w = target.width();
                    var barW = width;
                    var fillW = w - barW;
                    var numDiv = Math.floor(fillW / (barW + gap));

                    if (numDiv % 2 == 1) {
                        numDiv--;
                    }

                    var newGap = (fillW - numDiv * barW) / numDiv;
                    var left = 0;

                    target.html('');

                    var addBox = function (i) {
                        var bar = $(barOrigin.clone());
                        var color = lerpColor(startColor, endColor, i / numDiv);
                        bar.width(barW);
                        bar.css('left', Math.round(left) + 'px');
                        bar.css('background-color', color);
                        left += barW + newGap;
                        target.append(bar);
                    };

                    for (var i = 0; i < numDiv; i++) {
                        addBox(i);
                    }

                    addBox(numDiv);

                    setTimeout(function () {
                        $('.progress_container').css('left', ((w - barW) * percent / 100) + 'px');

                        // Determining percent for icon transformation
                        if (percent >= 87) {
                            $('.progress_people').css('transform', 'rotate(' + (-50 * percent / 100) + 'deg)');
                        }
                    }, 500);

                }

                jQuery(window).resize(function () {
                    regenProgressBars();
                });

                setInterval(function () {
                    regenProgressBars();
                }, 500);

                regenProgressBars();

                $('.donationbox2').on('click', function () {
                    $('.button-donate-other').trigger("click");
                    $('#other-amount-input').focus();
                });
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Progress.init();
});
