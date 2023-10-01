<?php
    namespace DAL;

    class station extends \wsos\database\core\row {
        public \wsos\database\types\text      $name;       // Satellite, ...
        public \wsos\database\types\uuid      $apiKey;     // access key
        public \wsos\database\types\timestamp $lastSeen;
        public \wsos\database\types\text      $description; 
        public \wsos\database\types\json      $locator;

        function __construct($id = null, $name = "", $apiKey = null, $lastSeen = 0, $description = "", $locator = [], $autoPlan = []) {
            parent::__construct($id);
            $this->name        = new \wsos\database\types\text($name);
            $this->description = new \wsos\database\types\text($description);
            $this->apiKey      = new \wsos\database\types\uuid($apiKey);
            $this->lastSeen    = new \wsos\database\types\timestamp($lastSeen);
            $this->locator     = new \wsos\database\types\json($locator);
        }
    }
?>