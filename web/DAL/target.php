<?php
    namespace DAL;

    class target extends \wsos\database\core\row {
        public \wsos\database\types\text      $name;         // noaa19, jonHAM, ... , ...
        public \wsos\database\types\reference $type;         // sat, groundStation, ...
        public \wsos\database\types\text      $description;
        public \wsos\database\types\json      $locator;      // TLE, GPS or URL locator if avaible

        function __construct($id = null, $name = "", $type = null, $description = "", $locator = ["tle" => null, "gps" => null, "url" => null]) {
            parent::__construct($id);
            $this->name     = new \wsos\database\types\text($name);
            $this->type     = new \wsos\database\types\reference($type, );
            
            $this->description = new \wsos\database\types\text($description);
            $this->locator     = new \wsos\database\types\json($locator);
        }
    }
?>