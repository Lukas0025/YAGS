<?php
    namespace DAL;

    class dataType extends \wsos\database\core\row {
        public \wsos\database\types\text      $name;   // MSR, TELEMETRY, ...
        public \wsos\database\types\text      $description; 

        function __construct($id = null, $name = "", $description = "") {
            parent::__construct($id);
            $this->name        = new \wsos\database\types\text($name);
            $this->description = new \wsos\database\types\text($description);
        }
    }
?>