<?php

namespace App\Service\Weather\OpenWeatherMap;

use App\Entity\AirCondition;
use App\Entity\City;
use App\Entity\WeatherCondition;
use App\Entity\WeatherRecord;
use App\Entity\WindCondition;
use App\Service\Weather\Exception\MalformedApiResponseException;
use App\Service\Weather\WeatherTranslatorInterface;

/**
 * Class OpenWeatherMapTranslator
 * @package App\Service\Weather\OpenWeatherMap
 */
final class OpenWeatherMapTranslator implements WeatherTranslatorInterface
{
    /**
     * @inheritdoc
     */
    public function translateResult(string $results): WeatherRecord
    {
        $dataArray = json_decode($results, true);
        if (is_null($dataArray)) {
            throw new MalformedApiResponseException("Malformed OpenWeatherMap API response");
        }

        $weather = $this->getWeatherRecord($dataArray);

        $airCondition = $this->getAirCondition($dataArray);
        $weather->setAirCondition($airCondition);

        $city = $this->getCity($dataArray);
        $weather->setCity($city);

        $wind = $this->getWindCondition($dataArray);
        $weather->setWind($wind);

        foreach ($dataArray['weather'] as $rawWeatherData) {
            $weatherCondition = $this->getWeatherCondition($rawWeatherData);
            $weather->addWeatherCondition($weatherCondition);
        }
        
        return $weather;
    }

    /**
     * @param int $timestamp
     * @return \DateTime
     */
    private function getDateForTimestamp(int $timestamp): \DateTime
    {
        $date = new \DateTime;
        $date->setTimestamp($timestamp) ;

        return $date;
    }

    /**
     * @param array $dataArray
     * @return WeatherRecord
     */
    private function getWeatherRecord(array $dataArray): WeatherRecord
    {
        $weather = new WeatherRecord();

        $weather->setDate($this->getDateForTimestamp($dataArray['dt']));
        $weather->setSunrise($this->getDateForTimestamp($dataArray['sys']['sunrise']));
        $weather->setSunset($this->getDateForTimestamp($dataArray['sys']['sunset']));

        return $weather;
    }

    /**
     * @param array $dataArray
     * @return AirCondition
     */
    private function getAirCondition(array $dataArray): AirCondition
    {
        $airCondition = new AirCondition();
        $airCondition->setClouds($dataArray['clouds']['all']);
        $airCondition->setHumidity($dataArray['main']['humidity']);
        $airCondition->setMaxTemp($dataArray['main']['temp_max']);
        $airCondition->setMinTemp($dataArray['main']['temp_min']);
        $airCondition->setPressure($dataArray['main']['pressure']);

        return $airCondition;
    }

    /**
     * @param array $dataArray
     * @return City
     */
    private function getCity(array $dataArray): City
    {
        $city = new City();
        $city->setName($dataArray['name']);
        $city->setCountryCode($dataArray['sys']['country']);
        $city->setLat($dataArray['coord']['lat']);
        $city->setLon($dataArray['coord']['lon']);

        return $city;
    }

    /**
     * @param array $dataArray
     * @return WindCondition
     */
    private function getWindCondition(array $dataArray): WindCondition
    {
        $wind = new WindCondition();
        $wind->setSpeed($dataArray['wind']['speed'] ?? null);
        $wind->setDirection($dataArray['wind']['deg'] ?? null);

        return $wind;
    }

    /**
     * @param array $rawData
     * @return WeatherCondition
     */
    private function getWeatherCondition(array $rawData): WeatherCondition
    {
        $weatherCondition = new WeatherCondition();
        $weatherCondition->setDescription($rawData['description']);

        return $weatherCondition;
    }
}