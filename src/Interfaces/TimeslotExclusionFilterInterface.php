<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Interfaces;

use Ronappleton\Timeslots\Objects\Timeslot;

interface TimeslotExclusionFilterInterface
{
    /**
     * Return validity of timeslot based on exclusion criteria.
     * A positive result means filter out.
     *
     * @param Timeslot $timeslot
     * @return bool
     */
    public function assess(Timeslot $timeslot): bool;
}
