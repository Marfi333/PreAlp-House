<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $county;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Estate", inversedBy="address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $estate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permission;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function setCounty(string $county): self
    {
        $this->county = $county;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getEstate(): ?Estate
    {
        return $this->estate;
    }

    public function setEstate(Estate $estate): self
    {
        $this->estate = $estate;

        return $this;
    }

    public function getPermission(): ?bool
    {
        return $this->permission;
    }

    public function setPermission(?bool $permission): self
    {
        $this->permission = $permission;

        return $this;
    }
}
