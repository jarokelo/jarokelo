/* eslint-disable */
/* @link https://github.com/mapbox/togeojson */
// cast array x into numbers
// get the content of a text node, if any
function nodeVal(x) {
    if (x && x.normalize) {
        x.normalize();
    }
    return x && x.textContent || "";
}

function getLineStyle(extensions) {
    var style = {};
    if (extensions) {
        var lineStyle = get1(extensions, "line");
        if (lineStyle) {
            var color = nodeVal(get1(lineStyle, "color")),
                opacity = parseFloat(nodeVal(get1(lineStyle, "opacity"))),
                width = parseFloat(nodeVal(get1(lineStyle, "width")));
            if (color) style.stroke = color;
            if (!isNaN(opacity)) style["stroke-opacity"] = opacity;
            // GPX width is in mm, convert to px with 96 px per inch
            if (!isNaN(width)) style["stroke-width"] = width * 96 / 25.4;
        }
    }
    return style;
}

// get the contents of multiple text nodes, if present
function getMulti(x, ys) {
    var o = {};
    var n;
    var k;
    for (k = 0; k < ys.length; k++) {
        n = get1(x, ys[k]);
        if (n) o[ys[k]] = nodeVal(n);
    }
    return o;
}
function getProperties(node) {
    var prop = getMulti(node, [
        "name",
        "cmt",
        "desc",
        "type",
        "time",
        "keywords"]);

    var links = node.getElementsByTagName("link");
    if (links.length) prop.links = [];
    for (var i = 0; i < links.length; i++) {
        prop.links.push(
            Object.assign(
                { href: links[i].getAttribute("href") },
                getMulti(links[i], ["text", "type"])));


    }
    return prop;
}

// one Y child of X, if any, otherwise null
function get1(x, y) {
    var n = x.getElementsByTagName(y);
    return n.length ? n[0] : null;
}

function coordPair(x) {
    var ll = [
        parseFloat(x.getAttribute("lon")),
        parseFloat(x.getAttribute("lat"))];

    var ele = get1(x, "ele");
    // handle namespaced attribute in browser
    var heartRate = get1(x, "gpxtpx:hr") || get1(x, "hr");
    var time = get1(x, "time");
    var e;
    if (ele) {
        e = parseFloat(nodeVal(ele));
        if (!isNaN(e)) {
            ll.push(e);
        }
    }
    return {
        coordinates: ll,
        time: time ? nodeVal(time) : null,
        heartRate: heartRate ? parseFloat(nodeVal(heartRate)) : null };

}
function getRoute(node) {
    var line = getPoints(node, "rtept");
    if (!line.line) return;
    return {
        type: "Feature",
        properties: Object.assign(
            getProperties(node),
            getLineStyle(get1(node, "extensions"))),

        geometry: {
            type: "LineString",
            coordinates: line.line } };


}
function getPoints(node, pointname) {
    var pts = node.getElementsByTagName(pointname);
    var line = [];
    var times = [];
    var l = pts.length;
    var heartRates = undefined;
    if (l < 2) return {}; // Invalid line in GeoJSON
    for (var i = 0; i < l; i++) {
        var c = coordPair(pts[i]);
        line.push(c.coordinates);
        if (c.time) times.push(c.time);
        if (c.heartRate || heartRates) {
            if (!heartRates) heartRates = Array(i).fill(null);
            heartRates.push(c.heartRate || null);
        }
    }
    return {
        line: line,
        times: times,
        heartRates: heartRates || [] };

}
function getTrack(node) {
    var segments = node.getElementsByTagName("trkseg");
    var track = [];
    var times = [];
    var heartRates = [];
    var line;
    for (var i = 0; i < segments.length; i++) {
        line = getPoints(segments[i], "trkpt");
        if (line) {
            if (line.line) track.push(line.line);
            if (line.times && line.times.length) times.push(line.times);
            if (heartRates.length || line.heartRates && line.heartRates.length) {
                if (!heartRates.length) {
                    for (var s = 0; s < i; s++) {
                        heartRates.push(Array(track[s].length).fill(null));
                    }
                }
                if (line.heartRates && line.heartRates.length) {
                    heartRates.push(line.heartRates);
                } else {
                    heartRates.push(Array(line.line.length || 0).fill(null));
                }
            }
        }
    }
    if (track.length === 0) return;
    var properties = Object.assign(
        getProperties(node),
        getLineStyle(get1(node, "extensions")));

    if (times.length)
        properties.coordTimes = track.length === 1 ? times[0] : times;
    if (heartRates.length)
        properties.heartRates = track.length === 1 ? heartRates[0] : heartRates;
    return {
        type: "Feature",
        properties: properties,
        geometry: {
            type: track.length === 1 ? "LineString" : "MultiLineString",
            coordinates: track.length === 1 ? track[0] : track } };


}

function getPoint(node) {
    return {
        type: "Feature",
        properties: Object.assign(getProperties(node), getMulti(node, ["sym"])),
        geometry: {
            type: "Point",
            coordinates: coordPair(node).coordinates } };


}

function gpxGen(doc) {
    var tracks = doc.getElementsByTagName("trk");
    var routes = doc.getElementsByTagName("rte");
    var waypoints = doc.getElementsByTagName("wpt");
    var results = [];

    for (var i = 0; i < tracks.length; i++) {
        var feature = getTrack(tracks[i]);

        if (feature) {
            results.push(feature);
        }
    }

    for (var i = 0; i < routes.length; i++) {
        var feature = getRoute(routes[i]);

        if (feature) {
            results.push(feature);
        }
    }
    for (var i = 0; i < waypoints.length; i++) {
        results.push(getPoint(waypoints[i]));
    }

    return results;
}

function gpx(doc) {
    return {
        type: "FeatureCollection",
        features: Array.from(gpxGen(doc)) };

}

var removeSpace = /\s*/g;
var trimSpace = /^\s*|\s*$/g;
var splitSpace = /\s+/;

// generate a short, numeric hash of a string
function okhash(x) {
    if (!x || !x.length) return 0;
    var h = 0;
    for (var i = 0; i < x.length; i++) {
        h = (h << 5) - h + x.charCodeAt(i) | 0;
    }
    return h;
}

// one Y child of X, if any, otherwise null
function get1$1(x, y) {
    var n = x.getElementsByTagName(y);
    return n.length ? n[0] : null;
}

// get one coordinate from a coordinate array, if any
function coord1(v) {
    return v.
    replace(removeSpace, "").
    split(",").
    map(parseFloat);
}

// get all coordinates from a coordinate array as [[],[]]
function coord(v) {
    return v.
    replace(trimSpace, "").
    split(splitSpace).
    map(coord1);
}

function xml2str(node) {
    if (node.xml !== undefined) return node.xml;
    if (node.tagName) {
        var output = node.tagName;
        for (var i = 0; i < node.attributes.length; i++) {
            output += node.attributes[i].name + node.attributes[i].value;
        }
        for (var i = 0; i < node.childNodes.length; i++) {
            output += xml2str(node.childNodes[i]);
        }
        return output;
    }
    if (node.nodeName === "#text") {
        return (node.nodeValue || node.value || "").trim();
    }
    if (node.nodeName === "#cdata-section") {
        return node.nodeValue;
    }
    return "";
}

var geotypes = ["Polygon", "LineString", "Point", "Track", "gx:Track"];

function kmlColor(v) {
    var color, opacity;
    v = v || "";
    if (v.substr(0, 1) === "#") {
        v = v.substr(1);
    }
    if (v.length === 6 || v.length === 3) {
        color = v;
    }
    if (v.length === 8) {
        opacity = parseInt(v.substr(0, 2), 16) / 255;
        color = "#" + v.substr(6, 2) + v.substr(4, 2) + v.substr(2, 2);
    }
    return [color, isNaN(opacity) ? undefined : opacity];
}

function gxCoords(root) {
    var elems = root.getElementsByTagName("coord");
    var coords = [];
    var times = [];
    if (elems.length === 0) elems = root.getElementsByTagName("gx:coord");
    for (var i = 0; i < elems.length; i++) {
        coords.push(
            nodeVal(elems[i]).
            split(" ").
            map(parseFloat));

    }
    var timeElems = root.getElementsByTagName("when");
    for (var j = 0; j < timeElems.length; j++) times.push(nodeVal(timeElems[j]));
    return {
        coords: coords,
        times: times };

}

function getGeometry(root) {
    var geomNode;
    var geomNodes;
    var i;
    var j;
    var k;
    var geoms = [];
    var coordTimes = [];
    if (get1$1(root, "MultiGeometry")) {
        return getGeometry(get1$1(root, "MultiGeometry"));
    }
    if (get1$1(root, "MultiTrack")) {
        return getGeometry(get1$1(root, "MultiTrack"));
    }
    if (get1$1(root, "gx:MultiTrack")) {
        return getGeometry(get1$1(root, "gx:MultiTrack"));
    }
    for (i = 0; i < geotypes.length; i++) {
        geomNodes = root.getElementsByTagName(geotypes[i]);
        if (geomNodes) {
            for (j = 0; j < geomNodes.length; j++) {
                geomNode = geomNodes[j];
                if (geotypes[i] === "Point") {
                    geoms.push({
                        type: "Point",
                        coordinates: coord1(nodeVal(get1$1(geomNode, "coordinates"))) });

                } else if (geotypes[i] === "LineString") {
                    geoms.push({
                        type: "LineString",
                        coordinates: coord(nodeVal(get1$1(geomNode, "coordinates"))) });

                } else if (geotypes[i] === "Polygon") {
                    var rings = geomNode.getElementsByTagName("LinearRing"),
                        coords = [];
                    for (k = 0; k < rings.length; k++) {
                        coords.push(coord(nodeVal(get1$1(rings[k], "coordinates"))));
                    }
                    geoms.push({
                        type: "Polygon",
                        coordinates: coords });

                } else if (geotypes[i] === "Track" || geotypes[i] === "gx:Track") {
                    var track = gxCoords(geomNode);
                    geoms.push({
                        type: "LineString",
                        coordinates: track.coords });

                    if (track.times.length) coordTimes.push(track.times);
                }
            }
        }
    }
    return {
        geoms: geoms,
        coordTimes: coordTimes };

}

function getPlacemark(root, styleIndex, styleMapIndex, styleByHash) {
    var geomsAndTimes = getGeometry(root);
    var i;
    var properties = {};
    var name = nodeVal(get1$1(root, "name"));
    var address = nodeVal(get1$1(root, "address"));
    var styleUrl = nodeVal(get1$1(root, "styleUrl"));
    var description = nodeVal(get1$1(root, "description"));
    var timeSpan = get1$1(root, "TimeSpan");
    var timeStamp = get1$1(root, "TimeStamp");
    var extendedData = get1$1(root, "ExtendedData");
    var lineStyle = get1$1(root, "LineStyle");
    var polyStyle = get1$1(root, "PolyStyle");
    var visibility = get1$1(root, "visibility");

    if (!geomsAndTimes.geoms.length) return;
    if (name) properties.name = name;
    if (address) properties.address = address;
    if (styleUrl) {
        if (styleUrl[0] !== "#") {
            styleUrl = "#" + styleUrl;
        }

        properties.styleUrl = styleUrl;
        if (styleIndex[styleUrl]) {
            properties.styleHash = styleIndex[styleUrl];
        }
        if (styleMapIndex[styleUrl]) {
            properties.styleMapHash = styleMapIndex[styleUrl];
            properties.styleHash = styleIndex[styleMapIndex[styleUrl].normal];
        }
        // Try to populate the lineStyle or polyStyle since we got the style hash
        var style = styleByHash[properties.styleHash];
        if (style) {
            if (!lineStyle) lineStyle = get1$1(style, "LineStyle");
            if (!polyStyle) polyStyle = get1$1(style, "PolyStyle");
            var iconStyle = get1$1(style, "IconStyle");
            if (iconStyle) {
                var icon = get1$1(iconStyle, "Icon");
                if (icon) {
                    var href = nodeVal(get1$1(icon, "href"));
                    if (href) properties.icon = href;
                }
            }
        }
    }
    if (description) properties.description = description;
    if (timeSpan) {
        var begin = nodeVal(get1$1(timeSpan, "begin"));
        var end = nodeVal(get1$1(timeSpan, "end"));
        properties.timespan = { begin: begin, end: end };
    }
    if (timeStamp) {
        properties.timestamp = nodeVal(get1$1(timeStamp, "when"));
    }
    if (lineStyle) {
        var linestyles = kmlColor(nodeVal(get1$1(lineStyle, "color"))),
            color = linestyles[0],
            opacity = linestyles[1],
            width = parseFloat(nodeVal(get1$1(lineStyle, "width")));
        if (color) properties.stroke = color;
        if (!isNaN(opacity)) properties["stroke-opacity"] = opacity;
        if (!isNaN(width)) properties["stroke-width"] = width;
    }
    if (polyStyle) {
        var polystyles = kmlColor(nodeVal(get1$1(polyStyle, "color")));
        var pcolor = polystyles[0];
        var popacity = polystyles[1];
        var fill = nodeVal(get1$1(polyStyle, "fill"));
        var outline = nodeVal(get1$1(polyStyle, "outline"));
        if (pcolor) properties.fill = pcolor;
        if (!isNaN(popacity)) properties["fill-opacity"] = popacity;
        if (fill)
            properties["fill-opacity"] =
                fill === "1" ? properties["fill-opacity"] || 1 : 0;
        if (outline)
            properties["stroke-opacity"] =
                outline === "1" ? properties["stroke-opacity"] || 1 : 0;
    }
    if (extendedData) {
        var datas = extendedData.getElementsByTagName("Data"),
            simpleDatas = extendedData.getElementsByTagName("SimpleData");

        for (i = 0; i < datas.length; i++) {
            properties[datas[i].getAttribute("name")] = nodeVal(
                get1$1(datas[i], "value"));

        }
        for (i = 0; i < simpleDatas.length; i++) {
            properties[simpleDatas[i].getAttribute("name")] = nodeVal(simpleDatas[i]);
        }
    }
    if (visibility) {
        properties.visibility = nodeVal(visibility);
    }
    if (geomsAndTimes.coordTimes.length) {
        properties.coordTimes =
            geomsAndTimes.coordTimes.length === 1 ?
                geomsAndTimes.coordTimes[0] :
                geomsAndTimes.coordTimes;
    }
    var feature = {
        type: "Feature",
        geometry:
            geomsAndTimes.geoms.length === 1 ?
                geomsAndTimes.geoms[0] :
                {
                    type: "GeometryCollection",
                    geometries: geomsAndTimes.geoms },

        properties: properties };

    if (root.getAttribute("id")) feature.id = root.getAttribute("id");
    return feature;
}

function kmlGen(doc) {
    // styleindex keeps track of hashed styles in order to match feature
    var styleIndex = {};
    var styleByHash = {};
    // stylemapindex keeps track of style maps to expose in properties
    var styleMapIndex = {};
    // atomic geospatial types supported by KML - MultiGeometry is
    // handled separately
    // all root placemarks in the file
    var placemarks = doc.getElementsByTagName("Placemark");
    var styles = doc.getElementsByTagName("Style");
    var styleMaps = doc.getElementsByTagName("StyleMap");
    var result = [];

    for (var k = 0; k < styles.length; k++) {
        var hash = okhash(xml2str(styles[k])).toString(16);
        styleIndex["#" + styles[k].getAttribute("id")] = hash;
        styleByHash[hash] = styles[k];
    }
    for (var l = 0; l < styleMaps.length; l++) {
        styleIndex["#" + styleMaps[l].getAttribute("id")] = okhash(
            xml2str(styleMaps[l])).
        toString(16);
        var pairs = styleMaps[l].getElementsByTagName("Pair");
        var pairsMap = {};
        for (var m = 0; m < pairs.length; m++) {
            pairsMap[nodeVal(get1$1(pairs[m], "key"))] = nodeVal(
                get1$1(pairs[m], "styleUrl"));

        }
        styleMapIndex["#" + styleMaps[l].getAttribute("id")] = pairsMap;
    }

    for (var j = 0; j < placemarks.length; j++) {
        var feature = getPlacemark(
            placemarks[j],
            styleIndex,
            styleMapIndex,
            styleByHash);

        if (feature) {
            result.push(feature);
        }
    }

    return result;
}

function kml(doc) {
    return {
        type: "FeatureCollection",
        features: Array.from(kmlGen(doc)) };

}
