<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class MalformedApiResponseEvent
 * @package App\Event
 */
final class MalformedApiResponseEvent extends Event
{
    /**
     * @var string
     */
    private $responseContent;

    /**
     * MalformedApiResponseEvent constructor.
     * @param string $responseContent
     */
    public function __construct(
        string $responseContent
    )
    {
        $this->responseContent = $responseContent;
    }

    /**
     * @return string
     */
    public function getResponseContent(): string
    {
        return $this->responseContent;
    }
}