<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DetailRepository")
 */
class Detail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $order_type;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $estate_type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $structure;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $heating;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $balcony;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $build;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lift;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $estate_condition;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $view;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $handicap;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Estate", inversedBy="detail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $estate;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderType(): ?string
    {
        return $this->order_type;
    }

    public function setOrderType(string $order_type): self
    {
        $this->order_type = $order_type;

        return $this;
    }

    public function getEstateType(): ?string
    {
        return $this->estate_type;
    }

    public function setEstateType(string $estate_type): self
    {
        $this->estate_type = $estate_type;

        return $this;
    }

    public function getStructure(): ?string
    {
        return $this->structure;
    }

    public function setStructure(string $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    public function getHeating(): ?string
    {
        return $this->heating;
    }

    public function setHeating(string $heating): self
    {
        $this->heating = $heating;

        return $this;
    }

    public function getBalcony(): ?bool
    {
        return $this->balcony;
    }

    public function setBalcony(bool $balcony): self
    {
        $this->balcony = $balcony;

        return $this;
    }

    public function getBuild(): ?\DateTimeInterface
    {
        return $this->build;
    }

    public function setBuild(?\DateTimeInterface $build): self
    {
        $this->build = $build;

        return $this;
    }

    public function getLift(): ?bool
    {
        return $this->lift;
    }

    public function setLift(?bool $lift): self
    {
        $this->lift = $lift;

        return $this;
    }

    public function getEstateCondition(): ?string
    {
        return $this->estate_condition;
    }

    public function setEstateCondition(string $estate_condition): self
    {
        $this->estate_condition = $estate_condition;

        return $this;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(string $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function setView(?string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getHandicap(): ?bool
    {
        return $this->handicap;
    }

    public function setHandicap(?bool $handicap): self
    {
        $this->handicap = $handicap;

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
}
