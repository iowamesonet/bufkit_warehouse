var map,
  selectedFeature,
  selectControli,
  selectControlj,
  selectControlk,
  selectControll,
  selectControlm,
  selectControln,
  layer;

function cb_siteOver(feature) {
  selectedFeature = feature;
  document.getElementById("sid").innerHTML = feature.attributes.sid;
  document.getElementById("sname").innerHTML = feature.attributes.sname;
  document.getElementById("tname").innerHTML = feature.attributes.tname;
  document.getElementById("vname").innerHTML = feature.attributes.vname;
  document.getElementById("wname").innerHTML = feature.attributes.wname;
  document.getElementById("rname").innerHTML = feature.attributes.rname;
  document.getElementById("srname").innerHTML = feature.attributes.srname;
  document.getElementById("nam").innerHTML = feature.attributes.nam;
  document.getElementById("namm").innerHTML = feature.attributes.namm;
  document.getElementById("gfs").innerHTML = feature.attributes.gfs;
  document.getElementById("gfsm").innerHTML = feature.attributes.gfsm;
  document.getElementById("rap").innerHTML = feature.attributes.rap;
  document.getElementById("xname").innerHTML = feature.attributes.xname;
  document.getElementById("meteo").innerHTML = feature.attributes.meteo;

  popup = new OpenLayers.Popup(
    "chicken",
    feature.geometry.getBounds().getCenterLonLat(),
    new OpenLayers.Size(300, 160),
    "<div style='font-size:.8em'><b><u>" +
      feature.attributes.sid +
      "</b></u><br /><a href='" +
      feature.attributes.nam_l +
      "'>" +
      feature.attributes.nam +
      "</a> - <a href='" +
      feature.attributes.nam_cobb +
      "'>" +
      feature.attributes.view_nam_cobb +
      "</a><br /><a href='" +
      feature.attributes.namm_l +
      "'>" +
      feature.attributes.namm +
      "</a> - <a href='" +
      feature.attributes.namm_cobb +
      "'>" +
      feature.attributes.view_nam_cobb +
      "</a><br /><a href='" +
      feature.attributes.gfs_l +
      "'>" +
      feature.attributes.gfs +
      "</a> - <a href='" +
      feature.attributes.gfs_cobb +
      "'>" +
      feature.attributes.view_gfs_cobb +
      "</a><br /><a href='" +
      feature.attributes.gfsm_l +
      "'>" +
      feature.attributes.gfsm +
      "</a> - <a href='" +
      feature.attributes.gfsm_cobb +
      "'>" +
      feature.attributes.view_gfs_cobb +
      "</a><br /><a href='" +
      feature.attributes.rap_l +
      "'>" +
      feature.attributes.rap +
      "</a><br /><a href='" +
      feature.attributes.rrfs_l +
      "'>" +
      feature.attributes.rrfs_i +
      "</a><br /><a href='" +
      feature.attributes.ewrf_l1 +
      "'>" +
      feature.attributes.ewrf_id1 +
      "</a>    <a href='" +
      feature.attributes.ewrf_l2 +
      "'>" +
      feature.attributes.ewrf_id2 +
      "</a><br /><a href='" +
      feature.attributes.xname +
      "'>" +
      feature.attributes.meteo +
      "</a><br /><a href='" +
      feature.attributes.a_link +
      "'>View Today's Archive!</a></div>",
    true,
  );
  feature.popup = popup;
  map.addPopup(popup);
}

function cb_siteOut(feature) {
  map.removePopup(feature.popup);
  document.getElementById("sid").innerHTML = "No Site Selected";
  document.getElementById("sname").innerHTML = "";
  document.getElementById("tname").innerHTML = "";
  document.getElementById("vname").innerHTML = "";
  document.getElementById("wname").innerHTML = "";
  document.getElementById("nam").innerHTML = "";
  document.getElementById("namm").innerHTML = "";
  document.getElementById("gfs").innerHTML = "";
  document.getElementById("gfsm").innerHTML = "";
  document.getElementById("xname").innerHTML = "";
  document.getElementById("meteo").innerHTML = "";
  document.getElementById("rname").innerHTML = "";
  document.getElementById("srname").innerHTML = "";
  document.getElementById("rap").innerHTML = "";

  feature.popup.destroy();
  feature.popup = null;
}

function init() {
  // Build Map Object
  map = new OpenLayers.Map("map", {
    projection: new OpenLayers.Projection("EPSG:900913"),
    displayProjection: new OpenLayers.Projection("EPSG:4326"),
    units: "m",
    wrapDateLine: false,
    numZoomLevels: 18,
    maxResolution: 156543.0339,
    maxExtent: new OpenLayers.Bounds(
      -20037508,
      -20037508,
      20037508,
      20037508.34,
    ),
  });

  // Traditional Google Map Layer
  var googleLayer = new OpenLayers.Layer.Google("Google Streets", {
    sphericalMercator: true,
  });

  var styleMap = new OpenLayers.StyleMap({
    default: {
      fillColor: "black",
      strokeColor: "yellow",
      strokeWidth: 2,
      pointRadius: 5,
      strokeOpacity: 1,
    },
    select: {
      fillOpacity: 1,
      strokeColor: "white",
      fillColor: "red",
    },
  });

  var geojsoni = new OpenLayers.Layer.GML(
    "Global Bufkit Sites",
    "global_network.php?" + new Date().getTime(),
    {
      projection: new OpenLayers.Projection("EPSG:4326"),
      format: OpenLayers.Format.GeoJSON,
      styleMap: styleMap,
    },
  );

  function get_my_url(bounds) {
    var res = this.map.getResolution();
    var x = Math.round(
      (bounds.left - this.maxExtent.left) / (res * this.tileSize.w),
    );
    var y = Math.round(
      (this.maxExtent.top - bounds.top) / (res * this.tileSize.h),
    );
    var z = this.map.getZoom();

    var path =
      z +
      "/" +
      x +
      "/" +
      y +
      "." +
      this.type +
      "?" +
      parseInt(Math.random() * 9999);
    var url = this.url;
    if (url instanceof Array) {
      url = this.selectUrl(path, url);
    }
    return url + this.service + "/" + this.layername + "/" + path;
  }
  var n0q = new OpenLayers.Layer.TMS(
    "NEXRAD Base Reflectivity",
    "https://mesonet.agron.iastate.edu/cache/tile.py/",
    // Find more layer names here: https://mesonet.agron.iastate.edu/ogc/
    {
      layername: "nexrad-n0q-900913",
      service: "1.0.0",
      type: "png",
      visibility: true,
      getURL: get_my_url,
      isBaseLayer: false,
    },
  );
  var dmx = new OpenLayers.Layer.TMS(
    "DMX Base Reflectivity",
    "https://mesonet.agron.iastate.edu/cache/tile.py/",
    // Find more layer names here: https://mesonet.agron.iastate.edu/ogc/
    {
      layername: "ridge::DMX-N0Q-0",
      service: "1.0.0",
      type: "png",
      visibility: false,
      getURL: get_my_url,
      isBaseLayer: false,
    },
  );
  var irsat = new OpenLayers.Layer.TMS(
    "GOES 13 IR Satellite",
    "https://mesonet.agron.iastate.edu/cache/tile.py/",
    {
      layername: "goes13-ir-4km-900913",
      service: "1.0.0",
      type: "png",
      visibility: false,
      getURL: get_my_url,
      isBaseLayer: false,
    },
  );
  var vissat = new OpenLayers.Layer.TMS(
    "GOES 13 Vis Satellite",
    "https://mesonet.agron.iastate.edu/cache/tile.py/",
    {
      layername: "goes13-vis-1km-900913",
      service: "1.0.0",
      type: "png",
      visibility: false,
      getURL: get_my_url,
      isBaseLayer: false,
    },
  );

  // Layer for NWS issued warnings, needs some help...
  var warnings = new OpenLayers.Layer.WMS(
    "NWS Watch/Warnings (via IEM)",
    "https://mesonet.agron.iastate.edu/cgi-bin/wms/us/wwa.cgi?",
    { layers: "warnings_c", format: "image/png", transparent: "true" },
  );
  warnings.setVisibility(false);

  var osm = new OpenLayers.Layer.OSM();
  map.addLayers([osm, geojsoni, irsat, vissat, n0q, warnings]);

  // Provide hover capabilities over road_condition layer
  selectControli = new OpenLayers.Control.SelectFeature(geojsoni, {
    onSelect: cb_siteOver,
    onUnselect: cb_siteOut,
  });

  map.addControl(selectControli);
  selectControli.activate();

  var proj = new OpenLayers.Projection("EPSG:4326");
  var proj2 = new OpenLayers.Projection("EPSG:900913");
  var point = new OpenLayers.LonLat(-30, 40);
  point.transform(proj, proj2);

  map.addControl(new OpenLayers.Control.LayerSwitcher({ id: "ls" }));
  map.addControl(new OpenLayers.Control.MousePosition());
  map.getControl("ls").maximizeControl();

  map.setCenter(point, 2);
}
