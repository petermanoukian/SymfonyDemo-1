<?php

namespace App\Entity;

use App\Repository\CatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;




#[ORM\Entity(repositoryClass: CatRepository::class)]
class Cat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $des = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dess = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filer = null;


    #[ORM\OneToMany(mappedBy: 'cat', targetEntity: Subcat::class)]
    private Collection $subcats;

    public function __construct()
    {
        // Initialize the collection so count() doesn't fail on new entities
        $this->subcats = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): static
    {
        $this->des = $des;
        return $this;
    }

    public function getDess(): ?string
    {
        return $this->dess;
    }

    public function setDess(?string $dess): static
    {
        $this->dess = $dess;
        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;
        return $this;
    }

    public function getImg2(): ?string
    {
        return $this->img2;
    }

    public function setImg2(?string $img2): static
    {
        $this->img2 = $img2;
        return $this;
    }

    public function getFiler(): ?string
    {
        return $this->filer;
    }

    public function setFiler(?string $filer): static
    {
        $this->filer = $filer;
        return $this;
    }

    public function getSubcats(): Collection
    {
        return $this->subcats;
    }


}