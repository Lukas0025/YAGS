<?php
    $container = new \wsos\structs\container();

    $templates = $container->get("templateLoader");
    $context   = $container->get("context"); 
    $auth      = $container->get("auth");

    // to show this page user must be logined
    $auth->requireLogin();

    // create planed template observations
    $ob = new \DAL\observation();

    /**
     * Get the directory size
     * @param  string $directory
     * @return integer
     */
    function foldersize($path, $extension = null) {
        $total_size = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/'). '/';

        foreach($files as $t) {
            if ($t <> "." && $t <> "..") {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = foldersize($currentFile, $extension);
                    $total_size += $size;
                } else {
                    if (is_null($extension) || (strtolower(pathinfo($currentFile)['extension']) == $extension)) {
                        $size = filesize($currentFile);
                        $total_size += $size;
                    }
                }
            }   
        }

        return $total_size;
    }

    $observationsTable = new \wsos\database\core\table(\DAL\observation::class);
    $maxSize = 8000; //8GB

    $context["artefactsSpace"]    = $maxSize;
   
    /**
     * For carts
     */
    $context["successCount"]      = $observationsTable->count("status==?", [$ob->status->getVal("success")]);
    $context["planedCount"]       = $observationsTable->count("status==?", [$ob->status->getVal("planed")]);
    $context["lastPlaned"]        = $observationsTable->count("", []);
    $context["failCount"]         = $observationsTable->count("status==?", [$ob->status->getVal("fail")]);
    $context["observationsCount"] = $observationsTable->count("", []);
    
    /**
     * Get used size
     */
    $context["usedSize"]          = round(foldersize(__DIR__ . "/../ARTEFACTS/")        / 1000000);
    $context["imagesSize"]        = round(foldersize(__DIR__ . "/../ARTEFACTS/", "png") / 1000000);
    $context["basebandSize"]      = round(foldersize(__DIR__ . "/../ARTEFACTS/", "s8")  / 1000000);
    $context["otherSize"]         = $context["usedSize"] - $context["imagesSize"] + $context["basebandSize"];
    $context["freeSize"]          = $context["artefactsSpace"] - $context["usedSize"];

    /**
     * Get observations
     */
    $observationTable             = new \wsos\database\core\table(\DAL\observation::class); 
    $context["observations"]      = $observationTable->query("", [], "DESC start", 10)->values;

    /**
     * Get stattions
     */
    $context["stations"]          = [];
    $stations                     = (new \wsos\database\core\table(\DAL\station::class))->getAll()->values;
    foreach ($stations as $station) {
        $context["stations"][] = [
            "id"           => $station->id->get(),
            "name"         => $station->name->get(),
            "observations" => $observationTable->count("receiver.station.id == ?", [$station->id->get()]),
            "lastSeen"     => $station->lastSeen->strDelta()
        ];
    }

    /**
     * Get planed observations
     */
    $planedTable          = $observationsTable->query("(status == ?) || (status == ?)", [$ob->status->getVal("assigned"), $ob->status->getVal("planed")]);

    $observationsLocators = new \wsos\structs\vector();
    foreach($planedTable->values as $obs) {
        $locator          = $obs->locator->get();
        $locator["start"] = $obs->start->get() . " UTC"; 
        $locator["end"]   = $obs->end->get()   . " UTC"; 

        $observationsLocators->append($locator);
    }

    $context["planedObservations"] = json_encode($observationsLocators->values);

    /**
     * Render view
     */
    $templates->load("dashboard.html");    
    $templates->render($context);
    $templates->show();