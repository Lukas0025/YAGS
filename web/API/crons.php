<?php
    namespace API\cron;

    function all($params) {
        tle($params);
        autoFail($params);
    }

    function tle($params) {
        // get all targets
        $targets = new \wsos\database\core\table(\DAL\target::class);

        $updated = [];
        foreach ($targets->getAll()->values as $target) {
            
            $locator = $target->locator->get();
            if (array_key_exists("tle", $locator)) {
                //get NORAD of objects
                $norad = explode(" ", $locator["tle"]["line2"])[1];

                //have norad now get data from celestrak
                //https://celestrak.org/NORAD/elements/gp.php?CATNR={norad}&FORMAT=tle

                $newTle = file_get_contents("https://celestrak.org/NORAD/elements/gp.php?CATNR={$norad}&FORMAT=tle");
                $newTle = explode("\n", $newTle);

                if (count($newTle) >= 3) { //tle loaded
                    $locator["tle"]["line1"] = str_replace("\r", "", $newTle[1]);
                    $locator["tle"]["line2"] = str_replace("\r", "", $newTle[2]);

                    $updated[] = ["name" => $target->name->get(), "norad" => $norad];

                    $target->locator->set($locator);
                    $target->commit();
                }
            }
        }

        return $updated;
    }

    function autoFail($params) {
        $observations = new \wsos\database\core\table(\DAL\observation::class);
        $ob           = new \DAL\observation();

        $faild = $observations->query("(status == ?) && (start < ?)", [$ob->status->getVal("assigned"), time() - 300]);

        foreach ($faild->values as $fob) {
            $fob->status->set("fail");
            $fob->commit(); 
        }

    }

    function setup($params) {
        $system = new \DAL\system();

        // detect if is seeded
        if (!$system->find("name", "seeds")) {
            //include seeds and seed DB
            include __DIR__ . "/../seeds.php";

            $params["plans"] = json_decode($params["plans"]);
            $params["params"] = json_decode($params["params"]);

            $params["lat"] = floatval($params["lat"]);
            $params["lon"] = floatval($params["lon"]);
            $params["alt"] = floatval($params["alt"]);

            $data = seed($params);
    
            $system->name->set("seeds");
            $system->value->set("true");
            $system->commit();

            //login as user
            $container = new \wsos\structs\container();
            $auth      = $container->get("auth");
            
            $auth->login($params["user"], $params["pass"]);

            return ["status" => true, "gsId" => $data["gs"], "apikey" => $data["apiKey"]];
        }
        
        return ["status" => false];
    }