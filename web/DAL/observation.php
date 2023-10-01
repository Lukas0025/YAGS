<?php
    namespace DAL;

    class observation extends \wsos\database\core\row {
        public \wsos\database\types\reference $transmitter; // observed trasmitter
        public \wsos\database\types\reference $receiver;    // used reciver
        public \wsos\database\types\enum      $status;      // fail, planed, ...
        public \wsos\database\types\text      $record;      // path to record
        public \wsos\database\types\json      $artefacts;   // JSON array of artefacts
        public \wsos\database\types\json      $locator;     // TLE, GPS or URL locator if avaible
        public \wsos\database\types\timestamp $start;       // start datetime
        public \wsos\database\types\timestamp $end;         // end datetimr

        function __construct(
            $id = null,
            $transmitter = null,
            $receiver = null,
            $status = "",
            $record = "",
            $artefacts = [],
            $locator = ["tle" => null, "gps" => null, "url" => null],
            $start = "2000-01-01 00:00:00",
            $end = "2000-01-01 00:10:00"
        ) {
            parent::__construct($id);
            $this->transmitter = new \wsos\database\types\reference($transmitter, \DAL\transmitter::class);
            $this->receiver    = new \wsos\database\types\reference($receiver,    \DAL\receiver::class);
            $this->status      = new \wsos\database\types\enum($status, [
                "fail",
                "success",
                "recording",
                "recorded",
                "decoding",
                "planed",
                "assigned",
                "unknow"
            ], "unknow");

            $this->record      = new \wsos\database\types\text($record);
            $this->artefacts   = new \wsos\database\types\json($artefacts);
            $this->locator     = new \wsos\database\types\json($locator);
            $this->start       = new \wsos\database\types\timestamp($start);
            $this->end         = new \wsos\database\types\timestamp($end);
        }
    }
?>