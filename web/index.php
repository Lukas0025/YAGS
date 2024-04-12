<?php
    include __DIR__ . "/wsos/wsos/autoload.php";

    foreach (glob("DAL/*.php") as $filename) {
        include_once $filename;
    }

    $container = new \wsos\structs\container();
    $db        = new \wsos\database\drivers\csv(__DIR__ . "/DB");
    $auth      = new \wsos\auth\basic\manager(DAL\user::class, "userName", "pass", "/login");

    //get current url
    $url = $_SERVER['REQUEST_URI'];

    $sites = [
        "sites" => [
            "observations" => ["controller" => __DIR__ . "/CONTROLLERS/observations.php", "name" => "Observations", "icon" => "/static/icons/telescope.svg",      "menu" => true],
            "targets"      => ["controller" => __DIR__ . "/CONTROLLERS/targets.php",      "name" => "Targets",      "icon" => "/static/icons/focus-2.svg",        "menu" => true],
            //"modulations"  => ["controller" => __DIR__ . "/CONTROLLERS/telemetry.php",    "name" => "Telemetry",    "icon" => "/static/icons/wave-sine.svg",      "menu" => true],
            /*
            "stations"     => ["controller" => __DIR__ . "/CONTROLLERS/stations.php",     "name" => "Stations",     "icon" => "/static/icons/radio.svg",          "menu" => true],
            "modulations"  => ["controller" => __DIR__ . "/CONTROLLERS/modulations.php",  "name" => "Modulations",  "icon" => "/static/icons/wave-sine.svg",      "menu" => true],
            "datatypes"    => ["controller" => __DIR__ . "/CONTROLLERS/datatypes.php",    "name" => "Data Types",   "icon" => "/static/icons/file-analytics.svg", "menu" => true],
            */
            "observation"  => ["controller" => __DIR__ . "/CONTROLLERS/observation.php",  "name" => "Observation view",                                           "menu" => false],
            "station"      => ["controller" => __DIR__ . "/CONTROLLERS/station.php",      "name" => "Station view",                                               "menu" => false],
            "target"       => ["controller" => __DIR__ . "/CONTROLLERS/target.php",       "name" => "Target",                                                     "menu" => false],
            "login"        => ["controller" => __DIR__ . "/CONTROLLERS/login.php",        "name" => "Login",                                                      "menu" => false],
            "api"          => ["controller" => __DIR__ . "/API/main.php",                 "name" => "api",                                                        "menu" => false],
        ],

        "controller" => __DIR__ . "/CONTROLLERS/dashboard.php",
        "name"       => "Dashboard",
        "icon"       => "/static/icons/dashboard.svg",
        "menu"       => true
    ];

    $router = new \wsos\router\basic\manager($sites);

    // create Basic context
    $context = [
        "url" => $url,
        "menu_items" => $router->getFlatMenu()->values,
        "logined" => $auth->getActive(),
        "appName" => "YAGS"
    ];

    // register containers
    $container->register("DBDriver", $db);
    $container->register("templateLoader", new wsos\templates\loader(__DIR__ . "/VIEWS"));
    $container->register("context", $context);
    $container->register("auth", $auth);
    $container->register("router", $router);

    $system = new \DAL\system();

    // detect if is seeded
    if (!$system->find("name", "seeds")) {
        include "seeds.php";

        $system->name->set("seeds");
        $system->value->set("true");
        $system->commit();
    }

    $router->route($url);
?>