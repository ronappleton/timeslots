<?php

declare(strict_types=1);

namespace Ronappleton\Timeslots\Objects;

use Carbon\Carbon;
use JsonException;
use Ronappleton\Timeslots\Interfaces\TimeslotInterface;

/**
 * This object represents a period within time.
 */
class Timeslot implements TimeslotInterface
{
    /**
     * The initial time period starting point.
     *
     * @var Carbon
     */
    private Carbon $timeslotStart;

    /**
     * The time period ending point.
     *
     * @var Carbon
     */
    private Carbon $timeslotEnd;

    /**
     * The duration of the timeslot period.
     *
     * @var int
     */
    private int $timeslotPeriodMinutes;

    /**
     * Allow the object properties to be created on initialisation.
     *
     * @param Carbon|null $timeslotStart
     * @param int|null $timeslotPeriodMinutes
     */
    public function __construct(Carbon $timeslotStart, int $timeslotPeriodMinutes, array $offsets)
    {
        $this->timeslotStart = $timeslotStart;
        $this->timeslotEnd = $this->timeslotStart->clone()->addMinutes($timeslotPeriodMinutes);
        $this->timeslotPeriodMinutes = $timeslotPeriodMinutes;
    }

    /**
     * @return Carbon
     */
    public function getTimeslotStart(): Carbon
    {
        return $this->timeslotStart;
    }

    /**
     * @param Carbon $timeslotStart
     * @return Timeslot
     */
    public function setTimeslotStart(Carbon $timeslotStart): Timeslot
    {
        $this->timeslotStart = $timeslotStart;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getTimeslotEnd(): Carbon
    {
        return $this->timeslotEnd;
    }

    /**
     * @param Carbon $timeslotEnd
     * @return Timeslot
     */
    public function setTimeslotEnd(Carbon $timeslotEnd): Timeslot
    {
        $this->timeslotEnd = $timeslotEnd;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeslotPeriodMinutes(): int
    {
        return $this->timeslotPeriodMinutes;
    }

    /**
     * @param int $timeslotPeriodMinutes
     * @return Timeslot
     */
    public function setTimeslotPeriodMinutes(int $timeslotPeriodMinutes): Timeslot
    {
        $this->timeslotPeriodMinutes = $timeslotPeriodMinutes;
        return $this;
    }

    /**
     * Performs a check to see if the given dateTime is within the timeslot period.
     *
     * @param Carbon $dateTime
     * @return bool
     */
    public function withinTimeslot(Carbon $dateTime): bool
    {
        return $dateTime->gte($this->getTimeslotStart()) && $dateTime->lte($this->getTimeslotEnd());
    }

    /**
     * Performs a check to see if this timeslot is with a time period, that spans minutes, hours or days for example.
     *
     * @param Carbon $periodStart
     * @param Carbon $periodEnd
     * @return bool
     */
    public function withinPeriod(Carbon $periodStart, Carbon $periodEnd): bool
    {
        return $this->getTimeslotStart()->gte($periodStart) && $this->getTimeslotEnd()->lte($periodEnd);
    }

    /**
     * Performs check to see if the day given falls within the timeslot.
     * If for example a shop was open 9 am to 3 am and the timeslot span was after midnight, even though that would
     * be a day later, to the shop it would be the same day 'shift' if you like, so we need to allow the day to be
     * a day later.
     *
     * @param Carbon $dateTime
     * @return bool
     */
    public function withinDay(Carbon $dateTime): bool
    {
        return $dateTime->gte($this->getTimeslotStart())
            && $dateTime->lte($this->getTimeslotEnd());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'slotStart' => $this->timeslotStart,
            'slotEnd' => $this->timeslotEnd,
        ];
    }

    /**
     * @throws JsonException
     * @return string|false
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Apply timeslot offsets.
     *
     * @param array $offsets
     * @return void
     */
    private function applyOffsets(array $offsets): void
    {
        foreach ($offsets as $period => $offset) {
            [$period, $offset] = $offset->getOffset();

            if ($period === 'start') {
                $this->timeslotStart->addMinutes($offset);
            }
            if ($period === 'end') {
                $this->timeslotEnd->subMinutes($offset);
            }
        }
    }
}
