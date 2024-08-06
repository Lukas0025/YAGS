<?php
    namespace DAL;

    class uplink extends \wsos\database\core\row {
        public \wsos\database\types\reference $transmitter; // observed trasmitter
        public \wsos\database\types\reference $receiver;    // used reciver
        public \wsos\database\types\enum      $status;      // fail, planed, ...
        public \wsos\database\types\text      $data;        // hexdump of data to upload
        public \wsos\database\types\json      $locator;     // TLE, GPS or URL locator if avaible
        public \wsos\database\types\timestamp $start;       // start datetime
        public \wsos\database\types\timestamp $end;         // end datetimr
        public \wsos\database\types\integer   $delay;       // uplink after downlink delay time

        function __construct(
            $id = null,
            $transmitter = null,
            $receiver = null,
            $status = "",
            $data = "",
            $locator = ["tle" => null, "gps" => null, "url" => null],
            $start = "2000-01-01 00:10:00",
            $end = "2000-01-01 00:10:00",
            $delay = 0
        ) {
            parent::__construct($id);
            $this->transmitter = new \wsos\database\types\reference($transmitter, \DAL\receiver::class);
            $this->receiver    = new \wsos\database\types\reference($receiver,    \DAL\transmitter::class);
            $this->status      = new \wsos\database\types\enum($status, [
                "fail",
                "done",
                "planed",
                "unknow"
            ], "unknow");

            $this->data        = new \wsos\database\types\text($data);
            $this->locator     = new \wsos\database\types\json($locator);
            $this->start       = new \wsos\database\types\timestamp($start);
            $this->end         = new \wsos\database\types\timestamp($end);
            $this->delay       = new \wsos\database\types\integer($delay);
        }
    }
?>