<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    $context["uplinks"]      = (new \wsos\database\core\table(\DAL\uplink::class))->query("", [], "DESC start")->values;
    $context["transmitters"] = new \wsos\database\core\table(\DAL\receiver::class);
    $context["receivers"]    = new \wsos\database\core\table(\DAL\transmitter::class);

    $templates->load("uplinks.html");    
    $templates->render($context);
    $templates->show();