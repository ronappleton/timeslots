<?php

namespace Ronappleton\Timeslots\Exclusions;

use Carbon\Carbon;
use Ronappleton\Timeslots\Interfaces\TimeslotExclusionFilterInterface;
use Ronappleton\Timeslots\Objects\Timeslot;

class TimePeriodExclusion implements TimeslotExclusionFilterInterface
{
    private Carbon $periodStart;
    private Carbon $periodEnd;

    public function __construct(Carbon $periodStart, Carbon $periodEnd)
    {
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
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
        return $timeslot->withinTimeslot($this->periodStart) || $timeslot->withinTimeslot($this->periodEnd);
    }

    public function adjustPeriodDate(Timeslot $timeslot)
    {
        $date = $timeslot->getTimeslotStart()->format('Y-m-d');
        $time =
    }
}
