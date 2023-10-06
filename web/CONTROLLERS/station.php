<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $router    = $container->get("router");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    //get station ID
    $stationId = $router->getArgs()[0];

    //get correct observation
    $context["station"] = new \DAL\station(new \wsos\database\types\uuid($stationId));
    $context["station"]->fetch();

    $observationsTable   = new \wsos\database\core\table(\DAL\observation::class);
    $ob                  = new \DAL\observation();

    $context["receivers"] = new \wsos\database\core\table(\DAL\receiver::class);
    $context["receivers"] = $context["receivers"]->query(
        "station.id == ?", [$stationId]
    )->values;

    $context["station"] = [
        "id"          => $context["station"]->id->get(),
        "name"        => $context["station"]->name->get(),
        "apiKey"      => $context["station"]->apiKey->get(),
        "lat"         => $context["station"]->locator->get()["gps"]["lat"],
        "lon"         => $context["station"]->locator->get()["gps"]["lon"],
        "alt"         => $context["station"]->locator->get()["gps"]["alt"],
        "lastSeen"    => $context["station"]->lastSeen->strDelta(),
        "description" => $context["station"]->description->get(),
        "success"     => $observationsTable->count("status==? && receiver.station.id == ?", [$ob->status->getVal("success"), $stationId]),
        "fail"        => $observationsTable->count("status==? && receiver.station.id == ?", [$ob->status->getVal("fail"), $stationId])
    ];

    $templates->load("station.html");    
    $templates->render($context);
    $templates->show();