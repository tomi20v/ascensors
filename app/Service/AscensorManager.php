<?php

namespace App\Service;

use App\Ascensor;
use App\Call;
use App\Exceptions\NoAscensorAvailable;

class AscensorManager
{

    public function lastTimeWhenAscensorsMoved(Ascensor ...$ascensors): int
    {
        $currentMinute = 0;
        /** @var Ascensor $eachAscensor */
        foreach ($ascensors as $eachAscensor) {
            $currentMinute = max($currentMinute, $eachAscensor->lastMinute);
        }
        return $currentMinute;
    }

    /**
     * Choose ascensor which has to travel the least to a given level.
     * Will prefer ascensor with less travel to
     * @throws NoAscensorAvailable
     */
    public function nearestTo(int $level, Ascensor ...$ascensors): Ascensor
    {

        if (!count($ascensors)) {
            throw new NoAscensorAvailable();
        }

        /** @var Ascensor $nearest */
        $nearest = reset($ascensors);
        foreach ($ascensors as $eachAscensor) {
            if (abs($eachAscensor->level - $level) <= abs($nearest->level - $level) &&
                ($eachAscensor->traveled <= $nearest->traveled)
            ) {
                $nearest = $eachAscensor;
            }
        }
        return $nearest;
    }

    public function fulfillCall(
        Ascensor $ascensor,
        Call $eachCall,
        int $currentMinute
    ) {

        $travelToCall = abs($eachCall->fromLevel - $ascensor->level);
        $usefulTravel = abs($eachCall->toLevel - $eachCall->fromLevel);

        $ascensor->level = $eachCall->toLevel;
        $ascensor->traveled+= $travelToCall + $usefulTravel;
        $ascensor->lastMinute = $currentMinute;

        $ascensor->saveOrFail();

    }

}
