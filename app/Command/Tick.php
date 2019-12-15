<?php

namespace App\Command;

use App\Ascensor;
use App\Call;
use App\Exceptions\NoAscensorAvailable;
use App\Exceptions\NotInitialized;
use App\Exceptions\OutOfService;
use App\Service\AscensorManager;
use App\Service\CallGenerator;
use Illuminate\Database\Eloquent\Collection;

/**
 * one minute run:
 * - start from last runtime + 1 minute
 * - generate new calls
 * - if there are no pending calls, and no new calls for current run neither, keep advancing current time until there is a call generated
 * - fulfill calls as possible, save pending calls
 */
class Tick
{

    /** @var Ascensor[]|Collection */
    private $ascensors;
    /** @var Call[]|Collection */
    private $pendingCalls;

    /**
     * @param Ascensor[]|Collection $ascensors
     * @param Call[]|Collection $pendingCalls
     * @throws NotInitialized
     */
    public function __construct(
        Collection $ascensors,
        Collection $pendingCalls
    ) {
        if (!count($ascensors))
            throw new NotInitialized();
        $this->ascensors = $ascensors;
        $this->pendingCalls = $pendingCalls;
    }

    /**
     * @param CallGenerator $callGenerator
     * @param AscensorManager $ascensorManager
     * @return Ascensor[]|Collection
     * @throws OutOfService
     * @throws \Exception
     */
    public function handle(
        CallGenerator $callGenerator,
        AscensorManager $ascensorManager
    ) {

        $currentMinute = $ascensorManager->lastTimeWhenAscensorsMoved(...$this->ascensors);
        $maxMinute = $callGenerator->maxMinute();

        $calls = $this->pendingCalls;

        do {
            $currentMinute++;
            if ($currentMinute > $maxMinute && count($calls) === 0) {
                throw new OutOfService();
            }
            $calls = $this->addNewCalls($callGenerator, $currentMinute, ...$calls);
        } while (count($calls) === 0);

        $availableAscensors = $this->ascensors;

        try {
            while (count($availableAscensors) > 0 && count($calls) > 0) {

                /** @var Call $eachCall */
                foreach ($calls as $eachIndex => $eachCall) {
                    $ascensor = $ascensorManager->nearestTo($eachCall->fromLevel, ...$availableAscensors);
                    $ascensorManager->fulfillCall($ascensor, $eachCall, $currentMinute);
                    $availableAscensors = $availableAscensors->reject(function ($eachAscensor) use ($ascensor) {

                        return $eachAscensor === $ascensor;
                    });

                    $eachCall->delete();
                    unset($calls[$eachIndex]);

                }
            }
        }
        catch (NoAscensorAvailable $e) {}

        return $this->ascensors;

    }

    private function addNewCalls(
        CallGenerator $callGenerator,
        int $currentMinute,
        Call ...$calls
    ) {
        foreach ($callGenerator->generator($currentMinute) as $eachCall) {
            $eachCall->save();
            $calls[] = $eachCall;
        }
        return $calls;
    }

}
