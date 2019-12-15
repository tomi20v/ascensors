<?php

namespace App\Model;

class CallScheme {
    public $period;
    public $beginMinute;
    public $endMinute;
    public $fromLevel;
    public $toLevel;


    public function __construct(int $period, int $beginMinute, int $endMinute, int $fromLevel, int $toLevel)
    {
        $this->period = $period;
        $this->beginMinute = $beginMinute;
        $this->endMinute = $endMinute;
        $this->fromLevel = $fromLevel;
        $this->toLevel = $toLevel;
    }

}
