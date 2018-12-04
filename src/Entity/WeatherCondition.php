<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class City
 * @package App\Entity
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(
 *     name="weather_conditions",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="weather_record_unique", columns={"weather_record_id", "description"} )
 *     }
 * )
 */
class WeatherCondition
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
     * @ORM\ManyToOne(targetEntity="WeatherRecord", inversedBy="weatherConditions")
     * @ORM\JoinColumn(name="weather_record_id", referencedColumnName="id")
     * @Groups({"private"})
     */
    protected $weatherRecord;

    /**
     * @var string
     * @ORM\Column(type="string", length=30)
     * @Groups({"public"})
     */
    protected $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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