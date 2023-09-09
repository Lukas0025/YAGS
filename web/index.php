<?php
    include __DIR__ . "/wsos/wsos/autoload.php";

    foreach (glob("DAL/*.php") as $filename) {
        include_once $filename;
    }

    $container = new \wsos\structs\container();
    $db        = new \wsos\database\drivers\inAppArray();
    $auth      = new \wsos\auth\basic\manager(DAL\user::class, "userName", "pass", "/login");

    //get current url
    $url = $_SERVER['REQUEST_URI'];

    // create Basic context
    $context = [
        "url" => $url,
        "menu_items" => [
            ["url" => "/",              "name" => "Dashboard",    "icon" => "/static/icons/dashboard.svg"],
            ["url" => "/observations",  "name" => "Observations", "icon" => "/static/icons/telescope.svg"],
            ["url" => "/stations",      "name" => "Stations",     "icon" => "/static/icons/radio.svg"],
            ["url" => "/targets",       "name" => "Targets",      "icon" => "/static/icons/focus-2.svg"],
            ["url" => "/modulations",   "name" => "Modulations",  "icon" => "/static/icons/wave-sine.svg"],
            ["url" => "/datatypes",     "name" => "Data Types",   "icon" => "/static/icons/file-analytics.svg"],
        ],

        "logined" => $auth->getActive()
    ];

    // register containers
    $container->register("DBDriver", $db);
    $container->register("templateLoader", new wsos\templates\loader(__DIR__ . "/VIEWS"));
    $container->register("context", $context);
    $container->register("auth", $auth);

    // seeds DB
    // do not do this in release!!
    include "seeds.php";

    if      ($url == "/")             include __DIR__ . "/CONTROLLERS/dashboard.php";
    else if ($url == "/observations") include __DIR__ . "/CONTROLLERS/observations.php";
    else if ($url == "/stations")     include __DIR__ . "/CONTROLLERS/stations.php";
    else if ($url == "/login")        include __DIR__ . "/CONTROLLERS/login.php";
    else                              include __DIR__ . "/CONTROLLERS/404.php";
?>