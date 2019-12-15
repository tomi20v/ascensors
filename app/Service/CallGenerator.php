<?php

namespace App\Service;

use App\Call;
use App\Model\CallScheme;

class CallGenerator
{

    /** @var CallScheme[] */
    private $schemes;

    public function __construct()
    {
        // @note these should rather come from config or similar
        $this->schemes = [
            new CallScheme(5, 9*60, 11*60, 0, 2),
            new CallScheme(5, 9*60, 11*60, 0, 3),
            new CallScheme(10, 9*60, 10*60, 0, 1),
            new CallScheme(20, 11*60, 18*60+20, 0, 4),
            new CallScheme(4, 14*60, 15*60, 1, 0),
            new CallScheme(4, 14*60, 15*60, 2, 0),
            new CallScheme(4, 14*60, 15*60, 3, 0),
            new CallScheme(7, 15*60, 16*60, 2, 0),
            new CallScheme(7, 15*60, 16*60, 3, 0),
            new CallScheme(7, 15*60, 16*60, 0, 1),
            new CallScheme(7, 15*60, 16*60, 0, 3),
            new CallScheme(3, 18*60, 20*60, 1, 0),
            new CallScheme(3, 18*60, 20*60, 2, 0),
            new CallScheme(3, 18*60, 20*60, 3, 0),
        ];
    }

    public function maxMinute()
    {
        $maxMinute = 0;
        foreach ($this->schemes as $eachScheme) {
            $maxMinute = max($maxMinute, $eachScheme->endMinute);
        }
        return $maxMinute;
    }

    public function generator(int $currentMinute)
    {
        foreach ($this->schemes as $eachScheme) {
            if (($currentMinute % $eachScheme->period === 0) &&
                ($eachScheme->beginMinute <= $currentMinute) &&
                ($eachScheme->endMinute > $currentMinute)
            ) {
                $call = new Call();
                $call->fromLevel = $eachScheme->fromLevel;
                $call->toLevel = $eachScheme->toLevel;
                yield $call;
            }
        }

    }

}
