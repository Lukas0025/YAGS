<?php
    namespace API\station;

    function APIinfo($params) {

        $station  = new \DAL\station();

        $station->find("apiKey", $params["key"]);

        return ["id" => $station->id->get(), "name" => $station->name->get(), "locator" => $station->locator->get()];
    }

    function keys($params) {
        $stations  = new \wsos\database\core\table(\DAL\station::class);

        $res = [];
        foreach ($stations->getAll()->values as $station) {
            $res[] = ["name" => $station->name->get(), "key" => $station->apiKey->get()];
        }

        return $res;
    }