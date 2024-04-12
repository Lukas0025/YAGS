<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $router    = $container->get("router");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    //get target ID
    $targetID = $router->getArgs()[0];

    //get correct observation
    $context["target"] = new \DAL\target(new \wsos\database\types\uuid($targetID));
    $context["target"]->fetch();

    $observationsTable   = new \wsos\database\core\table(\DAL\observation::class);
    $dummy_ob            = new \DAL\observation();

    $context["transmitters"] = new \wsos\database\core\table(\DAL\transmitter::class);
    $context["transmitters"] = $context["transmitters"]->query(
        "target.id == ?", [$targetID]
    )->values;

    $locatorsKey = array_keys($context["target"]->locator->get());

    $last = (new \wsos\database\core\table(\DAL\observation::class))->query("(transmitter.target.id == ?) && (status == ?)", [$targetID, $dummy_ob->status->getVal("success")], "DESC end", 1);
    $last = $last->len() > 0 ? $last->values[0]->end->strDelta() . " ago" : "never";

    $context["target"] = [
        "id"           => $context["target"]->id->get(),
        "name"         => $context["target"]->name->get(),
        "locatorsKey"  => count($locatorsKey) > 0 ? $locatorsKey : ["none"],
        "locators"     => $context["target"]->locator->get(),
        "type"         => $context["target"]->type->get()->name->get(),
        "description"  => $context["target"]->description->get(),
        "orbit"        => $context["target"]->orbit->get(),
        "transmitters" => count($context["transmitters"]),
        "lastObservation" => $last,
        "success"      => $observationsTable->count("(status==?) && (transmitter.target.id == ?)", [$dummy_ob ->status->getVal("success"), $targetID]),
        "fail"         => $observationsTable->count("(status==?) && (transmitter.target.id == ?)", [$dummy_ob ->status->getVal("fail"),    $targetID])
    ];

    $context["pipes"]       = new \wsos\database\core\table(\DAL\processPipe::class);
    $context["dataTypes"]   = new \wsos\database\core\table(\DAL\dataType::class);
    $context["modulations"] = new \wsos\database\core\table(\DAL\modulation::class);
    $context["antennas"]    = new \wsos\database\core\table(\DAL\antenna::class);

    $templates->load("target.html");    
    $templates->render($context);
    $templates->show();
