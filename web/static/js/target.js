var openTransmitter = null;

function modulationChange() {
    var modulation = document.getElementById("transmitter-modulation");

    if (modulation.options[modulation.selectedIndex].text == "LORA") {
        document.getElementById("lora-params").style.display = "flex";
    } else {
        document.getElementById("lora-params").style.display = "none";
    }
}

function save(targetID) {
    var transmitter = new FormData();

    var modulationEl = document.getElementById("transmitter-modulation");

    var freq       = document.getElementById("transmitter-freq").value;
    var band       = document.getElementById("transmitter-band").value;
    var antenna    = document.getElementById("transmitter-antenna").value;
    var modulation = document.getElementById("transmitter-modulation").value;
    var datatype   = document.getElementById("transmitter-datatype").value;
    var priority   = document.getElementById("transmitter-priority").value;
    var pipe       = document.getElementById("transmitter-pipe").value;

    var sf         = document.getElementById("transmitter-sf").value;
    var codingrate = document.getElementById("transmitter-codingrate").value;
    var syncword   = document.getElementById("transmitter-syncword").value;
    var preamble   = document.getElementById("transmitter-preamble").value;

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

    if (modulationEl.options[modulationEl.selectedIndex].text == "LORA") {
        transmitter.append('lora',           'true');
        transmitter.append('sf',             sf);
        transmitter.append('codingRate',     codingrate);
        transmitter.append('syncWord',       syncword);
        transmitter.append('preambleLength', preamble);
    }

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