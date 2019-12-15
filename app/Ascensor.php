<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $column
 * @property int $level
 * @property int $traveled
 * @property int $lastMinute
 */
class Ascensor extends Model
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->level = 0;
        $this->traveled = 0;
        $this->lastMinute = 0;
    }

}
