<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Ronappleton\Timeslots\Exclusions\DateExclusion;
use Ronappleton\Timeslots\Exclusions\TimePeriodExclusion;
use Ronappleton\Timeslots\Generators\TimeSlotGenerator;
use Ronappleton\Timeslots\Objects\Timeslot;
use Ronappleton\Timeslots\Offsets\OpeningOffset;

class TimeSlotGeneratorTest extends TestCase
{
    /**
     * @return void
     */
    public function testTimeslotGeneration(): void
    {
        $generator = new TimeSlotGenerator;

        $result = $generator->fromString('09:00', '03:00')->generate(15)->get();

        $this->assertCount(73, $result);
        $this->assertInstanceOf(Timeslot::class, $result->first());
    }

    /**
     * @return void
     */
    public function testTimeslotGenerationToArray(): void
    {
        $result = (new TimeSlotGenerator)
            ->fromCarbon(Carbon::parse('2021-12-01 09:00'), Carbon::parse('2021-12-02 03:00'))
            ->addGeneratorOffset(new OpeningOffset())
            ->addExclusion(new DateExclusion(Carbon::parse('2021-12-01')))
            ->generate(15)
            ->toJson();

        dd($result);
        $this->assertCount(73, $result);
        $this->assertIsArray($result);
        $this->assertIsArray($result[0]);

        dd($result);
    }

    /**
     * @throws \JsonException
     */
    public function testTimeslotGenerationToJson(): void
    {
        $generator = new TimeSlotGenerator;

        $result = $generator->fromString('09:00', '03:00')->generate(15)->toJson();

        $this->assertIsString($result);
        $array = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(73, $array);
        $this->assertIsArray($array);
        $this->assertIsArray($array[0]);
    }
}
