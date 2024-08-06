function plan() {
    var plan = new FormData();

    var transmitter = document.getElementById("plan-transmitter").value;
    var receiver    = document.getElementById("plan-receiver").value;
    var start       = document.getElementById("plan-start").value;
    var delay       = document.getElementById("plan-after-down").value;
    var data        = document.getElementById("plan-data").value;

    var delaydUse   = document.getElementById("delayd-uplink").checked;

    if (delaydUse) {
        // 0xFFFFFFFF
        start = "2106-02-07T06:28:15";
    } else {
        delay = 0;
    }

    plan.append('transmitter', transmitter);
    plan.append('receiver',    receiver);
    plan.append('start',       start);
    plan.append('delay',       delay);
    plan.append('data',        data);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("created-id").innerHTML = JSON.parse(this.responseText).id;
            document.getElementById("created-alert").style.display = "block";
        }
    };

    xhttp.open("POST", "/api/uplink/plan", true);
    xhttp.send(plan);
}