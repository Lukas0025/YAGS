function mapAddTLEPath(map, line1, line2, start, end) {
    var pointList = [];

    var satrec = satellite.twoline2satrec(line1, line2);
    
    for (var i = Date.parse(start); i < Date.parse(end); i += 1000) {
      var positionAndVelocity = satellite.propagate(satrec, new Date(i));
      var gmst = satellite.gstime(new Date(i));
    
      var positionEci = positionAndVelocity.position;
      var positionGd = satellite.eciToGeodetic(positionEci, gmst);
    
      pointList.push(
        new L.LatLng(satellite.degreesLat(positionGd.latitude), satellite.degreesLong(positionGd.longitude))
      );
    }
    
    var targetPath = new L.Polyline(pointList, {
      color: 'red',
      weight: 3,
      opacity: 0.5,
      smoothFactor: 1
    });
    
    targetPath.addTo(map);
}