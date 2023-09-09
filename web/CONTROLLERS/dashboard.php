<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context"); 
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    $context["successCount"]      = 75;
    $context["planedCount"]       = 15;
    $context["lastPlaned"]        = 2;
    $context["failCount"]         = 5;
    $context["observationsCount"] = 5;
    
    // create planed template observations
    $planed = new \DAL\observation();
    $planed->status->set("planed");

    $observationsTable = new \wsos\database\core\table(\DAL\observation::class);
    $planedTable       = $observationsTable->query("status=?", [$planed->status->value]);

    $observationsLocators = new \wsos\structs\vector();
    foreach($planedTable->values as $obs) {
        $observationsLocators->append($obs->locator->get());
    }

    $context["planedLocators"] = json_encode($observationsLocators->values);

    $templates->load("dashboard.html");    
    $templates->render($context);
    $templates->show();