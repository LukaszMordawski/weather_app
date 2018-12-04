<?php

namespace App\EventListener;

use App\Event\MalformedApiResponseEvent;
use App\Event\WeatherApiEvents;
use Psr\Log\LoggerInterface;

/**
 * Class MalformedApiResponseListener
 * @package App\EventSubscriber
 */
final class MalformedApiResponseListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onMalformedApiResponse(MalformedApiResponseEvent $event): void
    {
        $this->logger->error($event->getResponseContent(), [ WeatherApiEvents::MALFORMED_RESPONSE_EVENT ]);
    }
}