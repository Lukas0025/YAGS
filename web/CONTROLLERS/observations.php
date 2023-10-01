<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    $context["observations"] = (new \wsos\database\core\table(\DAL\observation::class))->query("", [], "DESC start")->values;
    $context["receivers"]    = new \wsos\database\core\table(\DAL\receiver::class);
    $context["transmitters"] = new \wsos\database\core\table(\DAL\transmitter::class);

    $templates->load("observations.html");    
    $templates->render($context);
    $templates->show();