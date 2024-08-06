<?php
    namespace DAL;

    class transmitter extends \wsos\database\core\row {
        public \wsos\database\types\reference $target;          // object what hame this transmitter
        public \wsos\database\types\reference $antenna;         // YAGI, DISH, ....
        public \wsos\database\types\reference $modulation;      // BPSK, QPSK, AM, FM, LORA, ....
        public \wsos\database\types\reference $dataType;        // MSR, TELEMETRY, ....
        public \wsos\database\types\reference $processPipe;     // process pipe for transmitter
        public \wsos\database\types\integer   $centerFrequency; // in Hz
        public \wsos\database\types\integer   $bandwidth;       // in Hz
        public \wsos\database\types\integer   $priority;        // priority of transmitter
        public \wsos\database\types\integer   $sf;              // spreading factor for LORA
        public \wsos\database\types\integer   $codingRate;      // coding rate
        public \wsos\database\types\text      $syncWord;        // sync word
        public \wsos\database\types\integer   $preambleLength;  // preamble length for LORA

        function __construct(
            $id = null,
            $target = null,
            $antenna = null,
            $modulation = null,
            $dataType = null,
            $centerFrequency = 0,
            $bandwidth = 0,
            $priority = 0,
            $processPipe = null,
            $sf = 7,
            $codingRate = 5,
            $syncWord = "34",
            $preambleLength = 8
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

            $this->sf              = new \wsos\database\types\integer($sf);
            $this->codingRate      = new \wsos\database\types\integer($codingRate);
            $this->syncWord        = new \wsos\database\types\text($syncWord);
            $this->preambleLength  = new \wsos\database\types\integer($preambleLength);
        }
    }
?>