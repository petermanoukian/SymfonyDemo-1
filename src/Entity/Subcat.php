<?php

namespace App\Entity;

use App\Repository\SubcatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubcatRepository::class)]
#[ORM\Table(name: 'subcat')]
#[ORM\UniqueConstraint(name: 'uniq_subcat_cat_name', columns: ['catid', 'name'])]
class Subcat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The Foreign Key link to Category
    #[ORM\ManyToOne(targetEntity: Cat::class)]
    #[ORM\JoinColumn(name: 'catid', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Cat $cat = null;

    #[ORM\Column(length: 255)]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCat(): ?Cat
    {
        return $this->cat;
    }

    public function setCat(?Cat $cat): static
    {
        $this->cat = $cat;
        return $this;
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
}