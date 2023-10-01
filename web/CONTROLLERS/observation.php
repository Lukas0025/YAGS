<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $router    = $container->get("router");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    //get observation ID
    $obId = $router->getArgs()[0];

    //$obId = new \DAL\observation();
    //$obId->find("status", 4);
    //$obId = $obId->id;

    //get correct observation
    $context["observation"] = new \DAL\observation(new \wsos\database\types\uuid($obId));
    $context["observation"]->fetch();

    //generate artefacts
    $context["artefacts"] = new \wsos\structs\vector();

    //get observations whats from same satellite and in 24h interval
    $context["observations"] = new \wsos\database\core\table(\DAL\observation::class);
    $context["observations"] = $context["observations"]->query(
        "(transmitter.target.id == ?) && (start > ?) && (end < ?)",
        [
            $context["observation"]->transmitter->get()->target->get()->id->get(),
            $context["observation"]->start->value - (60 * 60 * 12), 
            $context["observation"]->end->value   + (60 * 60 * 12)
        ],
        "DESC start"
    )->values;

    foreach ($context["observation"]->artefacts->get() as $art) {
        $context["artefacts"]->append([
            "name" => basename($art),
            "url"  => $art
        ]);
    }

    $context["artefacts"] = $context["artefacts"]->values;

    $templates->load("observation.html");    
    $templates->render($context);
    $templates->show();