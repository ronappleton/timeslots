<?php

namespace Ronappleton\Timeslots\Offsets;

class OpeningOffset implements \Ronappleton\Timeslots\Interfaces\TimeslotOffsetInterface
{
    /**
     * Get the timeslot offset.
     *
     * @return array
     */
    public function getOffset(): array
    {
        return ['start', 30];
    }
}
