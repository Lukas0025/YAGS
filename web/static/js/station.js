function updateStation(id) {
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
    station.append('id',           id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };

    xhttp.open("POST", "/api/station/update", true);
    xhttp.send(station);
}

function deleteStation(id) {
    var station = new FormData();

    station.append('id', id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };

    xhttp.open("POST", "/api/station/delete", true);
    xhttp.send(station);
}

function apiRegenerate(id) {
    var station = new FormData();

    station.append('id', id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };

    xhttp.open("POST", "/api/station/apiRegenerate", true);
    xhttp.send(station);
}