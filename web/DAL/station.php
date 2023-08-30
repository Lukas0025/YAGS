<?php
    namespace DAL;

    class station extends \wsos\database\core\row {
        public \wsos\database\types\text      $name;   // Satellite, ...
        public \wsos\database\types\text      $description; 
        public \wsos\database\types\json      $locator; 

        function __construct($id = null, $name = "", $description = "", $locator = ["tle" => null, "gps" => null, "url" => null]) {
            parent::__construct($id);
            $this->name        = new \wsos\database\types\text($name);
            $this->description = new \wsos\database\types\text($description);

            $this->locator     = new \wsos\database\types\json($locator);
        }
    }
?>