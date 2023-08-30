<?php
    namespace DAL;

    class observation extends \wsos\database\core\row {
        public \wsos\database\types\reference $transmitter; // observed trasmitter
        public \wsos\database\types\reference $receiver;    // used reciver
        public \wsos\database\types\enum      $status;      // fail, planed, ...
        public \wsos\database\types\text      $record;      // path to record
        public \wsos\database\types\json      $artefacts;   // JSON array of artefacts
        public \wsos\database\types\json      $locator;     // TLE, GPS or URL locator if avaible

        function __construct(
            $id = null,
            $transmitter = null,
            $receiver = null,
            $status = "",
            $record = "",
            $artefacts = [],
            $locator = ["tle" => null, "gps" => null, "url" => null]
        ) {
            parent::__construct($id);
            $this->transmitter = new \wsos\database\types\reference($transmitter, \DAL\transmitter::class);
            $this->receiver    = new \wsos\database\types\reference($receiver,    \DAL\receiver::class);
            $this->status      = new \wsos\database\types\enum($status, [
                "fail",
                "success",
                "recording",
                "decoding",
                "planed",
                "unknow"
            ], "unknow");

            $this->record      = new \wsos\database\types\text($record);
            $this->artefacts   = new \wsos\database\types\json($artefacts);
            $this->locator     = new \wsos\database\types\json($locator);
        }
    }
?>