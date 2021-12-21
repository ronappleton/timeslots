<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Exclusions;

use Carbon\Carbon;
use Ronappleton\Timeslots\Interfaces\TimeslotExclusionFilterInterface;
use Ronappleton\Timeslots\Objects\Timeslot;

class DateExclusion implements TimeslotExclusionFilterInterface
{
    /**
     * @var Carbon
     */
    private Carbon $excludedDay;

    /**
     * @param Carbon $excludedDay
     */
    public function __construct(Carbon $excludedDay)
    {
        $this->excludedDay = $excludedDay;
    }

    /**
     * Return validity of timeslot based on exclusion criteria.
     * A positive result means filter out.
     *
     * @param Timeslot $timeslot
     * @return bool
     */
    public function assess(Timeslot $timeslot): bool
    {
        return $timeslot->withinDay($this->excludedDay);
    }
}
