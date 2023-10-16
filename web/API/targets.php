<?php
    namespace API\target;

    function info($params) {

        $target  = new \DAL\target();
        $target->id->set($params["id"]);

        return [
            "id"       => $target->id->get(),
            "name"     => $target->name->get(),
            "locator"  => $target->locator->get()
        ];
    }

    function add($params) {

        $target  = new \DAL\target();
        
        $target->name->set($params["name"]);
        $target->type->set($params["type"]);
        $target->orbit->set($params["orbit"]);
        $target->locator->set($params["locator"]);

        $target->commit();

        return ["id" => $target->id->get()];
    }
