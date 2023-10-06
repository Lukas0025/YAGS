<?php
    namespace API\station;

    function APIinfo($params) {

        $station  = new \DAL\station();

        $station->find("apiKey", $params["key"]);

        return [
            "id"       => $station->id->get(),
            "name"     => $station->name->get(),
            "locator"  => $station->locator->get()
        ];
    }

    function autoPlanable($params) {

        $receivers = new \wsos\database\core\table(\DAL\receiver::class);

        $res = [];
        foreach ($receivers->query("station.apiKey==?", [$params["key"]])->values as $receiver) {
            foreach ($receiver->autoPlan->get() as $targetId) {
                $transmitter = new \DAL\transmitter();
                $transmitter->id->set($targetId);
                $transmitter->fetch();

                //get locator and name and transmitter
                $res[] = [
                    "name"        => $transmitter->target->get()->name->get(),
                    "locator"     => $transmitter->target->get()->locator->get(),
                    "transmitter" => $transmitter->id->get(),
                    "receiver"    => $receiver->id->get(),
                    "priority"    => $transmitter->priority->get()
                ];
            }
        }

        return $res;
    }

    function keys($params) {
        $stations  = new \wsos\database\core\table(\DAL\station::class);

        $res = [];
        foreach ($stations->getAll()->values as $station) {
            $res[] = ["name" => $station->name->get(), "key" => $station->apiKey->get()];
        }

        return $res;
    }

    function add($params) {
        $stations  = new \wsos\database\core\table(\DAL\station::class);

        $myStation = new \DAL\station();
        $myStation->name->set($params["name"]);
        $myStation->description->set($params["description"]);
        $myStation->locator->set([
            "gps" => [
                "lat" => floatval($params["lat"]),
                "lon" => floatval($params["lon"]),
                "alt" => floatval($params["alt"])
            ] 
        ]);

        $myStation->commit();

        return ["id" => $myStation->id->get()];
    }