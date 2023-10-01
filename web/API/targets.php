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
