$(function() {
  if (!window.wholesalerLocation) return;

  var createMap = function () {
    var center = SMap.Coords.fromWGS84(window.wholesalerLocation.lng, window.wholesalerLocation.lat);
    var map = new SMap(JAK.gel('wholesalerMap'), center, 13);
    map.addDefaultLayer(SMap.DEF_BASE).enable();

    map.addControl(new SMap.Control.Zoom(null, {showZoomMenu: false}), {right:'5px', top:'5px'});
    map.addControl(new SMap.Control.Mouse(SMap.MOUSE_PAN));
    map.addControl(new SMap.Control.Keyboard(SMap.KB_PAN));

    var layer = new SMap.Layer.Marker();
    map.addLayer(layer);
    layer.enable();

    var options = {};
    var marker = new SMap.Marker(center, 'wholesalerLocationMarker', options);
    layer.addMarker(marker);
  };

  Loader.async = true;
  Loader.load(null, null, createMap);
});
