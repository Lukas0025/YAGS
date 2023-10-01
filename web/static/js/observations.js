function plan() {
    var plan = new FormData();

    var transmitter = document.getElementById("plan-transmitter").value;
    var receiver    = document.getElementById("plan-receiver").value;
    var start       = document.getElementById("plan-start").value;
    var end         = document.getElementById("plan-end").value;

    plan.append('transmitter', transmitter);
    plan.append('receiver',    receiver);
    plan.append('start',       start);
    plan.append('end',         end);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("created-id").innerHTML = JSON.parse(this.responseText).id;
            document.getElementById("created-alert").style.display = "block";
        }
    };

    xhttp.open("POST", "/api/observation/plan", true);
    xhttp.send(plan);
}