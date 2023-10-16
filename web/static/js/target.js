var openTransmitter = null;

function save(targetID) {
    var transmitter = new FormData();

    var freq       = document.getElementById("transmitter-freq").value;
    var band       = document.getElementById("transmitter-band").value;
    var antenna    = document.getElementById("transmitter-antenna").value;
    var modulation = document.getElementById("transmitter-modulation").value;
    var datatype   = document.getElementById("transmitter-datatype").value;
    var priority   = document.getElementById("transmitter-priority").value;
    var pipe       = document.getElementById("transmitter-pipe").value;
    var id         = openTransmitter;

    transmitter.append('id', id);
    transmitter.append('freq', freq);
    transmitter.append('band', band);
    transmitter.append('antenna', antenna);
    transmitter.append('modulation', modulation);
    transmitter.append('dataType', datatype);
    transmitter.append('priority', priority);
    transmitter.append('pipe', pipe);
    transmitter.append('target', targetID);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };

    xhttp.open("POST", "/api/transmitter/save", true);
    xhttp.send(transmitter);
}

function loadTransmitter(id) {
    var xhttp = new XMLHttpRequest();

    var transmitter = new FormData();

    transmitter.append('id', id);

    xhttp.open("POST", "/api/transmitter/get", true);
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var data = JSON.parse(this.responseText);

          document.getElementById("transmitter-freq").value       = data.freq;
          document.getElementById("transmitter-band").value       = data.band;
          document.getElementById("transmitter-antenna").value    = data.antenna;
          document.getElementById("transmitter-modulation").value = data.modulation;
          document.getElementById("transmitter-datatype").value   = data.dataType;
          document.getElementById("transmitter-priority").value   = data.priority;
          document.getElementById("transmitter-pipe").value       = data.pipe;
            
          document.getElementById("transmitterModal").click();
          openTransmitter = data.id;
        }
    };

    
    xhttp.send(transmitter);
}