<?php

namespace App\Event;

use App\Entity\WeatherRecord;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class WeatherDataReceivedEvent
 * @package App\Event
 */
final class WeatherDataReceivedEvent extends Event
{
    /** @var WeatherRecord */
    private $data;

    public function __construct(WeatherRecord $data)
    {
        $this->data = $data;
    }

    /**
     * @return WeatherRecord
     */
    public function getData(): WeatherRecord
    {
        return $this->data;
    }
}