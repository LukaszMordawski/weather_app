<?php

namespace App\Service\Weather;

use App\Entity\WeatherRecord;

/**
 * Interface WeatherProviderInterface
 * @package App\Service\WeatherCondition
 */
interface WeatherProviderInterface
{
    /**
     * @param string $city
     * @return WeatherRecord[]
     */
    public function fetchByCity(string $city): array;
}