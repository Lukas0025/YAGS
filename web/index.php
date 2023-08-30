<?php
    include __DIR__ . "/wsos/autoload.php";
    include "DAL/user.php";

    $container = new \wsos\structs\container();
    $db        = new \wsos\database\drivers\inAppArray();
    $auth      = new \wsos\auth\basic\manager(DAL\user::class, "userName", "pass", "/login");

    //get current url
    $url = $_SERVER['REQUEST_URI'];

    // create Basic context
    $context = [
        "url" => $url,
        "menu_items" => [
            ["url" => "/",              "name" => "Dashboard"],
            ["url" => "/observations",  "name" => "Observations"],
            ["url" => "/stations",      "name" => "Stations"],
            ["url" => "/targets",       "name" => "Targets"],
            ["url" => "/modulations",   "name" => "Modulations"],
            ["url" => "/datatypes",     "name" => "Data Types"],
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

    if      ($url == "/")             include "CONTROLLERS/dashboard.php";
    else if ($url == "/observations") include "CONTROLLERS/observations.php";
    else if ($url == "/stations")     include "CONTROLLERS/stations.php";
    else                              include "CONTROLLERS/404.php";
?>