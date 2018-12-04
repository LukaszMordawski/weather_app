<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class WindCondition
 * @package App\Entity
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="wind_conditions",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="weather_record_unique", columns={"weather_record_id"} )
 *     }
 * )
 */
class WindCondition
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
     * @ORM\OneToOne(targetEntity="WeatherRecord", inversedBy="wind")
     * @ORM\JoinColumn(name="weather_record_id", referencedColumnName="id")
     * @Groups({"private"})
     */
    protected $weatherRecord;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=4, scale=1)
     * @Groups({"public"})
     */
    protected $speed;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"public"})
     */
    protected $direction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpeed(): ?string
    {
        return $this->speed;
    }

    public function setSpeed(?string $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getDirection(): ?int
    {
        return $this->direction;
    }

    public function setDirection(?int $direction): self
    {
        $this->direction = $direction;

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