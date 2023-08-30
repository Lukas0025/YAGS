<?php
    namespace DAL;

    class processPipe extends \wsos\database\core\row {
        public \wsos\database\types\text  $name;
        public \wsos\database\types\json  $pipe;

        function __construct($id = null, $name = "", $pipe = []) {
            parent::__construct($id);
            $this->name = new \wsos\database\types\text($name);
            $this->pipe = new \wsos\database\types\json($pipe);
        }
    }
?>