<?php
    namespace DAL;

    class system extends \wsos\database\core\row {
        public \wsos\database\types\text      $name;
        public \wsos\database\types\text      $value; 

        function __construct($id = null, $name = "", $value = "") {
            parent::__construct($id);
            $this->name        = new \wsos\database\types\text($name);
            $this->value       = new \wsos\database\types\text($value);
        }
    }
?>