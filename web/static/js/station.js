var openReceiver = null;

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

function rmAutoPlan(id) {
    var ele = document.getElementsByName(id);
    ele[0].remove();
}

function addAutoPlan() {
    var sel = document.getElementById("select-transmitter");

    // not not add is added
    if (getAutoPlaned().includes(sel.value)) return;

    var text = sel.options[sel.selectedIndex].text;

    document.getElementById("receiver-auto-plan").innerHTML += 
        '<span name="' + sel.value + '" class="status ms-2 mt-2" style="font-size: 10px;">' + text + ' <button type="button" class="btn btn-danger btn-sm" aria-label="del" onclick=\'rmAutoPlan("' + sel.value  + '")\'>X</button></span>';
}

function getAutoPlaned() {
    var list = [];

    var area = document.getElementById("receiver-auto-plan");
    var transmitters = area.getElementsByClassName("status");

    for (var i = 0; i < transmitters.length; i++) {
        list.push(transmitters[i].getAttribute("name"));
    }

    return list;
}

function loadReceiver(id) {
    var receiver = new FormData();

    receiver.append("id", id);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);

            document.getElementById("receiver-freq").value        = data.freq;
            document.getElementById("receiver-band").value        = data.band;
            document.getElementById("receiver-antenna").value     = data.antenna;
            document.getElementById("receiver-gain").value        = data.gain;
            document.getElementById("receiver-params").value      = JSON.stringify(data.params);

            var block = "";

            for (var i = 0; i < data.autoPlan.values.length; i++) {
                block += '<span name="' + data.autoPlan.values[i].id + '" class="status ms-2 mt-2" style="font-size: 10px;">' + 
                         data.autoPlan.values[i].target + " - " +
                         data.autoPlan.values[i].modulation + " - " +
                         data.autoPlan.values[i].dataType + " @ " + 
                         data.autoPlan.values[i].freq + 'Hz <button type="button" class="btn btn-danger btn-sm" aria-label="del" onclick=\'rmAutoPlan("' + data.autoPlan.values[i].id + '")\'>X</button></span>';
            }

            document.getElementById("receiver-auto-plan").innerHTML = block;

            document.getElementById("receiverModal").click();
            openReceiver = data.id;
        }
    };

    xhttp.open("POST", "/api/receiver/get", true);
    xhttp.send(receiver);
}

function saveReceiver(stationID) {
    var receiver = new FormData();

    var freq       = document.getElementById("receiver-freq").value;
    var band       = document.getElementById("receiver-band").value;
    var antenna    = document.getElementById("receiver-antenna").value;
    var gain       = document.getElementById("receiver-gain").value;
    var params     = document.getElementById("receiver-params").value;
    var autoPlan   = JSON.stringify(getAutoPlaned());
    var id         = openReceiver;

    receiver.append('id', id);
    receiver.append('freq', freq);
    receiver.append('band', band);
    receiver.append('antenna', antenna);
    receiver.append('gain', gain);
    receiver.append('params', params);
    receiver.append('autoPlan', autoPlan);
    receiver.append('station', stationID);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };

    xhttp.open("POST", "/api/receiver/save", true);
    xhttp.send(receiver);
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
