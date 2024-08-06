<?php
    namespace API\uplink;

    function plan($params) {

        //first get trasmitter and receiver
        $transmitter = new \DAL\receiver(new \wsos\database\types\uuid($params["transmitter"]));
        $receiver    = new \DAL\transmitter(new \wsos\database\types\uuid($params["receiver"]));

        $receiver->fetch();
        $transmitter->fetch();

        $plan = new \DAL\uplink();
        $plan->status     ->set("planed");
        $plan->locator    ->set($receiver->target->get()->locator->get());
        $plan->transmitter->set($transmitter);
        $plan->receiver   ->set($receiver);
        $plan->start      ->set($params["start"]);
        $plan->data       ->set($params["data"]);
        $plan->delay      ->set($params["delay"]);
        
        $plan->commit();

        return ["status" => true, "id" => $plan->id->get()];
    }

    function forStation($params) {
        //get GS and set last seen
        $station = new \DAL\station();
        if (!$station->find("apiKey", $params["key"])) return ["status" => "bad api key"];

        $station->lastSeen->now();
        $station->commit();

        //get all jobs for ground station
        $table  = new \wsos\database\core\table(\DAL\uplink::class);
        
        $dummyObservation = new \DAL\uplink();
        $dummyObservation->status->set("planed");

        $planed = $table->query("(status == ?) && (transmitter.station.id == ?)", [$dummyObservation->status->value, $station->id->get()]);
        
        $jobs = new \wsos\structs\vector();
        foreach ($planed->values as $plan) {
            $jobs->append([
                "id"      => $plan->id->get(),
                "target"  => [
                    "id"      => $plan->receiver->get()->target->get()->id->get(),
                    "name"    => $plan->receiver->get()->target->get()->name->get(),
                    "locator" => $plan->locator->get()
                ],

                "receiver" => [
                    "centerFrequency" => $plan->receiver->get()->centerFrequency->get(),
                    "bandwidth"       => $plan->receiver->get()->bandwidth->get(),
                    "modulation"      => $plan->receiver->get()->modulation->get()->name->get(),
                    "sf"              => $plan->receiver->get()->sf->get(),
                    "codingRate"      => $plan->receiver->get()->codingRate->get(),
                    "syncWord"        => $plan->receiver->get()->syncWord->get(),
                    "preambleLength"  => $plan->receiver->get()->preambleLength->get()
                ],

                "transmitter" => [
                    "params" => $plan->transmitter->get()->params->get()
                ],

                "data" => $plan->data->get(),

                "start" => $plan->start->get(),
                "end"   => $plan->end->get(),

                "delay" => $plan->delay->get(),

                "startEpoch" => $plan->start->value,
                "endEpoch" => $plan->end->value
            ]);
        }

        return $jobs->values;
    }

    function fail($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\uplink();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->start->value = time();
        $obs->status->set("fail");
        $obs->commit();
        
        return ["status" => true];
    }

    function done($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\uplink();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->start->value = time();
        $obs->status->set("done");
        $obs->commit();
        
        return ["status" => true];
    }