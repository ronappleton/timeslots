<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Interfaces;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;

interface GeneratorInterface extends Arrayable, Jsonable
{
    /**
     * @param Carbon $fromDateTime
     * @param Carbon $toDateTime
     * @return GeneratorInterface
     */
    public function fromCarbon(Carbon $fromDateTime, Carbon $toDateTime): GeneratorInterface;

    /**
     * @param string $fromTime
     * @param string $toTime
     * @return GeneratorInterface
     */
    public function fromString(string $fromTime, string $toTime): GeneratorInterface;

    /**
     * Add timeslot exclusion to filter out invalid timeslots.
     *
     * @param TimeslotExclusionFilterInterface $exclusion
     * @return void
     */
    public function addExclusion(TimeslotExclusionFilterInterface $exclusion): GeneratorInterface;

    /**
     * Add timeslot offset to generator to affect slot generation start and end times.
     *
     * @param TimeslotOffsetInterface $offset
     * @return void
     */
    public function addGeneratorOffset(TimeslotOffsetInterface $offset): GeneratorInterface;

    /**
     * Add timeslot offset to timeslots to affect timeslot start and end times.
     *
     * @param TimeslotOffsetInterface $offset
     * @return GeneratorInterface
     */
    public function addTimeslotOffset(TimeslotOffsetInterface $offset): GeneratorInterface;

    /**
     * Generate the timeslots.
     *
     * @param int $interval
     * @return GeneratorInterface
     */
    public function generate(int $interval = 15): GeneratorInterface;

    /**
     * Get the timeslot collection.
     *
     * @return Collection
     */
    public function get(): Collection;
}
