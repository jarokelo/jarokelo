var position = {
    init: function() {
        this.cacheDOM();
        this.bindEvents();
    },
    cacheDOM: function() {

    },
    bindEvents: function() {

    },
    round: function(number) {
        return Math.round(number * 10000000) / 10000000;
    },
    location: {
        init: function() {
            if (navigator && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position.location.successCallback, position.location.errorCallback);
            } else {
                console.log('Geolocation is not supported');
            }
        },
        successCallback: function(pos) {
            var url = window.location.href;
            var params = position.round(pos.coords.latitude) + '&long=' + position.round(pos.coords.longitude);
            if (url.indexOf('?') > -1) {
                url += '&lat=' + params;
            } else {
                url += '?lat=' + params;
            }
            if (window.location.href.indexOf('&long=') === -1) {
                window.location.href = url;
            }
        },
        errorCallback: function() {
        }
    }
};
