<?php
    function seed($data) {
        /**
         * Create default user
         */
        $admin = new \DAL\user();
        $admin->userName->set($data["user"]);
        $admin->pass->set($data["pass"]);
        $admin->admin->set(true);
        $admin->commit(); /* commit changes to DB */

        $leoWSatTape = new \DAL\targetType();
        $leoWSatTape->name->set("Weather Satellite");
        $leoWSatTape->commit();

        $nanosatelliteType = new \DAL\targetType();
        $nanosatelliteType->name->set("Nanosatellite");
        $nanosatelliteType->commit();

        $avhrrType = new \DAL\dataType();
        $avhrrType->name->set("AVHRR");
        $avhrrType->commit();

        $msumrType = new \DAL\dataType();
        $msumrType->name->set("MSU-MR");
        $msumrType->commit();

        $beaconType = new \DAL\dataType();
        $beaconType->name->set("Beacon");
        $beaconType->commit();

        $telemetryType = new \DAL\dataType();
        $telemetryType->name->set("Telemetry");
        $telemetryType->commit();

        /**
         * Antennas seeds
         */

        $yagi = new \DAL\Antenna();
        $yagi->name->set("YAGI");
        $yagi->commit();

        $dish = new \DAL\Antenna();
        $dish->name->set("DISH");
        $dish->commit();

        $qfh = new \DAL\Antenna();
        $qfh->name->set("QFH");
        $qfh->commit();

        $dipole = new \DAL\Antenna();
        $dipole->name->set("Dipole");
        $dipole->commit();

        $noneAnt = new \DAL\Antenna();
        $noneAnt->name->set("none");
        $noneAnt->commit();


        /**
         * First station
         */
        $myStation = new \DAL\station();
        $myStation->name->set("My first station");
        $myStation->locator->set([
            "gps" => [
                "lat" => $data["lat"],
                "lon" => $data["lon"],
                "alt" => $data["alt"]
            ] 
        ]);
        $myStation->commit();

        $myStationRcv = new \DAL\receiver();
        $myStationRcv->station->set($myStation);
        $myStationRcv->params->set($data["params"]);
        $myStationRcv->antenna->set($noneAnt);
        $myStationRcv->centerFrequency->set(0);
        $myStationRcv->gain->set(0);
        $myStationRcv->commit();

        /*
        $myStation2G = new \DAL\receiver();
        $myStation2G->station->set($myStation);
        $myStation2G->params->set([
            "radio"    => "hackrf",
            "amp"      => true,
            "bias"     => false,
            "lna_gain" => 45,
            "vga_gain" => 10,
            "bias"     => true
        ]);
        $myStation2G->antenna->set($yagi);
        $myStation2G->centerFrequency->set(1700000000);
        $myStation2G->gain->set(45);
        $myStation2G->commit();
        */

        /**
         * Modulations
         */

        $apt = new \DAL\modulation();
        $apt->name->set("APT");
        $apt->commit();

        $bpsk = new \DAL\modulation();
        $bpsk->name->set("BPSK");
        $bpsk->commit();

        $gfsk = new \DAL\modulation();
        $gfsk->name->set("GFSK");
        $gfsk->commit();

        $hrpt = new \DAL\modulation();
        $hrpt->name->set("HRPT");
        $hrpt->commit();

        $dsb = new \DAL\modulation();
        $dsb->name->set("DSB");
        $dsb->commit();

        $lrpt = new \DAL\modulation();
        $lrpt->name->set("LRPT");
        $lrpt->commit();

        $cw = new \DAL\modulation();
        $cw->name->set("CW");
        $cw->commit();

        $lora = new \DAL\modulation();
        $lora->name->set("LORA");
        $lora->commit();

        /**
         * Process pipes
         */
        $aptPipe = new \DAL\processPipe();
        $aptPipe->name->set("NOAA APT");
        $aptPipe->pipe->set([
            "satdump noaa_apt baseband {baseband} {artefactDir} --samplerate {fs} --satellite_number {targetNum} --start_timestamp {start} --autocrop_wedges --baseband_format s8",
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8"
        ]);

        $aptPipe->commit();

        $lrptPipe = new \DAL\processPipe();
        $lrptPipe->name->set("METEOR LRPT");
        $lrptPipe->pipe->set([
            "satdump meteor_m2-x_lrpt baseband {baseband} {artefactDir} --samplerate {fs} --baseband_format s8",
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8"
        ]);

        $lrptPipe->commit();

        $spectogramPipe = new \DAL\processPipe();
        $spectogramPipe->name->set("Spectogram");
        $spectogramPipe->pipe->set([
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8",
        ]);

        $spectogramPipe->commit();

        $cwPipe = new \DAL\processPipe();
        $cwPipe->name->set("CW Morse");
        $cwPipe->pipe->set([
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "cw_morse.py {baseband} {artefactDir}/morse.txt -fs {fs} -fc \"[?]\"",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8"
        ]);

        $cwPipe->commit();

        $luckyPipe = new \DAL\processPipe();
        $luckyPipe->name->set("Lucky7-UHF");
        $luckyPipe->pipe->set([
            "satdump lucky7_link baseband {baseband} {artefactDir} --samplerate {fs} --dc_block --start_timestamp {start} --enable_doppler --baseband_format s8",
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "/decoders/lucky7 {artefactDir}/lucky7_link.frm {start} > {artefactDir}/telemetry.json",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8",
            "test -f {artefactDir}/lucky7_link.frm"
        ]);

        $luckyPipe->commit();

        $stratosatPipe = new \DAL\processPipe();
        $stratosatPipe->name->set("Stratosat_TK1-UHF");
        $stratosatPipe->pipe->set([
            "satdump stratosat_tk1_uhf baseband {baseband} {artefactDir} --samplerate {fs} --dc_block --start_timestamp {start} --enable_doppler --baseband_format s8",
            "baseband_spectogram.py {baseband} {artefactDir}/spectogram.png -fs {fs} -fc {freq}",
            "cp {baseband} {artefactDir}/{dtstart}_{fs}SPS_{freq}Hz.s8"
        ]);

        $stratosatPipe->commit();

        /**
         * NOAA 19
         */
        $noaa19 = new \DAL\target();
        $noaa19->name->set("NOAA 19");
        $noaa19->type->set($leoWSatTape);
        $noaa19->orbit->set("leo");
        $noaa19->description->set("NOAA 19 is the fifth in a series of five Polar-orbiting Operational Environmental Satellites (POES) with advanced microwave sounding instruments that provide imaging and sounding capabilities.");
        $noaa19->locator->set([
            "tle" => [
                "line1" => "1 33591U 09005A   23243.18101660  .00000207  00000-0  13587-3 0  9998",
                "line2" => "2 33591  99.0938 290.2850 0014342  35.8617 324.3514 14.12812127750532"
            ]
        ]);
        $noaa19->commit();

        $noaa19APT = new \DAL\transmitter();
        $noaa19APT->target->set($noaa19);
        $noaa19APT->dataType->set($avhrrType);
        $noaa19APT->bandwidth->set(34000);
        $noaa19APT->centerFrequency->set(137100000);
        $noaa19APT->modulation->set($apt);
        $noaa19APT->antenna->set($qfh);
        $noaa19APT->priority->set(2);
        $noaa19APT->processPipe->set($aptPipe);
        $noaa19APT->commit();

        $noaa19DSB = new \DAL\transmitter();
        $noaa19DSB->target->set($noaa19);
        $noaa19DSB->dataType->set($avhrrType);
        $noaa19DSB->bandwidth->set(34000);
        $noaa19DSB->centerFrequency->set(137770000);
        $noaa19DSB->modulation->set($dsb);
        $noaa19DSB->antenna->set($qfh);
        $noaa19DSB->commit();

        $noaa19HRPT = new \DAL\transmitter();
        $noaa19HRPT->target->set($noaa19);
        $noaa19HRPT->dataType->set($avhrrType);
        $noaa19HRPT->bandwidth->set(3000000);
        $noaa19HRPT->centerFrequency->set(1698000000);
        $noaa19HRPT->modulation->set($hrpt);
        $noaa19HRPT->antenna->set($qfh);
        $noaa19HRPT->commit();

        /**
         * NOAA 18
         */
        $noaa18 = new \DAL\target();
        $noaa18->name->set("NOAA 18");
        $noaa18->type->set($leoWSatTape);
        $noaa18->orbit->set("leo");
        $noaa18->description->set("NOAA 18, known before launch as NOAA-N, is a weather forecasting satellite run by NOAA. NOAA-N (18) was launched into a sun-synchronous orbit at an altitude of 854 km above the Earth, with an orbital period of 102 minutes. It hosts the AMSU-A, MHS, AVHRR, Space Environment Monitor SEM/2 instrument and High Resolution Infrared Radiation Sounder (HIRS) instruments, as well as the SBUV/2 ozone-monitoring instrument.");
        $noaa18->locator->set([
            "tle" => [
                "line1" => "1 28654U 05018A   23250.34978256  .00000271  00000-0  16921-3 0  9997",
                "line2" => "2 28654  98.9045 324.3258 0015006 144.5825 215.6347 14.13000434943172"
            ]
        ]);
        $noaa18->commit();

        $noaa18APT = new \DAL\transmitter();
        $noaa18APT->target->set($noaa18);
        $noaa18APT->dataType->set($avhrrType);
        $noaa18APT->bandwidth->set(34000);
        $noaa18APT->centerFrequency->set(137912500);
        $noaa18APT->modulation->set($apt);
        $noaa18APT->antenna->set($qfh);
        $noaa18APT->priority->set(1);
        $noaa18APT->processPipe->set($aptPipe);
        $noaa18APT->commit();

        $noaa18DSB = new \DAL\transmitter();
        $noaa18DSB->target->set($noaa18);
        $noaa18DSB->dataType->set($avhrrType);
        $noaa18DSB->bandwidth->set(34000);
        $noaa18DSB->centerFrequency->set(137350000);
        $noaa18DSB->modulation->set($dsb);
        $noaa18DSB->antenna->set($qfh);
        $noaa18DSB->commit();

        $noaa18HRPT = new \DAL\transmitter();
        $noaa18HRPT->target->set($noaa18);
        $noaa18HRPT->dataType->set($avhrrType);
        $noaa18HRPT->bandwidth->set(3000000);
        $noaa18HRPT->centerFrequency->set(1707000000);
        $noaa18HRPT->modulation->set($hrpt);
        $noaa18HRPT->antenna->set($qfh);
        $noaa18HRPT->commit();

        /**
         * NOAA 15
         */
        $noaa15 = new \DAL\target();
        $noaa15->name->set("NOAA 15");
        $noaa15->type->set($leoWSatTape);
        $noaa15->orbit->set("leo");
        $noaa15->description->set("");
        $noaa15->locator->set([
            "tle" => [
                "line1" => "1 25338U 98030A   23256.05271705  .00000271  00000-0  13068-3 0  9997",
                "line2" => "2 25338  98.6015 283.5892 0011307  61.1013 299.1300 14.26387191317785"
            ]
        ]);
        $noaa15->commit();

        $noaa15APT = new \DAL\transmitter();
        $noaa15APT->target->set($noaa15);
        $noaa15APT->dataType->set($avhrrType);
        $noaa15APT->bandwidth->set(34000);
        $noaa15APT->centerFrequency->set(137620000);
        $noaa15APT->modulation->set($apt);
        $noaa15APT->antenna->set($qfh);
        $noaa15APT->priority->set(100);
        $noaa15APT->processPipe->set($aptPipe);
        $noaa15APT->commit();

        $noaa15DSB = new \DAL\transmitter();
        $noaa15DSB->target->set($noaa15);
        $noaa15DSB->dataType->set($avhrrType);
        $noaa15DSB->bandwidth->set(34000);
        $noaa15DSB->centerFrequency->set(1377700000);
        $noaa15DSB->modulation->set($dsb);
        $noaa15DSB->antenna->set($qfh);
        $noaa15DSB->commit();

        $noaa15HRPT = new \DAL\transmitter();
        $noaa15HRPT->target->set($noaa15);
        $noaa15HRPT->dataType->set($avhrrType);
        $noaa15HRPT->bandwidth->set(3000000);
        $noaa15HRPT->centerFrequency->set(1702500000);
        $noaa15HRPT->modulation->set($hrpt);
        $noaa15HRPT->antenna->set($qfh);
        $noaa15HRPT->commit();

        /**
         * METEOR M2-3
         */

        $meteor23 = new \DAL\target();
        $meteor23->name->set("METEOR M2-3");
        $meteor23->type->set($leoWSatTape);
        $meteor23->orbit->set("leo");
        $meteor23->description->set("");
        $meteor23->locator->set([
            "tle" => [
                "line1" => "1 57166U 23091A   23258.09139909  .00000046  00000-0  39414-4 0  9995",
                "line2" => "2 57166  98.7568 309.6443 0003662 194.1864 165.9212 14.23842173 11338"
            ]
        ]);
        $meteor23->commit();

        $meteor23LRPT1 = new \DAL\transmitter();
        $meteor23LRPT1->target->set($meteor23);
        $meteor23LRPT1->dataType->set($msumrType);
        $meteor23LRPT1->bandwidth->set(120000);
        $meteor23LRPT1->centerFrequency->set(137900000);
        $meteor23LRPT1->modulation->set($lrpt);
        $meteor23LRPT1->antenna->set($qfh);
        $meteor23LRPT1->priority->set(3);
        $meteor23LRPT1->processPipe->set($lrptPipe);
        $meteor23LRPT1->commit();

        $meteor23LRPT2 = new \DAL\transmitter();
        $meteor23LRPT2->target->set($meteor23);
        $meteor23LRPT2->dataType->set($msumrType);
        $meteor23LRPT2->bandwidth->set(120000);
        $meteor23LRPT2->centerFrequency->set(137100000);
        $meteor23LRPT2->modulation->set($lrpt);
        $meteor23LRPT2->antenna->set($qfh);
        $meteor23LRPT2->processPipe->set($lrptPipe);
        $meteor23LRPT2->commit();

        $meteor23HRPT = new \DAL\transmitter();
        $meteor23HRPT->target->set($meteor23);
        $meteor23HRPT->dataType->set($msumrType);
        $meteor23HRPT->bandwidth->set(3000000);
        $meteor23HRPT->centerFrequency->set(1700000000);
        $meteor23HRPT->modulation->set($hrpt);
        $meteor23HRPT->antenna->set($qfh);
        $meteor23HRPT->commit();

        /**
         * METEOR M2-4
         */

         $meteor24 = new \DAL\target();
         $meteor24->name->set("METEOR M2-4");
         $meteor24->type->set($leoWSatTape);
         $meteor24->orbit->set("leo");
         $meteor24->description->set("");
         $meteor24->locator->set([
             "tle" => [
                 "line1" => "1 57166U 23091A   23258.09139909  .00000046  00000-0  39414-4 0  9995",
                 "line2" => "2 57166  98.7568 309.6443 0003662 194.1864 165.9212 14.23842173 11338"
             ]
         ]);
         $meteor24->commit();
 
         $meteor24LRPT1 = new \DAL\transmitter();
         $meteor24LRPT1->target->set($meteor23);
         $meteor24LRPT1->dataType->set($msumrType);
         $meteor24LRPT1->bandwidth->set(120000);
         $meteor24LRPT1->centerFrequency->set(137900000);
         $meteor24LRPT1->modulation->set($lrpt);
         $meteor24LRPT1->antenna->set($qfh);
         $meteor24LRPT1->priority->set(3);
         $meteor24LRPT1->processPipe->set($lrptPipe);
         $meteor24LRPT1->commit();
 
         $meteor24LRPT2 = new \DAL\transmitter();
         $meteor24LRPT2->target->set($meteor23);
         $meteor24LRPT2->dataType->set($msumrType);
         $meteor24LRPT2->bandwidth->set(120000);
         $meteor24LRPT2->centerFrequency->set(137100000);
         $meteor24LRPT2->modulation->set($lrpt);
         $meteor24LRPT2->antenna->set($qfh);
         $meteor24LRPT2->processPipe->set($lrptPipe);
         $meteor24LRPT2->commit();
 
         $meteor24HRPT = new \DAL\transmitter();
         $meteor24HRPT->target->set($meteor23);
         $meteor24HRPT->dataType->set($msumrType);
         $meteor24HRPT->bandwidth->set(3000000);
         $meteor24HRPT->centerFrequency->set(1700000000);
         $meteor24HRPT->modulation->set($hrpt);
         $meteor24HRPT->antenna->set($qfh);
         $meteor24HRPT->commit();

        /**
         * Max valier sat
         */

        $maxvalier = new \DAL\target();
        $maxvalier->name->set("MAX VALIER SAT");
        $maxvalier->type->set($nanosatelliteType);
        $maxvalier->orbit->set("leo");
        $maxvalier->description->set("");
        $maxvalier->locator->set([
            "tle" => [
                "line1" => "1 42778U 17036P   23282.84620820  .00050788  00000-0  10567-2 0  9991",
                "line2" => "2 42778  97.1421 315.3778 0008233  57.6254 302.5791 15.45432755350391"
            ]
        ]);
        $maxvalier->commit();

        $maxvalierCW = new \DAL\transmitter();
        $maxvalierCW->target->set($maxvalier);
        $maxvalierCW->dataType->set($beaconType);
        $maxvalierCW->bandwidth->set(120000);
        $maxvalierCW->centerFrequency->set(145960000);
        $maxvalierCW->modulation->set($cw);
        $maxvalierCW->antenna->set($yagi);
        $maxvalierCW->priority->set(0);
        $maxvalierCW->processPipe->set($cwPipe);
        $maxvalierCW->commit();

        /**
         * Lucky 7
         */

        $lucky7 = new \DAL\target();
        $lucky7->name->set("Lucky 7");
        $lucky7->type->set($nanosatelliteType);
        $lucky7->orbit->set("sso");
        $lucky7->description->set("It is a single unit CubeSat with a size of 112×112×113.5 mm, to be easily traceable in space, available by launch cost and compatible with a variety of launch opportunities. Its aim is to test everyday electronics tweaked for deep space or long-lasting missions such as to the Moon, Mars, and beyond. As their lucky number is seven and it is supposed to be the seventh Czech made satellite in a row, they called it simply Lucky-7.");
        $lucky7->locator->set([
            "tle" => [
                "line1" => "1 44406U 19038W   24094.95219425  .00026482  00000-0  71098-3 0  9995",
                "line2" => "2 44406  97.7044  89.1200 0014055   7.8960 352.2502 15.37821998262930"
            ]
        ]);
        $lucky7->commit();

        $lucky7Telem = new \DAL\transmitter();
        $lucky7Telem->target->set($lucky7);
        $lucky7Telem->dataType->set($telemetryType);
        $lucky7Telem->bandwidth->set(120000);
        $lucky7Telem->centerFrequency->set(437525000);
        $lucky7Telem->modulation->set($gfsk);
        $lucky7Telem->antenna->set($dipole);
        $lucky7Telem->priority->set(0);
        $lucky7Telem->processPipe->set($luckyPipe);
        $lucky7Telem->commit();

        /**
         * Stratosat TK-1
         */

        $stratosattk = new \DAL\target();
        $stratosattk->name->set("StratoSat TK-1");
        $stratosattk->type->set($nanosatelliteType);
        $stratosattk->orbit->set("sso");
        $stratosattk->description->set("3U Cubesat StratoSat TK-1 is a Stratonautica satellite, created on the basis of a shortened satellite platform Geoscan 3U. One of the three units is a transport container for the delivery of 6 pocketcubes into low Earth orbit (StratoSat TK-1-A, StratoSat TK-1-B, StratoSat TK-1-C, StratoSat TK-1-D, StratoSat TK-1-E, StratoSat TK-1-F). These pico-satellites are intended for educational programs for high school students. Most pocketcubes were made by high school or university students. Each pocketcube operates on an independent power supply system and has radio transmitters and cameras on board. The process of the exit of the satellites from the transport container will be filmed using high-resolution photo and video cameras that are installed on the middle unit. All imagery data taken during the mission will be transmitted to amateurs around the world using open radio-protocol.");
        $stratosattk->locator->set([
            "tle" => [
                "line1" => "1 57167U 23091B   24146.85238156  .00015568  00000+0  91391-3 0  9996",
                "line2" => "2 57167  97.6120 199.9936 0015849 190.6793 169.4102 15.11710813 50186"
            ]
        ]);
        $stratosattk->commit();

        $stratosattkTelem = new \DAL\transmitter();
        $stratosattkTelem->target->set($stratosattk);
        $stratosattkTelem->dataType->set($telemetryType);
        $stratosattkTelem->bandwidth->set(120000);
        $stratosattkTelem->centerFrequency->set(435870000);
        $stratosattkTelem->modulation->set($gfsk);
        $stratosattkTelem->antenna->set($dipole);
        $stratosattkTelem->priority->set(0);
        $stratosattkTelem->processPipe->set($stratosatPipe);
        $stratosattkTelem->commit();

        /**
         * Set autoplans
         */
        $autoplan = [];

        foreach ($data["plans"] as $target) {
            if ($target == "noaa19apt") $autoplan[] = $noaa19APT->id->get();
            if ($target == "noaa18apt") $autoplan[] = $noaa18APT->id->get();
            if ($target == "noaa15apt") $autoplan[] = $noaa15APT->id->get();
            if ($target == "meteorm23") $autoplan[] = $meteor23LRPT1->id->get();
            if ($target == "meteorm24") $autoplan[] = $meteor24LRPT1->id->get();
            if ($target == "lucky7")    $autoplan[] = $lucky7Telem->id->get();
            if ($target == "stratosat") $autoplan[] = $stratosattkTelem->id->get();
        }

        $myStationRcv->autoPlan->set($autoplan);

        $myStationRcv->commit();

        return [
            "gs"     => $myStation->id->get(),
            "apiKey" => $myStation->apiKey->get()
        ];
    }
