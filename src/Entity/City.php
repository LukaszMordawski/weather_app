<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class City
 * @package App\Entity
 *
 * @ORM\Entity(readOnly=true, repositoryClass="\App\Repository\CityRepository")
 * @ORM\Table(
 *     name="cities"
 * )
 */
class City
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"}, unique=true)
     * @Groups({"private"})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     * @Groups({"public"})
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=2)
     * @Groups({"public"})
     */
    protected $countryCode;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=4, scale=2)
     * @Groups({"public"})
     */
    protected $lat;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"public"})
     */
    protected $lon;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setLat($lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function setLon($lon): self
    {
        $this->lon = $lon;

        return $this;
    }
}