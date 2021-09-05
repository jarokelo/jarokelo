/* eslint-disable no-unused-vars */
/* global $, site, google, mapboxgl */
var ReportsOnMap = (function() {
    'use strict';
    var map;
    var markers = [];
    var poi = '<?xml version="1.0" encoding="UTF-8" standalone="no"?> <svg width="32px" height="32px" viewBox="0 0 72 72" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"> <title>map-poi</title> <desc>Created with Sketch.</desc> <defs> <path d="M25.5,50.6 C39.5832611,50.6 51,39.2728042 51,25.3 C51,11.3271958 39.5832611,0 25.5,0 C11.4167389,0 0,11.3271958 0,25.3 C0,39.2728042 11.4167389,50.6 25.5,50.6 Z M25.5,33.7333333 C30.1944204,33.7333333 34,29.9576014 34,25.3 C34,20.6423986 30.1944204,16.8666667 25.5,16.8666667 C20.8055796,16.8666667 17,20.6423986 17,25.3 C17,29.9576014 20.8055796,33.7333333 25.5,33.7333333 Z" id="path-1"></path> <mask id="mask-2" maskContentUnits="userSpaceOnUse" maskUnits="objectBoundingBox" x="-3" y="-3" width="57" height="56.6"> <rect x="-3" y="-3" width="57" height="56.6" fill="white"></rect> <use xlink:href="#path-1" fill="black"></use> </mask> </defs> <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Jarokelo_icons_poi"> <g id="POI" transform="translate(11.000000, 3.000000)"> <circle id="Oval" fill="#FFFFFF" cx="25.5" cy="25.5" r="8.5"></circle> <g id="Combined-Shape"> <use fill="#000000" fill-rule="evenodd" xlink:href="#path-1"></use> <use stroke-opacity="0.2" stroke="#000000" mask="url(#mask-2)" stroke-width="6" xlink:href="#path-1"></use> </g> <path d="M35.8714776,48.4197347 C44.7859045,44.4772896 51,35.608643 51,25.3 C51,11.3271958 39.5832611,0 25.5,0 C11.4167389,0 0,11.3271958 0,25.3 C0,35.608643 6.21409547,44.4772896 15.1285224,48.4197347 L25.5,69 L35.8714776,48.4197347 Z M25.5,33.7333333 C30.1944204,33.7333333 34,29.9576014 34,25.3 C34,20.6423986 30.1944204,16.8666667 25.5,16.8666667 C20.8055796,16.8666667 17,20.6423986 17,25.3 C17,29.9576014 20.8055796,33.7333333 25.5,33.7333333 Z" id="Combined-Shape" fill="{{ color }}"></path> </g> </g> </g> </svg>';

    // initializing mapbox token
    mapboxgl.accessToken = window.mapboxToken;

    var panToMarker = function(markerId) {
        map.setZoom(16);
        map.panTo(markers[markerId].marker.getLngLat());
    };

    var initAutoComplete = function() {
        $('.map-search-form-location').each(function(index, input) {
            /* eslint-disable */
            new google.maps.places.Autocomplete(
                input,
                {
                    types: ['address'],
                    componentRestrictions: {country: 'hu'}
                }
            );
        });
    };

    function submit() {
        $('#report-search-form').submit();
    }

    var initShowMeOnMap = function() {
        var $input = $('#map-search-form-location-mobile');

        $('.button-show_me-mobile').on('click', function() {
            if (!navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                var lng = position.coords.longitude;
                var lat = position.coords.latitude;
                var geocoder = new google.maps.Geocoder();

                geocoder.geocode(
                    {
                        'location': {
                            lng: lng,
                            lat: lat
                        }
                    },
                    function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            if (results[0] && results[0].formatted_address) {
                                $input.val(results[0].formatted_address);
                                submit();
                            }
                        }
                    }
                );
            });
        });

        $input.on('change', function() {
            submit();
        });

        $('.button-show_me-mobile--clear').click(function(e) {
            e.preventDefault();
            $input.val('');
            submit();
        });
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

    var fitBounds = function() {
        var bounds = new mapboxgl.LngLatBounds();

        markers.forEach(function(el) {
            bounds.extend(el.marker.getLngLat());
        });

        // reminder: property padding resets zoom value, however without this it looks awkward
        map.fitBounds(bounds, {
            padding: 100
        });
    };

    var initMarkers = function() {
        $('.reportsonmap').find('.card').each(function() {
            var $this = $(this);
            var color = $this.find('.badge').css('background-color');
            var $id = $this.data('id');

            markers[$id] = {};

            if (site.Helper.isIE()) {
                // TODO [jiren][2019-04-18] this is wrong in this way, create colorable png icon of transparent google pin or solve it somehow
                mark.src = 'https://maps.gstatic.com/mapfiles/api-3/images/autocomplete-icons_hdpi.png';
                mark.style.backgroundColor = color;
                mark.style.width = '22px';
                mark.style.height = '42px';
            } else {
                var mark = document.createElement('div');
                var svgElem = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var useElem = document.createElementNS('http://www.w3.org/2000/svg', 'use');
                useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', window.STATIC_ASSETS_PATH + '/images/icons.svg#icon-magnify');

                mark.appendChild(svgElem);
                svgElem.appendChild(useElem);

                svgElem.setAttribute('fill', color);

                svgElem.style.width = '32px';
                svgElem.style.height = '32px';
                svgElem.setAttribute('class','marker');
            }

            mark.title = $this.find('h3').text();
            svgElem.dataset.href = $this.find('h3').find('a').attr('href');

            // make a marker for each feature and add to the map
            markers[$id].marker = new mapboxgl.Marker(mark, {})
                .setLngLat([
                    parseFloat($this.attr('data-lng')),
                    parseFloat($this.attr('data-lat'))
                ]);

            markers[$id].marker.addTo(map);
        });
    };

    function fireFullScreenChange() {
        [
            'fullscreenchange',
            'mozfullscreenchange',
            'webkitfullscreenchange',
            'msfullscreenchange'
        ].forEach(function(value) {
            document.addEventListener(value, function() {
                map.resize();
            });
        });
    }

    function setListScrollable() {
        var $list = $('#map-report-list');

        if ($list.length === 0 || $(window).width() < 1200) {
            return;
        }

        $list.css('overflow-y', 'scroll');
        $list.height($(window).height() - 300);
    }

    function initMap() {
        if (window.reportsOnMapInitData == null) {
            setTimeout(initMap, 500);
            return;
        }

        initAutoComplete();
        initShowMeOnMap();

        // Preventing errors when #map doesn't exist
        if (!$('#map').html()) {
            return;
        }

        // keep resizing before map init, otherwise boundles will slip apart
        setListScrollable();

        fireFullScreenChange();

        var center = window.reportsOnMapInitData.center;

        map = new mapboxgl.Map({
            container: 'map', // HTML container id
            style: 'mapbox://styles/mapbox/streets-v11', // style URL
            center: [center.lng, center.lat], // starting position as [lng, lat]
            zoom: 17
        });

        map.addControl(new mapboxgl.FullscreenControl());

        initCardClicks();
        initMarkers();
        fitBounds();

        $('.marker').on('click', function() {
            document.location.href = $(this).data('href');
        });

        map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');
    }

    return {
        initMap: initMap
    };
})();
