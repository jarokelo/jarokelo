/* global $, google, Map */
var JarokeloMap = (function() {
    'use strict';
    var defaultZoom;
    var defaultCenter;
    var zoomed = false;
    var mapSelectors = {
        map: null,
        zoom: null,
        show_on_map: null,
        show_me_on_map: null,
        latitude: null,
        longitude: null,
        user_location: null,
        post_code: null,
        street_name: null
    };
    var map;
    var geocoder;
    var marker;
    var autocomplete;
    var mapInitInterval = null;
    var placeMarker = function(location) {
        marker.setPosition(location);
        map.panTo(location);
        if (zoomed !== true) {
            map.setZoom(16);
        }
    };
    var init = function(data) {
        mapSelectors = data.selectors;
        defaultZoom = data.zoom;
        defaultCenter = data.center;
    };

    function validateReportCreateForm() {
        var $reportForm = $('#report-create-form');
        if ($reportForm.length > 0) {
            $reportForm.yiiActiveForm('validate');
        }
    }

    function changeInputValue(inputName, value, trigger) {
        if (typeof mapSelectors[inputName] === 'string') {
            var $input = $(mapSelectors[inputName]);
            $input.val(value);
            if (trigger) {
                $input.trigger('input');
            }
        }
    }

    function Address() {
        this.streetName = '';
        this.streetNumber = '';
        this.postCode = '';
        this.formattedAddress = '';
        this.latitude = '';
        this.longitude = '';
        this.significantName = '';

        this.isAllFilled = function() {
            return this.streetName !== '' && this.streetNumber !== '' &&
                this.postCode !== '' && this.formattedAddress !== '' &&
                this.latitude !== '' && this.longitude !== '';
        };

        this.isAllButStreetNumberFilled = function() {
            return this.streetName !== '' &&
                this.postCode !== '' && this.formattedAddress !== '' &&
                this.latitude !== '' && this.longitude !== '';
        };

        this.removeCountryNameFromFormattedAddress = function() {
            var COUNTRY_NAME_TO_REMOVE = 'Magyarország';
            if (!this.streetName.includes(COUNTRY_NAME_TO_REMOVE)) {
                this.formattedAddress = this.formattedAddress
                    .replace(COUNTRY_NAME_TO_REMOVE, '')
                    .replace('  ', ' ');
            }
        };
    }

    function parseAddressFromPlace(place) {
        var address = new Address();
        address.formattedAddress = place.formatted_address;
        for (var i = 0; i < place.address_components.length && !address.isAllFilled(); ++i) {
            if (address.significantName === '') {
                address.significantName = place.address_components[i].long_name;
            }
            if (place.address_components[i].types[0] === 'route') {
                address.streetName = place.address_components[i].long_name;
            } else if (place.address_components[i].types[0] === 'street_number') {
                address.streetNumber = place.address_components[i].long_name;
            } else if (place.address_components[i].types[0] === 'postal_code') {
                address.postCode = place.address_components[i].long_name;
            }
            address.latitude = place.geometry.location.lat();
            address.longitude = place.geometry.location.lng();
        }
        return address;
    }

    function parseAddressFromPlaces(places) {
        var address = new Address();
        var addressWithoutStreetNumber = new Address();
        if (places.length > 0) {
            var firstAddress = new Address();
            for (var i = 0; i < places.length && !address.isAllFilled(); ++i) {
                var parsedAddress = parseAddressFromPlace(places[i]);
                if (i === 0) { firstAddress = parsedAddress; }
                if (parsedAddress.isAllFilled()) {
                    address = parsedAddress;
                } else if (parsedAddress.isAllButStreetNumberFilled()) {
                    addressWithoutStreetNumber = parsedAddress;
                }
            }
            if (!address.isAllFilled() && addressWithoutStreetNumber.isAllButStreetNumberFilled()) {
                address = addressWithoutStreetNumber;
            }
            if (firstAddress.significantName !== '' && !address.formattedAddress.includes(firstAddress.significantName)) {
                address.formattedAddress += ' (' + firstAddress.significantName + ')';
            }
        }
        address.removeCountryNameFromFormattedAddress();
        return address;
    }

    function fillInAddress(places, updateLatlng) {
        var address = parseAddressFromPlaces(places);
        changeInputValue('address', address.streetName + ' ' + address.streetNumber);
        changeInputValue('street_name', address.streetName);
        changeInputValue('post_code', address.postCode, true);
        changeInputValue('user_location', address.formattedAddress, true);
        if (typeof updateLatlng === 'undefined') {
            changeInputValue('latitude', address.latitude);
            changeInputValue('longitude', address.longitude);
        }
    }

    var geocode = function(coordinates, updateLatlng) {
        geocoder.geocode({'location': coordinates}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                placeMarker(coordinates);
                fillInAddress(results, updateLatlng);
            }
        });
    };

    function initAutoComplete() {
        if (typeof mapSelectors.user_location !== 'string') {
            return;
        }
        autocomplete = new google.maps.places.Autocomplete($(mapSelectors.user_location)[0], {
            types: ['address'],
            componentRestrictions: {country: 'hu'}
        });
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (typeof place.geometry === 'undefined') {
                return;
            }
            geocode(place.geometry.location);
        });
    }

    function handleLocationError(browserHasGeolocation, pos) {
        var infoWindow = new google.maps.InfoWindow({map: map});
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Error: The Geolocation service failed.' :
            'Error: Your browser doesn\'t support geolocation.');
    }

    var bind = function() {
        $(mapSelectors.show_on_map).on('click', function() {
            geocoder.geocode({'address': $(mapSelectors.user_location).val()}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK && results[0]) {
                    geocode(results[0].geometry.location);
                    validateReportCreateForm();
                }
            });
        });
        $(mapSelectors.show_me_on_map).on('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    geocode(pos);
                });
            } else {
                handleLocationError(false, map.getCenter());
            }
        });
        $(mapSelectors.user_location).on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                $(mapSelectors.show_on_map).trigger('click');
                return;
            }
        });
    };

    function locationChangeHandler(e) {
        changeInputValue('latitude', e.latLng.lat());
        changeInputValue('longitude', e.latLng.lng());
        geocode(e.latLng, false);
    }

    var initMap = function() {
        // noinspection JSUnresolvedVariable
        init(window.mapInitData);
        if (typeof mapSelectors.map !== 'string') {
            return;
        }
        initAutoComplete();
        map = new google.maps.Map($(mapSelectors.map)[0], {
            center: defaultCenter,
            zoom: defaultZoom,
            mapTypeControl: false,
            streetViewControl: true,
            fullscreenControl: true,
            scrollwheel: true,
            draggable: !('ontouchend' in document)
        });

        map.addListener('zoom_changed', function() {
            changeInputValue('zoom', map.getZoom());
            zoomed = true;
        });
        geocoder = new google.maps.Geocoder();
        marker = new google.maps.Marker({
            map: map,
            draggable: true
        });
        if (window.mapInitData.locationChangeHandler === true) {
            map.addListener('click', locationChangeHandler);
            marker.addListener('dragend', locationChangeHandler);
        }
        bind();

        $(document).trigger('mapReady', map);
    };
    var showPlace = function() {
        initMap();
        geocode(defaultCenter, false);
    };
    var geolocate = function() {
        if (!navigator.geolocation) {
            return;
        }
        navigator.geolocation.getCurrentPosition(function(position) {
            var circle = new google.maps.Circle({
                center: {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                },
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    };

    return {
        initMap: initMap,
        geolocate: geolocate,
        showPlace: showPlace,
        placeMarker: placeMarker,
        geocode: geocode,
        mapInitInterval: mapInitInterval
    };
})();
$(document).ready(function() {
    JarokeloMap.mapInitInterval = setInterval(function() {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.Map === 'undefined') {
            return;
        }
        clearInterval(JarokeloMap.mapInitInterval);
        JarokeloMap.mapInitInterval = null;
        JarokeloMap.showPlace();
    }, 250);
});
