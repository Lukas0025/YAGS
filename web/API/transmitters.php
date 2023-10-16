<?php
    namespace API\transmitter;

    function get($params) {

        $transmitter  = new \DAL\transmitter();
        $transmitter->id->set($params["id"]);

        $transmitter->fetch();

        return [
            "id"         => $transmitter->id->get(),
            "freq"       => $transmitter->centerFrequency->get(),
            "band"       => $transmitter->bandwidth->get(),
            "priority"   => $transmitter->priority->get(),
            "pipe"       => $transmitter->processPipe->get()->id->get(),
            "dataType"   => $transmitter->dataType->get()->id->get(),
            "modulation" => $transmitter->modulation->get()->id->get(),
            "antenna"    => $transmitter->antenna->get()->id->get(),
            "target"     => $transmitter->target->get()->id->get(),
        ];
    }

    function save($params) {

        $transmitter  = new \DAL\transmitter();

        if ($params["id"] <> "null") {
            $transmitter->id->set($params["id"]);

            // try fetch from DB?
            $transmitter->fetch();
        }

        $transmitter->centerFrequency->set($params["freq"]);
        $transmitter->bandwidth->set($params["band"]);
        $transmitter->priority->set($params["priority"]);
        $transmitter->processPipe->set($params["pipe"]);
        $transmitter->dataType->set($params["dataType"]);
        $transmitter->modulation->set($params["modulation"]);
        $transmitter->antenna->set($params["antenna"]);
        $transmitter->target->set($params["target"]);

        $transmitter->commit();

        return [
            "id" => $transmitter->id->get()
        ];
    }