/* global $, site, google */
(function($, site) {
    'use strict';
    var map;
    var markers = [];
    var poi = '<?xml version="1.0" encoding="UTF-8" standalone="no"?> <svg width="32px" height="32px" viewBox="0 0 72 72" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <title>map-poi</title> <desc>Created with Sketch.</desc> <defs> <path d="M25.5,50.6 C39.5832611,50.6 51,39.2728042 51,25.3 C51,11.3271958 39.5832611,0 25.5,0 C11.4167389,0 0,11.3271958 0,25.3 C0,39.2728042 11.4167389,50.6 25.5,50.6 Z M25.5,33.7333333 C30.1944204,33.7333333 34,29.9576014 34,25.3 C34,20.6423986 30.1944204,16.8666667 25.5,16.8666667 C20.8055796,16.8666667 17,20.6423986 17,25.3 C17,29.9576014 20.8055796,33.7333333 25.5,33.7333333 Z" id="path-1"></path> <mask id="mask-2" maskContentUnits="userSpaceOnUse" maskUnits="objectBoundingBox" x="-3" y="-3" width="57" height="56.6"> <rect x="-3" y="-3" width="57" height="56.6" fill="white"></rect> <use xlink:href="#path-1" fill="black"></use> </mask> </defs> <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Jarokelo_icons_poi"> <g id="POI" transform="translate(11.000000, 3.000000)"> <circle id="Oval" fill="#FFFFFF" cx="25.5" cy="25.5" r="8.5"></circle> <g id="Combined-Shape"> <use fill="#000000" fill-rule="evenodd" xlink:href="#path-1"></use> <use stroke-opacity="0.2" stroke="#000000" mask="url(#mask-2)" stroke-width="6" xlink:href="#path-1"></use> </g> <path d="M35.8714776,48.4197347 C44.7859045,44.4772896 51,35.608643 51,25.3 C51,11.3271958 39.5832611,0 25.5,0 C11.4167389,0 0,11.3271958 0,25.3 C0,35.608643 6.21409547,44.4772896 15.1285224,48.4197347 L25.5,69 L35.8714776,48.4197347 Z M25.5,33.7333333 C30.1944204,33.7333333 34,29.9576014 34,25.3 C34,20.6423986 30.1944204,16.8666667 25.5,16.8666667 C20.8055796,16.8666667 17,20.6423986 17,25.3 C17,29.9576014 20.8055796,33.7333333 25.5,33.7333333 Z" id="Combined-Shape" fill="{{ color }}"></path> </g> </g> </g> </svg>';
    var panToMarker = function(markerId) {
        map.panTo(markers[markerId].marker.getPosition());
        map.setZoom(16);
    };
    var initCardClicks = function() {
        $('.reportsonmap').find('.card').click(function(e) {
            var $origin = $(e.target || e.srcElement || e.originalTarget);
            if (!$origin.attr('href')) {
                e.preventDefault();
                panToMarker($(this).data('id'));
            }
        });
    };
    var initMarkers = function() {
        $('.reportsonmap').find('.card').each(function() {
            var $this = $(this);
            var $id = $this.data('id');
            markers[$id] = {
                id: $id,
                title: $this.find('h3').text(),
                href: $this.find('h3').find('a').attr('href'),
                lat: parseFloat($this.attr('data-lat')),
                lng: parseFloat($this.attr('data-lng')),
                marker: ''
            };

            var marker = {
                title: markers[$id].title,
                position: {
                    lat: markers[$id].lat,
                    lng: markers[$id].lng
                },
                map: map
            };

            var color = $this.find('.badge').css('background-color');

            if (!site.Helper.isIE()) {
                marker.icon = {
                    url: 'data:image/svg+xml;charset=UTF-8;base64,' + btoa(poi.replace('{{ color }}', color)),
                    scale: 1,
                    zIndex: 1
                };
            } else {
                marker.icon = {
                    path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z M -2,-30 a 2,2 0 1,1 4,0 2,2 0 1,1 -4,0',
                    fillColor: color,
                    fillOpacity: 1,
                    strokeColor: '#333',
                    strokeWeight: 1,
                    scale: 1
                };
            }

            markers[$id].marker = new google.maps.Marker(marker);
            markers[$id].marker.addListener('click', function() {
                document.location.href = markers[$id].href;
            });
        });
    };

    function fitBounds() {
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < markers.length; i++) {
            if (markers[i]) {
                bounds.extend(markers[i].marker.getPosition());
            }
        }

        map.fitBounds(bounds);
    }

    function setListScrollable() {
        var $list = $('#map-report-list');
        if ($list.length === 0 || $(window).width() < 1200) {
            return;
        }

        $list.css('overflow-y', 'scroll');
        $list.height($(window).height() - 300);
    }

    return $.extend(site, {
        ReportsOnMap: {
            mapData: [],
            initMap: function() {
                $.extend(this.mapData, {
                    draggable: !('ontouchend' in document),
                    scrollwheel: true
                });
                map = new google.maps.Map($('.reportsonmap__map')[0], this.mapData);
                initCardClicks();
                initMarkers();
                fitBounds();
                setListScrollable();
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
});
