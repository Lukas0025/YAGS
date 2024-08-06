var pages     = ["welcome", "users", "station", "rxtx", "plan", "connect"];
var pagesCntr = [dummyCntr, processUser, processStation, processRxTx, processPlan, connectCntr];
var aPage     = 0;
var data      = {};
var apiKey    = "";

function dummyCntr() {
    return true;
}

function connectCntr() {
    alert("Waiting until client is connected");
    return false;
}

function nextPage(useCntr = true) {
    if (aPage + 1 > pages.length) {
        window.location.href = "/";
        return;
    }

    if (useCntr && !pagesCntr[aPage]()) return;

    document.getElementById(pages[aPage]).style.display = "none";

    aPage++;

    if (aPage + 1 > pages.length)  document.getElementById("done").style.display = "block";
    else                           document.getElementById(pages[aPage]).style.display = "block";

    document.getElementById("progress-bar").style.width = aPage / pages.length * 100 + "%";
}

function processUser() {
    data["user"] = document.getElementById("username").value;
    data["pass"] = document.getElementById("pass1").value;

    if (data["user"] == "") {
        alert("Username cant be empty");
        return false;
    }

    if (data["pass"] == "") {
        alert("Password cant be empty");
        return false;
    }

    if (data["pass"] != document.getElementById("pass2").value) {
        alert("Passwords is not same");
        return false;
    }

    return true;
}

function processStation() {
    data["lat"] = document.getElementById("lat").value;
    data["lon"] = document.getElementById("lon").value;
    data["alt"] = document.getElementById("alt").value;

    if (data["alt"] == "") {
        alert("alt cant be empty");
        return false;
    }

    if (data["lon"] == "") {
        alert("lon cant be empty");
        return false;
    }

    if (data["lat"] == "") {
        alert("lat cant be empty");
        return false;
    }

    return true;
}

function processRxTx() {
    var radio = document.getElementById("radio").value;

    if      (radio == "RTLSDR") data["params"] = "{\"radio\":\"rtlsdr\",\"gain\":45,\"agc\":false,\"bias\":false,\"fs\":[250000,1024000,1536000,1792000,1920000,2048000,2160000,2400000,2560000,2880000,3200000],\"comment\":\"Auto created by SETUP\"}";
    else if (radio == "RFM95") {
        var dio = document.getElementById("dio").value;
        var nss = document.getElementById("nss").value;

        if (dio == "" || nss == "") {
            alert("Need dio0 and nss pin");
            return false;
        }

        data["params"] = "{\"radio\":\"RFM95\",\"nss\":" + nss + ",\"dio0\":" + dio + ",\"power\":10,\"comment\":\"Auto created by SETUP\"}";
    }

    return true;
}

function radioChange() {
    if (document.getElementById("radio").value == "RFM95") document.getElementById("rfm-options").style.display = "block";
    else                                                   document.getElementById("rfm-options").style.display = "none";
}

function processPlan() {
    data["plans"] = [];

    if (document.getElementById("noaa19").checked)        data["plans"].push("noaa19apt");
    if (document.getElementById("noaa18").checked)        data["plans"].push("noaa18apt");
    if (document.getElementById("noaa15").checked)        data["plans"].push("noaa15apt");
    if (document.getElementById("meteorm23").checked)     data["plans"].push("meteorm23lrpt");
    if (document.getElementById("meteorm24").checked)     data["plans"].push("meteorm24lrpt");
    if (document.getElementById("lucky7").checked)        data["plans"].push("lucky7");
    if (document.getElementById("stratosat").checked)     data["plans"].push("stratosat");

    data["plans"] = JSON.stringify(data["plans"]);

    // now create req on API and create seeds
    var dataP = new FormData();

    for (var key in data) {
        dataP.append(key, data[key]);
    }

    document.getElementById("nextBtn").style.display = "none";

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const json = JSON.parse(this.responseText);

            console.log(json);

            if (json["status"] == false) {
                alert("Error when try setup DB: " + req.responseText)
                return;
            }

            document.getElementById("apiKey").value = json["apikey"];
            apiKey                                  = json["apikey"];

            // pull status every 10s
            setTimeout(() => {
                getClientStatus();
            }, 10000);

            nextPage(false);

            document.getElementById("nextBtn").style.display = "block";
        }
    };

    xhttp.open("POST", "/api/cron/setup", true);
    xhttp.send(dataP);

    return false;
}

function getClientStatus() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const json = JSON.parse(this.responseText);

            if (json["status"] == true) {
                document.getElementById("waiting").style.display = "none";
                document.getElementById("connected").style.display = "block";

                if (aPage == 5) {
                    setTimeout(() => {
                        nextPage(false);
                    }, 2000);
                }
            }
        }
    };

    xhttp.open("GET", "/api/station/amIActive?apiKey=" + apiKey, true);
    xhttp.send();
    
    // pull status every 10s
    setTimeout(() => {
        getClientStatus();
    }, 10000);
}