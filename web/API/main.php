<?php
    $container = new \wsos\structs\container();
    $api       = new \wsos\api\functional\manager("API");

    $templates = $container->get("templateLoader");
    $context   = $container->get("context"); 
    $auth      = $container->get("auth");
    $router    = $container->get("router");

    // to show this page user must be logined
    //$auth->requireLogin();

    //register API functions
    include_once(__DIR__ . "/observations.php");
    include_once(__DIR__ . "/stations.php");

    //init API
    $api->serve($router->getArgs());