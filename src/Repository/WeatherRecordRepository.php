<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\WeatherRecord;
use Doctrine\ORM\EntityRepository;
use steevanb\DoctrineReadOnlyHydrator\Hydrator\ReadOnlyHydrator;
use steevanb\DoctrineReadOnlyHydrator\Hydrator\SimpleObjectHydrator;

/**
 * Class WeatherRecordRepository
 * @package App\Repository
 */
class WeatherRecordRepository extends EntityRepository
{
    /**
     * @param City $city
     * @param \DateTime $date
     * @return int
     */
    public function countByCityAndDate(
        City $city,
        \DateTime $date
    )
    {
        $result = $this->createQueryBuilder('w')
            ->select('COUNT(w.id) AS cnt')
            ->where('w.city = :city')
            ->andWhere('w.date = :date')
            ->setParameter('city', $city->getId())
            ->setParameter('date', $date->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getArrayResult();

        return $result[0]['cnt'] ?? 0;
    }

    /**
     * @param string $city
     * @return WeatherRecord[]
     */
    public function getByCity(string $city): array
    {
        $records = $this->createQueryBuilder('wr')
            ->select('wr, c, ac, w, wc')
            ->join('wr.city', 'c')
            ->join('wr.airCondition', 'ac')
            ->join('wr.wind', 'w')
            ->join('wr.weatherConditions', 'wc')
            ->where('c.name = :city')
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult(ReadOnlyHydrator::HYDRATOR_NAME);

        return $records;
    }
}