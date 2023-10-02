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
        public \wsos\database\types\integer   $priority;        // priority of transmitter

        function __construct(
            $id = null,
            $target = null,
            $antenna = null,
            $modulation = null,
            $dataType = null,
            $centerFrequency = 0,
            $bandwidth = 0,
            $priority = 0,
            $processPipe = null
        ) {
            parent::__construct($id);
            $this->target          = new \wsos\database\types\reference($target,      \DAL\target::class);
            $this->antenna         = new \wsos\database\types\reference($antenna,     \DAL\antenna::class);
            $this->modulation      = new \wsos\database\types\reference($modulation,  \DAL\modulation::class);
            $this->dataType        = new \wsos\database\types\reference($dataType,    \DAL\dataType::class);
            $this->processPipe     = new \wsos\database\types\reference($processPipe, \DAL\processPipe::class);

            $this->centerFrequency = new \wsos\database\types\integer($centerFrequency);
            $this->bandwidth       = new \wsos\database\types\integer($bandwidth);
            $this->priority        = new \wsos\database\types\integer($priority);
        }
    }
?>