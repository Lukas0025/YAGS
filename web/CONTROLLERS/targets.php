<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context");
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    $context["targets"] = new \wsos\structs\vector();

    $targets = (new \wsos\database\core\table(\DAL\target::class))->getAll();

    // create planed template observations
    $ob = new \DAL\observation();

    foreach ($targets->values as $target) {

        $last = (new \wsos\database\core\table(\DAL\observation::class))->query("transmitter.target.id == ? && status == ?", [$target->id->get(), $ob->status->getVal("success")], "DESC end", 1);
        $last = $last->len() > 0 ? "ago " . $last->values[0]->end->strDelta() : "never";

        $observations = (new \wsos\database\core\table(\DAL\observation::class))->count("transmitter.target.id == ?", [$target->id->get()]);

        $context["targets"]->append([
            "id"    => $target->id->get(),
            "name"  => $target->name->get(),
            "orbit" => $target->orbit->get(),
            "type"  => $target->type->get()->name->get(),
            "last"  => $last,
            "observations" => $observations
        ]);
    }

    $context["targets"] = $context["targets"]->values;


    $templates->load("targets.html");    
    $templates->render($context);
    $templates->show();