function addStation() {
    var station = new FormData();

    var name        = document.getElementById("station-name").value;
    var lat         = document.getElementById("station-lat").value;
    var lon         = document.getElementById("station-lon").value;
    var alt         = document.getElementById("station-alt").value;
    var description = document.getElementById("station-description").value;

    station.append('name',         name);
    station.append('lat',          lat);
    station.append('lon',          lon);
    station.append('alt',          alt);
    station.append('description',  description);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        window.location.href = "/station/" + JSON.parse(this.responseText).id;
      }
    };

    xhttp.open("POST", "/api/station/add", true);
    xhttp.send(station);
}

function updateStationMap() {
    setTimeout(function(){ stationMap.invalidateSize()}, 400);
  
    var lat = document.getElementById("station-lat").value;
    var lon = document.getElementById("station-lon").value;
  
    var newLatLon = new L.LatLng(lat, lon);
    stationMarker.setLatLng(newLatLon); 
  
    stationMap.setView(stationMarker.getLatLng(), 5); 
  }
  

var stationMap = L.map('stationMap').setView([0, 0], 1);
    
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(stationMap);

var stationMarker = L.marker([0, 0]).addTo(stationMap).bindPopup('Your station coordinates');
