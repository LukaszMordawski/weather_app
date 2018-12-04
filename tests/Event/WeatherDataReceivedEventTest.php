<?php

namespace tests\Event;

use App\Entity\WeatherRecord;
use App\Event\WeatherDataReceivedEvent;
use PHPUnit\Framework\TestCase;

class WeatherDataReceivedEventTest extends TestCase
{
    public function testGetData_ReturnsWeatherRecordObject()
    {
        $event = new WeatherDataReceivedEvent(new WeatherRecord());
        $this->assertInstanceOf(WeatherRecord::class, $event->getData());
    }
}