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
    $wetImgType->name->set("weather photo");
    $wetImgType->commit();


    /**
     * Test station
     */
    $myStation = new \DAL\station();
    $myStation->name->set("Medlánky");
    $myStation->commit();

    $myStation137 = new \DAL\receiver();
    $myStation137->station->set($myStation);
    $myStation137->commit();

    /**
     * NOAA modulations
     */

    $fm = new \DAL\modulation();
    $fm->name->set("FM");
    $fm->commit();


    /**
     * NOAA 19
     */
    $noaa19 = new \DAL\target();
    $noaa19->name->set("NOAA 19");
    $noaa19->type->set($satType);
    $noaa19->description->set("NOAA 19 is the fifth in a series of five Polar-orbiting Operational Environmental Satellites (POES) with advanced microwave sounding instruments that provide imaging and sounding capabilities.");
    $noaa19->locator->set([
        "tle" => "NOAA 19\n1 33591U 09005A   23243.18101660  .00000207  00000-0  13587-3 0  9998\n2 33591  99.0938 290.2850 0014342  35.8617 324.3514 14.12812127750532"
    ]);
    $noaa19->commit();

    $noaa19APT = new \DAL\transmitter();
    $noaa19APT->target->set($noaa19);
    $noaa19APT->dataType->set($wetImgType);
    $noaa19APT->bandwidth->set(100);
    $noaa19APT->centerFrequency->set(137100000);
    $noaa19APT->modulation->set($fm);
    $noaa19APT->commit();

    /**
     * NOAA 18
     */
    $noaa18 = new \DAL\target();
    $noaa18->name->set("NOAA 18");
    $noaa18->type->set($satType);
    $noaa18->description->set("NOAA 18, known before launch as NOAA-N, is a weather forecasting satellite run by NOAA. NOAA-N (18) was launched into a sun-synchronous orbit at an altitude of 854 km above the Earth, with an orbital period of 102 minutes. It hosts the AMSU-A, MHS, AVHRR, Space Environment Monitor SEM/2 instrument and High Resolution Infrared Radiation Sounder (HIRS) instruments, as well as the SBUV/2 ozone-monitoring instrument.");
    $noaa18->locator->set([
        "tle" => "NOAA 18\n1 28654U 05018A   23250.34978256  .00000271  00000-0  16921-3 0  9997\n2 28654  98.9045 324.3258 0015006 144.5825 215.6347 14.13000434943172"
    ]);
    $noaa18->commit();

    $noaa18APT = new \DAL\transmitter();
    $noaa18APT->target->set($noaa18);
    $noaa18APT->dataType->set($wetImgType);
    $noaa18APT->bandwidth->set(100);
    $noaa18APT->centerFrequency->set(137912500);
    $noaa18APT->modulation->set($fm);
    $noaa18APT->commit();


    /**
     * Plan observation
     */
    $noaa19Plan = new \DAL\observation();
    $noaa19Plan->status->set("planed");
    $noaa19Plan->locator->set($noaa19->locator->get());
    $noaa19Plan->transmitter->set($noaa19APT);
    $noaa19Plan->receiver->set($myStation137);
    $noaa19Plan->commit();

    $noaa18Plan = new \DAL\observation();
    $noaa18Plan->status->set("planed");
    $noaa18Plan->locator->set($noaa18->locator->get());
    $noaa18Plan->transmitter->set($noaa18APT);
    $noaa18Plan->receiver->set($myStation137);
    $noaa18Plan->commit();