/* eslint-disable */

var map = false;
var geoJson = false;
var lng = false;
var lat = false;
var zoom = false;
var parsedJson = JSON.parse(GEOJSON);

/**
 *
 * handle zipped kml file type
 * @param {object} file
 */
function handleKmz(file) {
    JSZip.loadAsync(file).then(function (zip) {
        Object.keys(zip.files).forEach(function (filename) {
            zip.files[filename].async('string').then(function (fileData) {
                handleKmlContent(fileData);
                refresh();
            });
        });
    });
}

/**
 * handle KML file content
 * @param {string} contents XML content of the KML file
 */
function handleKmlContent(contents) {
    var dom = new DOMParser().parseFromString(contents, 'text/xml');
    var tmp = kml(dom);

    if (tmp.features.length <= 0) {
        alert('Invalid kml file');
    } else {
        parsedJson.push(tmp);
        geoJson = false;
    }

}

/**
 * @param {object} file
 */
function readFile(file) {
    if (file.name.substr(-4).toLowerCase() === '.kmz') {
        handleKmz(file);
        return;
    }

    var reader = new FileReader();
    reader.onload = function(event) {
        var contents = event.target.result;
        var json = false;

        try {
            json = JSON.parse(contents);
        } catch (err) {
            // ..
        }

        if (json !== false) {
            parsedJson.push(json);
            geoJson = false;
        } else {
            handleKmlContent(contents);
        }

        refresh();
    };

    reader.onerror = function(event) {
        alert('File could not be read! Code ' + event.target.error.code);
    };

    reader.readAsText(file);
}

/**
 *
 */
function initMapLayerHandler() {
    if (parsedJson && parsedJson.length > 0) {
        $('#maplayer-data').val(JSON.stringify(parsedJson));
    }

    $('#maplayer-form').change(function() {
        var files = $('#maplayer-files').get(0).files; // FileList object

        if (files.length > 0) {
            parsedJson = [];
        }

        for (var i = 0; i < files.length; i++) {
            readFile(files[i]);
        }
    });

    $('#maplayer-lng, #maplayer-lat, #maplayer-zoom, #maplayer-color').on('change', function() {
        refresh();
    });

    mapboxgl.accessToken = window.mapboxToken;
    var container = $('#map').get(0);

    map = new mapboxgl.Map({
        container: container, // HTML container id
        style: 'mapbox://styles/mapbox/streets-v11', // style URL
    });

    map.addControl(new mapboxgl.FullscreenControl());

    // add zoom and rotation controls to the map.
    map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');

    map.on('load', function() {
        refresh();
    });

    map.on('zoom', function() {
        zoom = Math.round(map.getZoom());
        $('#maplayer-zoom').val(zoom);
    });

    map.on('click', function (e) {
        lat = e.lngLat.wrap().lat;
        lng = e.lngLat.wrap().lng;
        $('#maplayer-lat').val(lat);
        $('#maplayer-lng').val(lng);
    });
}

/**
 *
 */
function refresh() {
    if (map === false) {
        return;
    }

    var changedLng = parseFloat($('#maplayer-lng').val());
    var changedLat = parseFloat($('#maplayer-lat').val());
    var changedZoom = parseFloat($('#maplayer-zoom').val());
    var color = $('#maplayer-color').val() || 'blue';

    if (parsedJson && parsedJson.length > 0) {
        geoJson = parsedJson;

        var layers = map.getStyle().layers;

        for (var i in layers) {
            if (parsedJson !== false && layers[i].id.indexOf('regionlayer_line') === 0) {
                map.removeLayer(layers[i].id);
                continue;
            }
        }

        $('#maplayer-data').val(JSON.stringify(geoJson));

        for (var i = 0; i < geoJson.length; i++) {
            map.addLayer(
                {
                    'id': 'regionlayer_line' + Math.round(Math.random() * 10000),
                    'type': 'line',
                    'source': {
                        'type': 'geojson',
                        'data': geoJson[i],
                    },
                    'paint': {
                        'line-color': color,
                        'line-width': 3,
                        'line-opacity': 0.5
                    }
                }
            );
        }
    }

    if (isNaN(changedLng)) {
        changedLng = lng;
    }

    if (isNaN(changedLat)) {
        changedLat = lat;
    }

    if (isNaN(changedZoom)) {
        changedZoom = zoom;
    }

    if (changedLng !== lng || changedLat !== lat || changedZoom !== zoom) {
        lng = changedLng;
        lat = changedLat;
        zoom = changedZoom;
        map.jumpTo({center: [lng,lat], zoom: zoom});
    }
}
