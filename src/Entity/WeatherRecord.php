<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class WeatherRecord
 * @package App\Entity
 *
 * @ORM\Entity(readOnly=true, repositoryClass="\App\Repository\WeatherRecordRepository")
 * @ORM\Table(
 *     name="weather_records",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="weather_record_unique", columns={"city_id", "date"} )
 *     }
 * )
 */
class WeatherRecord
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
     * @var City
     * @ORM\ManyToOne(targetEntity="City", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @Groups({"public"})
     */
    protected $city;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"public"})
     */
    protected $date;

    /**
     * @var WeatherCondition[]
     * @ORM\OneToMany(targetEntity="WeatherCondition", mappedBy="weatherRecord", cascade={"persist"}, fetch="EAGER")
     * @Groups({"public"})
     */
    protected $weatherConditions;

    /**
     * @var AirCondition
     * @ORM\OneToOne(targetEntity="AirCondition", mappedBy="weatherRecord", cascade={"persist"}, fetch="EAGER")
     * @Groups({"public"})
     */
    protected $airCondition;

    /**
     * @var WindCondition
     * @ORM\OneToOne(targetEntity="WindCondition", mappedBy="weatherRecord", cascade={"persist"}, fetch="EAGER")
     * @Groups({"public"})
     */
    protected $wind;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"public"})
     */
    protected $sunrise;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"public"})
     */
    protected $sunset;

    public function __construct()
    {
        $this->weatherConditions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSunrise(): ?\DateTimeInterface
    {
        return $this->sunrise;
    }

    public function setSunrise(\DateTimeInterface $sunrise): self
    {
        $this->sunrise = $sunrise;

        return $this;
    }

    public function getSunset(): ?\DateTimeInterface
    {
        return $this->sunset;
    }

    public function setSunset(\DateTimeInterface $sunset): self
    {
        $this->sunset = $sunset;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|WeatherCondition[]
     */
    public function getWeatherConditions(): Collection
    {
        return $this->weatherConditions;
    }

    public function addWeatherCondition(WeatherCondition $weatherCondition): self
    {
        if (!$this->weatherConditions->contains($weatherCondition)) {
            $this->weatherConditions[] = $weatherCondition;
            $weatherCondition->setWeatherRecord($this);
        }

        return $this;
    }

    public function removeWeatherCondition(WeatherCondition $weatherCondition): self
    {
        if ($this->weatherConditions->contains($weatherCondition)) {
            $this->weatherConditions->removeElement($weatherCondition);
            // set the owning side to null (unless already changed)
            if ($weatherCondition->getWeatherRecord() === $this) {
                $weatherCondition->setWeatherRecord(null);
            }
        }

        return $this;
    }

    public function getAirCondition(): ?AirCondition
    {
        return $this->airCondition;
    }

    public function setAirCondition(?AirCondition $airCondition): self
    {
        $this->airCondition = $airCondition;

        // set (or unset) the owning side of the relation if necessary
        $newWeatherRecord = $airCondition === null ? null : $this;
        if ($newWeatherRecord !== $airCondition->getWeatherRecord()) {
            $airCondition->setWeatherRecord($newWeatherRecord);
        }

        return $this;
    }

    public function getWind(): ?WindCondition
    {
        return $this->wind;
    }

    public function setWind(?WindCondition $wind): self
    {
        $this->wind = $wind;

        // set (or unset) the owning side of the relation if necessary
        $newWeatherRecord = $wind === null ? null : $this;
        if ($newWeatherRecord !== $wind->getWeatherRecord()) {
            $wind->setWeatherRecord($newWeatherRecord);
        }

        return $this;
    }
}