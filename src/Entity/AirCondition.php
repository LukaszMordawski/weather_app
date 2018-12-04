<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class AirCondition
 * @package App\Entity
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="air_conditions",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="weather_record_unique", columns={"weather_record_id"} )
 *     }
 * )
 */
class AirCondition
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"})
     * @Groups({"private"})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var WeatherRecord
     * @ORM\OneToOne(targetEntity="WeatherRecord", inversedBy="airCondition")
     * @ORM\JoinColumn(name="weather_record_id", referencedColumnName="id")
     * @Groups({"private"})
     */
    protected $weatherRecord;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     * @Groups({"public"})
     */
    protected $pressure;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     * @Groups({"public"})
     */
    protected $humidity;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Groups({"public"})
     */
    protected $minTemp;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Groups({"public"})
     */
    protected $maxTemp;

    /**
     * @var int
     * @ORM\Column(type="smallint")
     * @Groups({"public"})
     */
    protected $clouds;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPressure(): ?int
    {
        return $this->pressure;
    }

    public function setPressure(int $pressure): self
    {
        $this->pressure = $pressure;

        return $this;
    }

    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    public function setHumidity(int $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getMinTemp(): ?float
    {
        return $this->minTemp;
    }

    public function setMinTemp(float $minTemp): self
    {
        $this->minTemp = $minTemp;

        return $this;
    }

    public function getMaxTemp(): ?float
    {
        return $this->maxTemp;
    }

    public function setMaxTemp(float $maxTemp): self
    {
        $this->maxTemp = $maxTemp;

        return $this;
    }

    public function getClouds(): ?int
    {
        return $this->clouds;
    }

    public function setClouds(int $clouds): self
    {
        $this->clouds = $clouds;

        return $this;
    }

    public function getWeatherRecord(): ?WeatherRecord
    {
        return $this->weatherRecord;
    }

    public function setWeatherRecord(?WeatherRecord $weatherRecord): self
    {
        $this->weatherRecord = $weatherRecord;

        return $this;
    }
}