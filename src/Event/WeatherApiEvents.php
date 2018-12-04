<?php

namespace App\Event;

/**
 * Class WeatherApiEvents
 * @package App\Event
 */
final class WeatherApiEvents
{
    const MALFORMED_RESPONSE_EVENT = 'weather_api.malformed_response';
    const WEATHER_DATA_RECEIVED = 'weather_api.weather_data_received';
}