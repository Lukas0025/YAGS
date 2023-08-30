<?php
    namespace DAL;

    class user extends \wsos\database\core\row {
        public \wsos\database\types\text      $userName;
        public \wsos\database\types\text      $realName;
        public \wsos\database\types\password  $pass;
        public \wsos\database\types\boolean   $admin;
        public \wsos\database\types\json      $radios;

        function __construct($id = null, $userName = "", $realName = "", $password = "", $admin = false, $radios = []) {
            parent::__construct($id);
            $this->userName = new \wsos\database\types\text($userName);
            $this->realName = new \wsos\database\types\text($realName);
            $this->pass     = new \wsos\database\types\password($password);
            $this->admin    = new \wsos\database\types\boolean($admin);
            $this->radios   = new \wsos\database\types\json($radios);
        }
    }
?>