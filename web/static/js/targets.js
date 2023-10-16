function add() {
    var target = new FormData();

    var name        = document.getElementById("target-name").value;
    var type        = document.getElementById("target-type").value;
    var orbit       = document.getElementById("target-orbit").value;
    var locator     = document.getElementById("target-locator-type").value;

    if (locator == "tle-locator") {
        locator = {
            "tle": {
                "line1": document.getElementById("tle-locator-line1").value,
                "line2": document.getElementById("tle-locator-line2").value
            }
        };
    } else {
        locator = {};
    }

    locator = JSON.stringify(locator);


    target.append('name', name);
    target.append('type', type);
    target.append('orbit', orbit);
    target.append('locator', locator);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.href = "/target/" + JSON.parse(this.responseText).id;
        }
    };

    xhttp.open("POST", "/api/target/add", true);
    xhttp.send(target);
}

function locatorChange() {
    var locatorType = document.getElementById("target-locator-type").value;

    //hide all locators options
    var locators = document.getElementsByClassName("locatorType");

    for (var i = 0; i < locators.length; i++) {
        locators[i].style.display = "none";
    }

    if (locatorType != "none") {
        document.getElementById(locatorType).style.display = "block";
    }
}