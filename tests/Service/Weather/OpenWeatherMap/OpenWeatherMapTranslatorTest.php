<?php

namespace tests\Service\Weather\OpenWeatherMap;

use App\Entity\AirCondition;
use App\Entity\WeatherRecord;
use App\Entity\WindCondition;
use App\Service\Weather\OpenWeatherMap\OpenWeatherMapTranslator;
use PHPUnit\Framework\TestCase;

class OpenWeatherMapTranslatorTest extends TestCase
{
    /**
     * @var OpenWeatherMapTranslator
     */
    private $translator;

    public function setUp()
    {
        $this->translator = new OpenWeatherMapTranslator();
    }

    public function testTranslateResult_HappyPath()
    {
        $results = <<<EOT
{
    "coord":{"lon":19.94,"lat":50.06},
    "weather":[
        {"id":500,"main":"Rain","description":"light rain","icon":"10n"},
        {"id":701,"main":"Mist","description":"mist","icon":"50n"}
    ],
    "base":"stations",
    "main":{"temp":274.15,"pressure":1007,"humidity":72,"temp_min":274.15,"temp_max":274.15},
    "visibility":2500,
    "wind":{"speed":1,"deg":310},
    "clouds":{"all":75},
    "dt":1543788000,
    "sys":{"type":1,"id":1701,"message":0.0028,"country":"PL","sunrise":1543731568,"sunset":1543761610},
    "id":3094802,
    "name":"Krakow",
    "cod":200
}
EOT;

        $weatherRecord = $this->translator->translateResult(
            $results
        );

        $this->assertInstanceOf(WeatherRecord::class, $weatherRecord);

        $city = $weatherRecord->getCity();
        $this->assertEquals("Krakow", $city->getName());
        $this->assertEquals("pl", strtolower($city->getCountryCode()));
        $this->assertEquals("19.94", $city->getLon());
        $this->assertEquals("50.06", $city->getLat());

        $date = $weatherRecord->getDate();
        $this->assertInstanceOf(\DateTime::class, $date);
        $this->assertEquals("2018-12-02", $date->format("Y-m-d"));

        $weatherConditions = $weatherRecord->getWeatherConditions();
        $this->assertCount(2, $weatherConditions);
        $this->assertEquals("light rain", $weatherConditions[0]->getDescription());
        $this->assertEquals("mist", $weatherConditions[1]->getDescription());

        $airCondition = $weatherRecord->getAirCondition();
        $this->assertInstanceOf(AirCondition::class, $airCondition);
        $this->assertEquals(1007, $airCondition->getPressure());
        $this->assertEquals(72, $airCondition->getHumidity());
        $this->assertEquals(274.15, $airCondition->getMinTemp());
        $this->assertEquals(274.15, $airCondition->getMaxTemp());
        $this->assertEquals(75, $airCondition->getClouds());

        $windCondition = $weatherRecord->getWind();
        $this->assertInstanceOf(WindCondition::class, $windCondition);
        $this->assertEquals(1, $windCondition->getSpeed());
        $this->assertEquals(310, $windCondition->getDirection());

        $sunriseDate = $weatherRecord->getSunrise();
        $this->assertEquals("06:19", $sunriseDate->format('H:i'));

        $sunsetDate = $weatherRecord->getSunset();
        $this->assertEquals("14:40", $sunsetDate->format('H:i'));
    }

    /**
     * @expectedException \App\Service\Weather\Exception\MalformedApiResponseException
     */
    public function testTranslateResults_ResultsIsIncorrectJSON_ExceptionIsThrown()
    {
        $results = '<<sdajdasja>>>;';
        $this->translator->translateResult($results);
    }

    public function testTranslateResults_NoWindDataInResult_WindEntityHasNoData()
    {
        $results = <<<EOT
{
    "coord":{"lon":19.94,"lat":50.06},
    "weather":[
        {"id":500,"main":"Rain","description":"light rain","icon":"10n"},
        {"id":701,"main":"Mist","description":"mist","icon":"50n"}
    ],
    "base":"stations",
    "main":{"temp":274.15,"pressure":1007,"humidity":72,"temp_min":274.15,"temp_max":274.15},
    "visibility":2500,
    "clouds":{"all":75},
    "dt":1543788000,
    "sys":{"type":1,"id":1701,"message":0.0028,"country":"PL","sunrise":1543731568,"sunset":1543761610},
    "id":3094802,
    "name":"Krakow",
    "cod":200
}
EOT;

        $weatherRecord = $this->translator->translateResult(
            $results
        );

        $this->assertInstanceOf(WeatherRecord::class, $weatherRecord);

        $windCondition = $weatherRecord->getWind();
        $this->assertInstanceOf(WindCondition::class, $windCondition);
        $this->assertEquals(null, $windCondition->getSpeed());
        $this->assertEquals(null, $windCondition->getDirection());
    }
}