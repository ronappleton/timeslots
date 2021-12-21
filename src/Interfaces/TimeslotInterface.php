<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Interfaces;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface TimeslotInterface extends Arrayable, Jsonable
{
    /**
     * @param Carbon $dateTime
     * @return bool
     */
    public function withinTimeslot(Carbon $dateTime): bool;
}
