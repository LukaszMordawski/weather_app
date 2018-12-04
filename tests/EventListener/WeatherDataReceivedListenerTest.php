<?php

namespace App\EventListener;

use App\Entity\AirCondition;
use App\Entity\City;
use App\Entity\WeatherCondition;
use App\Entity\WeatherRecord;
use App\Entity\WindCondition;
use App\Event\WeatherDataReceivedEvent;
use App\Repository\CityRepository;
use App\Repository\WeatherRecordRepository;
use App\Service\Weather\WeatherCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MatthiasMullie\Scrapbook\KeyValueStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WeatherDataReceivedListenerTest extends TestCase
{
    /** @var MockObject|WeatherRecordRepository $weatherRecordRepository */
    private $weatherRecordRepository;


    /** @var MockObject|CityRepository $cityRepository */
    private $cityRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    /** @var WeatherDataReceivedListener */
    private $listener;

    public function setUp()
    {
        $this->weatherRecordRepository = $this->getMockBuilder(WeatherRecordRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cityRepository = $this->getMockBuilder(CityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMockForAbstractClass();

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->with($this->logicalOr(WeatherRecord::class, City::class))
            ->willReturnMap([
                [ WeatherRecord::class, $this->weatherRecordRepository ],
                [ City::class, $this->cityRepository ]
            ]);

        $this->listener = new WeatherDataReceivedListener(
            $this->entityManager
        );
    }

    public function testOnWeatherDataReceived_WeatherRecordNorCityDoesNotExist_WeatherRecordIsStoredAndCacheIsCleared()
    {
        $weatherRecord = new WeatherRecord();

        $city = new City();
        $city->setCountryCode('PL');
        $city->setName('Krakow');

        $airCondition = new AirCondition();
        $weatherCondition = new WeatherCondition();
        $windCondition = new WindCondition();

        $weatherRecord->setWind($windCondition);
        $weatherRecord->addWeatherCondition($weatherCondition);
        $weatherRecord->setAirCondition($airCondition);
        $weatherRecord->setCity($city);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($weatherRecord);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->cityRepository->expects($this->any())
            ->method('findOneBy')
            ->with([
                'countryCode' => 'PL',
                'name' => 'Krakow'
            ])
            ->willReturn(null);

        $this->listener->onWeatherDataReceived(
            new WeatherDataReceivedEvent(
                $weatherRecord
            )
        );
    }

    public function testOnWeatherDataReceived_CityDoesExist()
    {
        $weatherRecord = new WeatherRecord();

        $city = new City();
        $city->setCountryCode('PL');
        $city->setName('Krakow');

        $databaseCity = new City();
        $databaseCity->setCountryCode('PL');
        $databaseCity->setName('Krakow');

        $airCondition = new AirCondition();
        $weatherCondition = new WeatherCondition();
        $windCondition = new WindCondition();

        $weatherRecord->setWind($windCondition);
        $weatherRecord->addWeatherCondition($weatherCondition);
        $weatherRecord->setAirCondition($airCondition);
        $weatherRecord->setCity($city);
        $weatherRecord->setDate(new \Datetime('2018-12-01'));

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($weatherRecord);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->cityRepository->expects($this->any())
            ->method('findOneBy')
            ->with([
                'countryCode' => 'PL',
                'name' => 'Krakow'
            ])
            ->willReturn($databaseCity);

        $this->weatherRecordRepository->expects($this->any())
            ->method('countByCityAndDate')
            ->with(
                $city,
                $this->callback(function(\DateTime $date) {
                    return $date->format('Y-m-d') === '2018-12-01';
                })
            )
            ->willReturn(0);

        $this->listener->onWeatherDataReceived(
            new WeatherDataReceivedEvent(
                $weatherRecord
            )
        );
    }

    public function testOnWeatherDataReceived_WeatherRecordExist_DataIsNotPersisted()
    {
        $weatherRecord = new WeatherRecord();

        $city = new City();
        $city->setCountryCode('PL');
        $city->setName('Krakow');

        $databaseCity = new City();
        $databaseCity->setCountryCode('PL');
        $databaseCity->setName('Krakow');

        $airCondition = new AirCondition();
        $weatherCondition = new WeatherCondition();
        $windCondition = new WindCondition();

        $weatherRecord->setWind($windCondition);
        $weatherRecord->addWeatherCondition($weatherCondition);
        $weatherRecord->setAirCondition($airCondition);
        $weatherRecord->setCity($city);
        $weatherRecord->setDate(new \Datetime('2018-12-01'));

        $this->entityManager->expects($this->never())
            ->method('persist');

        $this->entityManager->expects($this->never())
            ->method('flush');

        $this->cityRepository->expects($this->any())
            ->method('findOneBy')
            ->with([
                'countryCode' => 'PL',
                'name' => 'Krakow'
            ])
            ->willReturn($databaseCity);

        $this->weatherRecordRepository->expects($this->any())
            ->method('countByCityAndDate')
            ->with(
                $databaseCity,
                $this->callback(function(\DateTime $date) {
                    return $date->format('Y-m-d') === '2018-12-01';
                })
            )
            ->willReturn(1);

        $this->listener->onWeatherDataReceived(
            new WeatherDataReceivedEvent(
                $weatherRecord
            )
        );
    }
}