<?php
    namespace API\observation;

    function plan($params) {

        //first get trasmitter and receiver
        $receiver    = new \DAL\receiver(new \wsos\database\types\uuid($params["receiver"]));
        $transmitter = new \DAL\transmitter(new \wsos\database\types\uuid($params["transmitter"]));

        $receiver->fetch();
        $transmitter->fetch();

        $plan = new \DAL\observation();
        $plan->status     ->set("planed");
        $plan->locator    ->set($transmitter->target->get()->locator->get());
        $plan->transmitter->set($transmitter);
        $plan->receiver   ->set($receiver);
        $plan->start      ->set($params["start"]);
        $plan->end        ->set($params["end"]);

        if ($plan->start >= $plan->end) return ["status" => false];
        
        $plan->commit();

        return ["status" => true, "id" => $plan->id->get()];
    }

    function record($params) {
        //get GS and set last seen
        $station = new \DAL\station();
        if (!$station->find("apiKey", $params["key"])) return ["status" => "bad api key"];

        $station->lastSeen->now();
        $station->commit();

        //get all jobs for ground station
        $table  = new \wsos\database\core\table(\DAL\observation::class);
        
        $dummyObservation = new \DAL\observation();
        $dummyObservation->status->set("planed");
        //$dummyObservation->station->set($params["station"]);

        $planed = $table->query("(status == ?) && (receiver.station.id == ?)", [$dummyObservation->status->value, $station->id->get()]);
        
        $jobs = new \wsos\structs\vector();
        foreach ($planed->values as $plan) {
            $jobs->append([
                "id"      => $plan->id->get(),
                "target"  => [
                    "id"      => $plan->transmitter->get()->target->get()->id->get(),
                    "name"    => $plan->transmitter->get()->target->get()->name->get(),
                    "locator" => $plan->locator->get()
                ],

                "transmitter" => [
                    "centerFrequency" => $plan->transmitter->get()->centerFrequency->get(),
                    "bandwidth"       => $plan->transmitter->get()->bandwidth->get()
                ],

                "receiver" => [
                    "params" => $plan->receiver->get()->params->get()
                ],

                "proccessPipe" => $plan->transmitter->get()->processPipe->get()->pipe->get(),

                "start" => $plan->start->get(),
                "end"   => $plan->end->get()
            ]);
        }

        return $jobs->values;
    }

    function fail($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("fail");
        $obs->commit();
        
        return ["status" => true];
    }

    function assigned($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("assigned");
        $obs->commit();
        
        return ["status" => true];
    }

    function recording($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("recording");
        $obs->commit();
        
        return ["status" => true];
    }

    function recorded($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("recorded");
        $obs->commit();
        
        
        return ["status" => true];
    }

    function decoding($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("decoding");
        $obs->commit();
        
        return ["status" => true];
    }

    function success($params) {
		if (is_null($params["id"])) {
			return ["status" => "observation ID is not set"];
		}
		
        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        $obs->status->set("success");
        $obs->commit();
        
        return ["status" => true];
    }

    function addArtefacts($params) {
		
		if (is_null($params["fname"]) || is_null($params["id"])) {
			return ["status" => "file name or observation ID is not set"];
		}

        $adir = __DIR__ . "/../ARTEFACTS/" . $params["id"];

        $fname = basename($params["fname"]);

        mkdir($adir, 0777, true);

        // file pointer
        $ifp = fopen($adir . "/" . $fname, 'ab'); 

        fwrite($ifp, $params["data"]);
            
        // clean up the file resource
        fclose($ifp); 

        // chunk upload file
        if ($params["offset"] != 0) return;

        $obs = new \DAL\observation();
        $obs->id->set($params["id"]);
        $obs->fetch();

        //get current artefacts
        $artefacts = $obs->artefacts->get();

        $artefacts[] = "/ARTEFACTS/{$params['id']}/{$fname}";

        //done artefact save
        $obs->artefacts->set($artefacts);
        $obs->commit();
        
        return ["status" => true];
    }
