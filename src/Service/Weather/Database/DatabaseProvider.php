<?php

namespace App\Service\Weather\Database;

use App\Repository\WeatherRecordRepository;
use App\Service\Weather\WeatherProviderInterface;

/**
 * Class DatabaseProvider
 * @package App\Service\Weather\Database
 */
final class DatabaseProvider implements WeatherProviderInterface
{
    /** @var WeatherRecordRepository  */
    private $repository;

    /**
     * DatabaseProvider constructor.
     * @param WeatherRecordRepository $repository
     */
    public function __construct(WeatherRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function fetchByCity(string $city): array
    {
        return $this->repository->getByCity($city);
    }
}