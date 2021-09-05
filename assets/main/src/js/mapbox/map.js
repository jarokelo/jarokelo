/* eslint-disable */
/* global $, mapboxgl, google, Map */
var Mapbox = (function() {  // eslint-disable-line no-unused-vars
    'use strict';

    // initializing mapbox token
    mapboxgl.accessToken = window.mapboxToken;

    var defaultZoom = window.mapInitData.zoom;
    var mapSelectors = window.mapInitData.selectors;
    var defaultCenterLng = window.mapInitData.center.lng;
    var defaultCenterLat = window.mapInitData.center.lat;
    var geocoder;
    var marker;
    var autocomplete;
    var map;
    var triggerResize = true;
    var isReportEndPage = window.mapInitData.isReportEndPage || false;

    /**
     * @param {string} inputName
     * @param {string|number} value
     * @param trigger
     */
    function changeInputValue(inputName, value, trigger) {
        if (typeof mapSelectors[inputName] === 'string') {
            var $input = $(mapSelectors[inputName]);
            $input.val(value);

            if (trigger) {
                $input.trigger('input');
            }
        }
    }

    /**
     * @param {object} data
     * @param {object} lngLat
     */
    function fillInAddress(data, lngLat) {
        var streetName = 'Unnamed Road';
        var streetNumber = '';
        var postalCode = 0;

        for (var i = 0; i < data.address_components.length; i++) {
            // zeroth index of types is enough to work with
            switch (data.address_components[i].types[0]) {
                case 'street_number':
                    streetNumber = data.address_components[i].long_name;
                    break;
                case 'postal_code':
                    postalCode = data.address_components[i].long_name;
                    break;
                case 'route':
                    streetName = data.address_components[i].long_name;
                    break;
                default: // nothing special here
            }
        }

        changeInputValue('address', streetName + ' ' + streetNumber);
        changeInputValue('street_name', streetName);
        changeInputValue('post_code', postalCode, true);
        changeInputValue('user_location', data.formatted_address, true);
        changeInputValue('latitude', lngLat.lat);
        changeInputValue('longitude', lngLat.lng);
    }

    /**
     * @param {object} lngLat
     */
    var placeMarker = function(lngLat) {
        marker
            .setLngLat([lngLat.lng, lngLat.lat])
            .addTo(map);
    };

    /**
     * Regardless of geo coding result, working with original coordinates, so
     * Google won't break any coordinate, while fetching the nearest found one
     *
     * @param {object} lngLat
     */
    function geoCode(lngLat) {
        geocoder = new google.maps.Geocoder();

        geocoder.geocode(
            {'location': lngLat},
            function(results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        // don't use marker here to avoid inconsistent pin bouncing
                        map.jumpTo({
                            center: [
                                lngLat.lng,
                                lngLat.lat
                            ],
                            zoom: map.getZoom() || defaultZoom
                        });

                        $('#reportform-user_location').val(results[0].formatted_address);
                        fillInAddress(results[0], lngLat);
                    }
                }
            }
        );
    }

    /**
     * @param {callback} callback
     */
    function lateCall(callback) {
        setTimeout(function() {
            callback();
        }, 0);
    }

    /**
     * @param {object} lngLat
     */
    var geoCodePlaceMarker = function(lngLat) {
        geoCode(lngLat);
        placeMarker(lngLat);
    };

    function resizeMap() {
        lateCall(function() {
            map.resize();
        });
    }

    function fireFullScreenChange() {
        [
            'fullscreenchange',
            'mozfullscreenchange',
            'webkitfullscreenchange',
            'msfullscreenchange'
        ].forEach(function(value) {
            document.addEventListener(value, function() {
                resizeMap();
            });
        });
    }

    /**
     * @returns {boolean}
     */
    function isLocationHandlerSet() {
        return !!window.mapInitData.locationChangeHandler;
    }

    /**
     * @var {bool} draggable
     */
    function initMarker(draggable) {
        var svgElem = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        var useElem = document.createElementNS('http://www.w3.org/2000/svg', 'use');
        useElem.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href',  window.STATIC_ASSETS_PATH + '/images/icons.svg#icon-magnify');

        svgElem.appendChild(useElem);
        svgElem.style.height = '43px';
        svgElem.setAttribute('fill', '#104A9C');

        marker = new mapboxgl.Marker(svgElem, {
            draggable: draggable,
        });

        // avoid useless geocode request upon static maps
        if (isLocationHandlerSet()) {
            marker.on('dragend', function() {
                geoCode(marker.getLngLat());
            });
        }
    }

    function handleMobileScreenActions() {
        if ($('[step=2]').css('display') === 'none') {
            return;
        }

        fireFullScreenChange();

        var selectorDisplay = $('.step--final').css('display');

        // last step - summary
        if (selectorDisplay === 'block') {
            $('.mapboxgl-ctrl-group').hide();
        } else {
            // editable map
            $('.mapboxgl-ctrl-group').show();
        }

        // reinitializing marker to display draggable or not depends on current step (mobile screen)
        var coordinates = marker.getLngLat();
        marker.remove();

        initMarker(selectorDisplay !== 'block');
        placeMarker(coordinates);
    }

    $('.button--large').on('click', function() {
        if ($('[step=1]:visible').length) {
            resizeMap();
        }

        lateCall(handleMobileScreenActions);
    });

    $('.steps__icon').on('click', function() {
        // upon user clicks step icon, where map is displayed, a resize should be triggered
        // but once at all
        lateCall(function() {
            if ($('[step=2]:visible').length && triggerResize) {
                triggerResize = false;
                resizeMap();
            }

            handleMobileScreenActions();
        });
    });

    function validateReportCreateForm() {
        var $reportForm = $('#report-create-form');

        if ($reportForm.length > 0) {
            $reportForm.yiiActiveForm('validate');
        }
    }

    /**
     * @param browserHasGeolocation
     * @param pos
     */
    function handleLocationError(browserHasGeolocation, pos) {
        var infoWindow = new google.maps.InfoWindow({map: map});
        infoWindow.setPosition(pos);
        infoWindow.setContent(
            browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.'
        );
    }

    var initAutoComplete = function() {
        if (typeof mapSelectors.user_location !== 'string') {
            return;
        }

        autocomplete = new google.maps.places.Autocomplete(
            $(mapSelectors.user_location)[0],
            {
                types: ['(cities)'],
                componentRestrictions: {country: 'hu'}
            }
        );

        /**
         * Using the search input
         */
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();

            if (!place.geometry) {
                // show_on_map click callback's the responsible for this
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry && place.geometry.location) {
                var lng = place.geometry.location.lng();
                var lat = place.geometry.location.lat();

                // cannot leave geocoding since autocomplete response is insufficient
                geoCodePlaceMarker({
                    'lng': lng,
                    'lat': lat
                });
            }
        });

        $(mapSelectors.show_on_map).on('click', function() {
            geocoder.geocode({'address': $(mapSelectors.user_location).val()}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    var lng = results[0].geometry.location.lng();
                    var lat = results[0].geometry.location.lat();

                    geoCodePlaceMarker({
                        'lng': lng,
                        'lat': lat
                    });
                    validateReportCreateForm();
                }
            });
        });

        $(mapSelectors.show_me_on_map).on('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lng = position.coords.longitude;
                    var lat = position.coords.latitude;

                    geoCodePlaceMarker({
                        'lng': lng,
                        'lat': lat
                    });
                });
            } else {
                handleLocationError(false, map.getCenter());
            }
        });

        // block form submit
        $(mapSelectors.user_location).on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                $(mapSelectors.show_on_map).trigger('click');
            }
        });
    };

    var initMap = function() {
        initAutoComplete();

        map = new mapboxgl.Map({
            container: 'map', // HTML container id
            style: 'mapbox://styles/mapbox/streets-v11', // style URL
            center: [defaultCenterLng, defaultCenterLat], // starting position as [lng, lat]
            zoom: defaultZoom
        });

        // initial zoom value
        changeInputValue('zoom', defaultZoom);

        map.on('zoom', function() {
            changeInputValue('zoom', Math.round(map.getZoom()));
        });

        initMarker(true);

        if (isReportEndPage) {
            // initial call to set default location on map
            placeMarker({
                'lng': defaultCenterLng,
                'lat': defaultCenterLat
            });
        } else {
            geoCodePlaceMarker({
                'lng': defaultCenterLng,
                'lat': defaultCenterLat
            });
        }

        // initializes on dynamic maps
        if (isLocationHandlerSet()) {
            map.on('click', function(e) {
                geoCodePlaceMarker({
                    'lng': e.lngLat.lng,
                    'lat': e.lngLat.lat
                });
            });
        }

        map.addControl(new mapboxgl.FullscreenControl());
        // add zoom and rotation controls to the map.
        map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');

        $(document).trigger('mapReady', map);
        window._mapReady = map;
    };

    return {
        initMap: initMap
    };
})();
