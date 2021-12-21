<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Generators;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Ronappleton\Timeslots\Interfaces\GeneratorInterface;
use Ronappleton\Timeslots\Interfaces\TimeslotExclusionFilterInterface;
use Ronappleton\Timeslots\Interfaces\TimeslotOffsetInterface;
use Ronappleton\Timeslots\Objects\Timeslot;

/**
 * Generate a collection of timeslots.
 */
class TimeSlotGenerator implements GeneratorInterface
{
    /**
     * @var Carbon|null
     */
    private ?Carbon $periodStart;

    /**
     * @var Carbon|null
     */
    private ?Carbon $periodEnd;

    /**
     * @var Collection|null
     */
    private Collection $timeslots;

    /**
     * @var array<TimeslotExclusionFilterInterface>
     */
    private array $timeslotExclusions = [];

    /**
     * @var array
     */
    private array $generatorOffsets = [];

    /**
     * @var array
     */
    private array $timeslotOffsets = [];

    /**
     * @param Carbon $fromDateTime
     * @param Carbon $toDateTime
     * @return GeneratorInterface
     */
    public function fromCarbon(Carbon $fromDateTime, Carbon $toDateTime): GeneratorInterface
    {
        $this->setPeriodStartCarbon($fromDateTime);
        $this->setPeriodEndCarbon($toDateTime);

        return $this;
    }

    /**
     * @param string $fromTime
     * @param string $toTime
     * @return GeneratorInterface
     */
    public function fromString(string $fromTime, string $toTime): GeneratorInterface
    {
        $this->setPeriodStartString($fromTime);
        $this->setPeriodEndString($toTime);

        return $this;
    }

    /**
     * Add timeslot exclusion to filter out invalid timeslots.
     *
     * @param TimeslotExclusionFilterInterface $exclusion
     * @return GeneratorInterface
     */
    public function addExclusion(TimeslotExclusionFilterInterface $exclusion): GeneratorInterface
    {
        $this->timeslotExclusions[] = $exclusion;

        return $this;
    }

    /**
     * Add timeslot offset to generator to affect slot generation start and end times.
     *
     * @param TimeslotOffsetInterface $offset
     * @return void
     */
    public function addGeneratorOffset(TimeslotOffsetInterface $offset): GeneratorInterface
    {
        $this->generatorOffsets[] = $offset;

        return $this;
    }

    /**
     * Add timeslot offset to timeslots to affect timeslot start and end times.
     *
     * @param TimeslotOffsetInterface $offset
     * @return GeneratorInterface
     */
    public function addTimeslotOffset(TimeslotOffsetInterface $offset): GeneratorInterface
    {
        $this->timeslotOffsets[] = $offset;

        return $this;
    }

    /**
     * @param Carbon $periodStart
     * @return void
     */
    private function setPeriodStartCarbon(Carbon $periodStart): void
    {
        $this->periodStart = $periodStart;
    }

    /**
     * @param string $periodStart
     * @return void
     */
    private function setPeriodStartString(string $periodStart): void
    {
        $this->setPeriodStartCarbon(Carbon::parse($periodStart));
    }

    /**
     * @param Carbon $periodEnd
     * @return void
     */
    private function setPeriodEndCarbon(Carbon $periodEnd): void
    {
        $this->periodEnd = $periodEnd;
    }

    /**
     * @param string $periodEnd
     * @return void
     */
    private function setPeriodEndString(string $periodEnd): void
    {
        $this->setPeriodEndCarbon(Carbon::parse($periodEnd));
    }

    /**
     * @return void
     */
    private function adjustEndTime(): void
    {
        if ($this->periodEnd->lt($this->periodStart)) {
            $this->periodEnd->addDay();
        }
    }

    /**
     * Generate the actual timeslots.
     *
     * @param int $interval
     * @return GeneratorInterface
     */
    public function generate(int $interval = 15): GeneratorInterface
    {
        $this->adjustEndTime();
        $this->offsetGenerator();
        $this->timeslots = new Collection();

        $periodMinutes = sprintf('%d minutes', $interval);
        $period = new CarbonPeriod($this->periodStart, $periodMinutes, $this->periodEnd);

        $period->forEach(function ($date) use ($interval) {
            $this->timeslots->push(new Timeslot($date, $interval, $this->timeslotOffsets));
        });

        $this->filterSlots();

        return $this;
    }

    private function filterSlots(): void
    {
        $this->timeslots = $this->timeslots->filter(function ($timeslot){
            foreach ($this->timeslotExclusions as $exclusion) {
                if ($exclusion->assess($timeslot)) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Take generator offsets into account.
     *
     * @return void
     */
    private function offsetGenerator(): void
    {
        foreach ($this->generatorOffsets as $offset) {
            [$period, $offset] = $offset->getOffset();

            if ($period === 'start') {
                $this->periodStart = $this->periodStart->addMinutes($offset);
            }
            if ($period === 'end') {
                $this->periodEnd = $this->periodEnd->subMinutes($offset);
            }
        }
    }

    public function get(): Collection
    {
        return $this->timeslots;
    }

    public function toArray(): array
    {
        return $this->get()->toArray();
    }

    public function toJson($options = 0): string
    {
        return $this->get()->toJson();
    }
}
