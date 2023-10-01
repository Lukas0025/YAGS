<?php
    namespace API\cron;

    function all($params) {

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