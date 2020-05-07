<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MappingRepository")
 */
class Mapping
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $enum;

    /**
     * @ORM\Column(type="array")
     */
    private $value = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnum(): ?string
    {
        return $this->enum;
    }

    public function setEnum(string $enum): self
    {
        $this->enum = $enum;

        return $this;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }
}
