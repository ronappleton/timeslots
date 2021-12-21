<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Interfaces;

/**
 * Timeslot offsets are added prior to timeslot generation.
 * For example when the shop opens, a small amount of time
 * is needed to prepare for the time slots.
 */
interface TimeslotOffsetInterface
{
    /**
     * Get the timeslot offset in minutes.
     *
     * @return array
     */
    public function getOffset(): array;
}
