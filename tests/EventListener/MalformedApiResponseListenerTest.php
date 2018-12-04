<?php

namespace tests\EventListener;

use App\Event\MalformedApiResponseEvent;
use App\Event\WeatherApiEvents;
use App\EventListener\MalformedApiResponseListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MalformedApiResponseListenerTest extends TestCase
{
    public function testOnMalformedApiResponse_LogsResponseContent()
    {
        $event = new MalformedApiResponseEvent('zzzzaa');

        /** @var MockObject|LoggerInterface $logger */
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $listener = new MalformedApiResponseListener($logger);

        $logger->expects($this->once())
            ->method('error')
            ->with('zzzzaa', [ WeatherApiEvents::MALFORMED_RESPONSE_EVENT ]);

        $listener->onMalformedApiResponse($event);
    }
}