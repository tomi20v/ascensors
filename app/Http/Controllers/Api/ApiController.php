<?php

namespace App\Http\Controllers\Api;

use App\Ascensor;
use App\Call;
use App\Command\Tick;
use App\Command\Wipe;
use App\Exceptions\NotInitialized;
use App\Exceptions\OutOfService;
use App\Http\Controllers\Controller;
use App\Service\CallGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController extends Controller
{

    use DispatchesJobs;

    /** @var CallGenerator  */
    private $callGenerator;
    /** @var int max minute of day we'll still look for new calls */
    private $maxMinute;

    public function __construct(
        CallGenerator $callGenerator
    ) {
        $this->callGenerator = $callGenerator;
        $this->maxMinute = $this->callGenerator->maxMinute();
    }

    /**
     * init DB, @see Wipe
     */
    public function wipe(Request $request)
    {

        $ascensorCnt = intval($request->get('cnt'));
        if ($ascensorCnt < 1) {
            return Response::create('cnt required number|min:1', 400);
        }

        $ascensors = $this->dispatchNow(new Wipe($ascensorCnt));

        return $this->response($ascensors);

    }

    /**
     * one run, @see Tick
     */
    public function tick()
    {

        try {

            $ascensors = Ascensor::all();
            $calls = Call::all();

            $ascensors = $this->dispatchNow(new Tick($ascensors, $calls));

            return $this->response($ascensors);

        }
        catch (NotInitialized $e) {
            return Response::create('no ascensors', 400);
        }
        catch (OutOfService $e) {
            return Response::create('out of service', 400);
        }
    }

    private function response(Collection $ascensors)
    {
        return Response::create(array_map(function($eachAscensor) {
            /** @var Ascensor $eachAscensor */
            return [
                'id' => $eachAscensor->id,
                'level' => $eachAscensor->level,
                'traveled' => $eachAscensor->traveled,
                'lastMinute' => $eachAscensor->lastMinute,
            ];
        }, $ascensors->all()));
    }

}
