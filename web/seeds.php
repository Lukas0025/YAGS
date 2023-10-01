<?php
    /**
     * Create default user
     */
    $admin = new \DAL\user();
    $admin->userName->set("admin");
    $admin->pass->set("admin");
    $admin->admin->set(true);
    $admin->commit(); /* commit changes to DB */

    $satType = new \DAL\targetType();
    $satType->name->set("sat");
    $satType->commit();

    $wetImgType = new \DAL\dataType();
    $wetImgType->name->set("AVHRR");
    $wetImgType->commit();

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


    /**
     * Test station
     */
    $myStation = new \DAL\station();
    $myStation->name->set("default");
    $myStation->commit();

    $myStation137 = new \DAL\receiver();
    $myStation137->station->set($myStation);
    $myStation137->params->set([
        "radio" => "rtlsdr",
        "gain"  => 45,
        "agc"   => false,
        "fs"    => [250000, 1024000, 1536000, 1792000, 1920000, 2048000, 2160000, 2400000, 2560000, 2880000, 3200000]
    ]);
    $myStation137->antenna->set($yagi);
    $myStation137->centerFrequency->set(137500000);
    $myStation137->gain->set(45);
    $myStation137->commit();

    $myStation2G = new \DAL\receiver();
    $myStation2G->station->set($myStation);
    $myStation2G->params->set([
        "radio"    => "hackrf",
        "amp"      => true,
        "lna_gain" => 45,
        "vga_gain" => 10,
        "bias"     => true
    ]);
    $myStation2G->antenna->set($yagi);
    $myStation2G->centerFrequency->set(1700000000);
    $myStation2G->gain->set(45);
    $myStation2G->commit();

    /**
     * Modulations
     */

    $apt = new \DAL\modulation();
    $apt->name->set("APT");
    $apt->commit();

    $bpsk = new \DAL\modulation();
    $bpsk->name->set("BPSK");
    $bpsk->commit();

    $hrpt = new \DAL\modulation();
    $hrpt->name->set("HRPT");
    $hrpt->commit();

    $dsb = new \DAL\modulation();
    $dsb->name->set("DSB");
    $dsb->commit();

    $lrpt = new \DAL\modulation();
    $lrpt->name->set("LRPT");
    $lrpt->commit();

    /**
     * Process pipes
     */

    $aptPipe = new \DAL\processPipe();
    $aptPipe->name->set("NOAA APT");
    $aptPipe->pipe->set([
        "satdump noaa_apt baseband {baseband} {artefactDir} --samplerate {fs} --baseband_format s8",
        "cp {baseband} {artefactDir}"
    ]);

    $aptPipe->commit();

    $lrptPipe = new \DAL\processPipe();
    $lrptPipe->name->set("METEOR LRPT");
    $lrptPipe->pipe->set([
        "satdump meteor_m2-x_lrpt baseband {baseband} {artefactDir} --samplerate {fs} --baseband_format s8",
        "cp {baseband} {artefactDir}"
    ]);

    $lrptPipe->commit();


    /**
     * NOAA 19
     */
    $noaa19 = new \DAL\target();
    $noaa19->name->set("NOAA 19");
    $noaa19->type->set($satType);
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
    $noaa19APT->dataType->set($wetImgType);
    $noaa19APT->bandwidth->set(34000);
    $noaa19APT->centerFrequency->set(137100000);
    $noaa19APT->modulation->set($apt);
    $noaa19APT->antenna->set($qfh);
    $noaa19APT->processPipe->set($aptPipe);
    $noaa19APT->commit();

    $noaa19DSB = new \DAL\transmitter();
    $noaa19DSB->target->set($noaa19);
    $noaa19DSB->dataType->set($wetImgType);
    $noaa19DSB->bandwidth->set(34000);
    $noaa19DSB->centerFrequency->set(137770000);
    $noaa19DSB->modulation->set($dsb);
    $noaa19DSB->antenna->set($qfh);
    $noaa19DSB->commit();

    $noaa19HRPT = new \DAL\transmitter();
    $noaa19HRPT->target->set($noaa19);
    $noaa19HRPT->dataType->set($wetImgType);
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
    $noaa18->type->set($satType);
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
    $noaa18APT->dataType->set($wetImgType);
    $noaa18APT->bandwidth->set(34000);
    $noaa18APT->centerFrequency->set(137912500);
    $noaa18APT->modulation->set($apt);
    $noaa18APT->antenna->set($qfh);
    $noaa18APT->processPipe->set($aptPipe);
    $noaa18APT->commit();

    $noaa18DSB = new \DAL\transmitter();
    $noaa18DSB->target->set($noaa18);
    $noaa18DSB->dataType->set($wetImgType);
    $noaa18DSB->bandwidth->set(34000);
    $noaa18DSB->centerFrequency->set(137350000);
    $noaa18DSB->modulation->set($dsb);
    $noaa18DSB->antenna->set($qfh);
    $noaa18DSB->commit();

    $noaa18HRPT = new \DAL\transmitter();
    $noaa18HRPT->target->set($noaa18);
    $noaa18HRPT->dataType->set($wetImgType);
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
    $noaa15->type->set($satType);
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
    $noaa15APT->dataType->set($wetImgType);
    $noaa15APT->bandwidth->set(34000);
    $noaa15APT->centerFrequency->set(137500000);
    $noaa15APT->modulation->set($apt);
    $noaa15APT->antenna->set($qfh);
    $noaa15APT->processPipe->set($aptPipe);
    $noaa15APT->commit();

    $noaa15DSB = new \DAL\transmitter();
    $noaa15DSB->target->set($noaa15);
    $noaa15DSB->dataType->set($wetImgType);
    $noaa15DSB->bandwidth->set(34000);
    $noaa15DSB->centerFrequency->set(1377700000);
    $noaa15DSB->modulation->set($dsb);
    $noaa15DSB->antenna->set($qfh);
    $noaa15DSB->commit();

    $noaa15HRPT = new \DAL\transmitter();
    $noaa15HRPT->target->set($noaa15);
    $noaa15HRPT->dataType->set($wetImgType);
    $noaa15HRPT->bandwidth->set(3000000);
    $noaa15HRPT->centerFrequency->set(1702500000);
    $noaa15HRPT->modulation->set($hrpt);
    $noaa15HRPT->antenna->set($qfh);
    $noaa15HRPT->commit();

    $meteor23 = new \DAL\target();
    $meteor23->name->set("METEOR M2-3");
    $meteor23->type->set($satType);
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
    $meteor23LRPT1->dataType->set($wetImgType);
    $meteor23LRPT1->bandwidth->set(120000);
    $meteor23LRPT1->centerFrequency->set(137900000);
    $meteor23LRPT1->modulation->set($lrpt);
    $meteor23LRPT1->antenna->set($qfh);
    $meteor23LRPT1->processPipe->set($lrptPipe);
    $meteor23LRPT1->commit();

    $meteor23LRPT2 = new \DAL\transmitter();
    $meteor23LRPT2->target->set($meteor23);
    $meteor23LRPT2->dataType->set($wetImgType);
    $meteor23LRPT2->bandwidth->set(120000);
    $meteor23LRPT2->centerFrequency->set(137100000);
    $meteor23LRPT2->modulation->set($lrpt);
    $meteor23LRPT2->antenna->set($qfh);
    $meteor23LRPT2->processPipe->set($lrptPipe);
    $meteor23LRPT2->commit();

    $meteor23HRPT = new \DAL\transmitter();
    $meteor23HRPT->target->set($meteor23);
    $meteor23HRPT->dataType->set($wetImgType);
    $meteor23HRPT->bandwidth->set(3000000);
    $meteor23HRPT->centerFrequency->set(1700000000);
    $meteor23HRPT->modulation->set($hrpt);
    $meteor23HRPT->antenna->set($qfh);
    $meteor23HRPT->commit();