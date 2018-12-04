<?php

namespace tests\Event;

use App\Event\MalformedApiResponseEvent;
use PHPUnit\Framework\TestCase;

class MalformedApiResponseEventTest extends TestCase
{
    public function testGetResponseContent_ReturnsResponseContent()
    {
        $event = new MalformedApiResponseEvent('test');
        $this->assertEquals('test', $event->getResponseContent());
    }
}