<?php

namespace App\EventListener;

use App\Entity\City;
use App\Entity\WeatherRecord;
use App\Event\WeatherDataReceivedEvent;
use App\Service\Weather\Cache\WeatherCache;
use Doctrine\ORM\EntityManagerInterface;
use MatthiasMullie\Scrapbook\KeyValueStore;

/**
 * Class WeatherDataReceivedListener
 * @package App\EventListener
 */
class WeatherDataReceivedListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param WeatherDataReceivedEvent $event
     */
    public function onWeatherDataReceived(WeatherDataReceivedEvent $event): void
    {
        $weatherRecord = $event->getData();

        $cityRepository = $this->entityManager->getRepository(City::class);
        $databaseCity = $cityRepository->findOneBy([
            'countryCode' => $weatherRecord->getCity()->getCountryCode(),
            'name' => $weatherRecord->getCity()->getName()
        ]);

        if ($this->shouldStoreRecord($weatherRecord, $databaseCity)) {
            $this->storeRecord($weatherRecord, $databaseCity);
        }
    }

    /**
     * @param WeatherRecord $weatherRecord
     * @param City|null $city
     * @return bool
     */
    private function shouldStoreRecord(WeatherRecord $weatherRecord, City $databaseCity = null): bool
    {
        if ($databaseCity === null) {
            return true;
        }

        $weatherRecordRepository = $this->entityManager->getRepository(WeatherRecord::class);
        $weatherRecordCount = $weatherRecordRepository->countByCityAndDate(
            $databaseCity,
            $weatherRecord->getDate()
        );

        if ($weatherRecordCount > 0) {
            return false;
        }

        return true;
    }

    /**
     * @param WeatherRecord $weatherRecord
     * @param City|null $databaseCity
     */
    private function storeRecord(WeatherRecord $weatherRecord, City $databaseCity = null): void
    {
        if ($databaseCity !== null) {
            $weatherRecord->setCity($databaseCity);
        }
        $this->entityManager->persist($weatherRecord);
        $this->entityManager->flush();
    }
}