<?php
    namespace DAL;

    class receiver extends \wsos\database\core\row {
        public \wsos\database\types\reference $station;         // station with antena
        public \wsos\database\types\reference $antenna;         // YAGI, DISH, ....
        public \wsos\database\types\integer   $centerFrequency; // in Hz
        public \wsos\database\types\integer   $bandwidth;       // in Hz
        public \wsos\database\types\integer   $gain;            // gain of reciver setup

        function __construct(
            $id              = null,
            $station         = null,
            $antenna         = null,
            $centerFrequency = 0,
            $bandwidth       = 0,
            $gain            = 0
        ) {
            parent::__construct($id);
            $this->station        = new \wsos\database\types\reference($station,     \DAL\station::class);
            $this->antenna         = new \wsos\database\types\reference($antenna,     \DAL\antenna::class);

            $this->centerFrequency = new \wsos\database\types\integer($centerFrequency);
            $this->bandwidth       = new \wsos\database\types\integer($bandwidth);
            $this->gain            = new \wsos\database\types\integer($gain);
        }
    }
?>