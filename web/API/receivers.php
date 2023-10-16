<?php
    namespace API\receiver;

    function get($params) {

        $receiver  = new \DAL\receiver();
        $receiver->id->set($params["id"]);

        $receiver->fetch();

        /**
         * Create array of autoPlan with names
         */

        $autoplan = new \wsos\structs\vector();
        foreach ($receiver->autoPlan->get() as $transmitterId) {
            $transmitter = new \DAL\transmitter();
            $transmitter->id->set($transmitterId);
            $transmitter->fetch();

            $autoplan->append([
                "id"         => $transmitter->id->get(),
                "target"     => $transmitter->target->get()->name->get(),
                "modulation" => $transmitter->modulation->get()->name->get(),
                "dataType"   => $transmitter->dataType->get()->name->get(),
                "freq"       => $transmitter->centerFrequency->get(),
            ]);
        }

        return [
            "id"         => $receiver->id->get(),
            "freq"       => $receiver->centerFrequency->get(),
            "band"       => $receiver->bandwidth->get(),
            "params"     => $receiver->params->get(),
            "gain"       => $receiver->gain->get(),
            "autoPlan"   => $autoplan,
            "antenna"    => $receiver->antenna->get()->id->get(),
            "station"    => $receiver->station->get()->id->get()
        ];
    }

    function save($params) {

        $receiver  = new \DAL\receiver();

        if ($params["id"] <> "null") {
            $receiver->id->set($params["id"]);

            // try fetch from DB?
            $receiver->fetch();
        }

        $receiver->centerFrequency->set($params["freq"]);
        $receiver->bandwidth->set($params["band"]);
        $receiver->gain->set($params["gain"]);
        $receiver->params->set(json_decode($params["params"]));
        $receiver->autoPlan->set(json_decode($params["autoPlan"]));
        $receiver->station->set($params["station"]);
        $receiver->antenna->set($params["antenna"]);
        

        $receiver->commit();

        return [
            "id" => $receiver->id->get()
        ];
    }