<?php

namespace App\Service\Weather;

use App\Entity\WeatherRecord;

/**
 * Interface WeatherTranslatorInterface
 * @package App\Service\WeatherCondition
 */
interface WeatherTranslatorInterface
{
    /**
     * @param string $results
     * @return WeatherRecord
     */
    public function translateResult(string $results): WeatherRecord;
}