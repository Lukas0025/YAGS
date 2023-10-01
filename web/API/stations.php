<?php
    namespace API\station;

    function APIinfo($params) {

        $station  = new \DAL\station()

        $station->find("apiKey", $params["key"])

        return ["id" => $station->id->get(), "name" => $station->name->get(), "locator" => $station->locator->get()];
    }