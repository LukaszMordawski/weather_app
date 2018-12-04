<?php

namespace tests\Service\Weather\Database;

use App\Entity\WeatherRecord;
use App\Repository\WeatherRecordRepository;
use App\Service\Weather\Database\DatabaseProvider;
use PHPUnit\Framework\TestCase;

class DatabaseProviderTest extends TestCase
{
    public function testFetchByCity()
    {
        $repository = $this->getMockBuilder(WeatherRecordRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = new DatabaseProvider($repository);

        $repository->expects($this->any())
            ->method('getByCity')
            ->with('London')
            ->willReturn([ new WeatherRecord() ]);

        $data = $provider->fetchByCity('London');

        $this->assertTrue(is_array($data), '$data expected to be array');
        $this->assertCount(1, $data);
        $this->assertInstanceOf(WeatherRecord::class, $data[0]);
    }
}