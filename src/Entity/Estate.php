<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EstateRepository")
 */
class Estate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rooms;

    /**
     * @ORM\Column(type="datetime")
     */
    private $upload;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="`comment`")
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="array")
     */
    private $images = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $publicId;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $order_type;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $estate_type;

    /**
     * @ORM\Column(type="string", length=80)
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $build;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $lift;

    /**
     * @ORM\Column(type="string", length=50, name="`condition`")
     */
    private $condition;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $floor;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="`view`")
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permission;


    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getRooms(): ?string
    {
        return $this->rooms;
    }

    public function setRooms(string $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getUpload(): ?\DateTimeInterface
    {
        return $this->upload;
    }

    public function setUpload(\DateTimeInterface $upload): self
    {
        $this->upload = $upload;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    public function setPublicId(string $publicId): self
    {
        $this->publicId = $publicId;

        return $this;
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

    public function getPermission(): ?bool
    {
        return $this->permission;
    }

    public function setPermission(?bool $permission): self
    {
        $this->permission = $permission;

        return $this;
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

    public function setBalcony(?bool $balcony): self
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

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(string $estate_condition): self
    {
        $this->condition = $estate_condition;

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
}
