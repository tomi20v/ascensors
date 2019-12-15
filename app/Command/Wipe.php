<?php

namespace App\Command;

use App\Ascensor;
use App\Call;
use Illuminate\Database\Eloquent\Collection;

/**
 * reset database, create cnt pieces of ascensor models on level 0
 */
class Wipe
{

    /** @var int  */
    private $cnt;

    public function __construct(
        int $cnt
    ) {
        $this->cnt = $cnt;
    }

    public function handle()
    {

        Call::query()->delete();
        Ascensor::query()->delete();

        $ascensors = new Collection();
        for ($i=0; $i<$this->cnt; $i++) {
            $ascensor = new Ascensor();
            $ascensor->column = $i;
            $ascensor->save();
            $ascensors[] = $ascensor;
        }

        return $ascensors;

    }

}
