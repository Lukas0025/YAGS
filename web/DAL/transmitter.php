<?php
    namespace DAL;

    class transmitter extends \wsos\database\core\row {
        public \wsos\database\types\reference $target;          // object what hame this transmitter
        public \wsos\database\types\reference $antenna;         // YAGI, DISH, ....
        public \wsos\database\types\reference $modulation;      // BPSK, QPSK, AM, FM, ....
        public \wsos\database\types\reference $dataType;        // MSR, TELEMETRY, ....
        public \wsos\database\types\reference $processPipe;     // process pipe for transmitter
        public \wsos\database\types\integer   $centerFrequency; // in Hz
        public \wsos\database\types\integer   $bandwidth;       // in Hz
        public \wsos\database\types\boolean   $autoPlan;        // can be events autoplaned?

        function __construct(
            $id = null,
            $object = null,
            $antenna = null,
            $modulation = null,
            $dataType = null,
            $centerFrequency = 0,
            $bandwidth = 0,
            $autoPlan = false,
            $processPipe = null
        ) {
            parent::__construct($id);
            $this->object          = new \wsos\database\types\reference($object,      \DAL\object::class);
            $this->antenna         = new \wsos\database\types\reference($antenna,     \DAL\antenna::class);
            $this->modulation      = new \wsos\database\types\reference($modulation,  \DAL\modulation::class);
            $this->dataType        = new \wsos\database\types\reference($dataType,    \DAL\dataType::class);
            $this->object          = new \wsos\database\types\reference($processPipe, \DAL\processPipe::class);

            $this->centerFrequency = new \wsos\database\types\integer($centerFrequency);
            $this->bandwidth       = new \wsos\database\types\integer($bandwidth);
            $this->autoPlan        = new \wsos\database\types\boolean($autoPlan);
        }
    }
?>