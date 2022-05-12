<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMainImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameGallaryImages;

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

    public function getIsMainImage(): ?bool
    {
        return $this->isMainImage;
    }

    public function setIsMainImage(?bool $isMainImage): self
    {
        $this->isMainImage = $isMainImage;

        return $this;
    }

    public function getNameGallaryImages(): ?string
    {
        return $this->nameGallaryImages;
    }

    public function setNameGallaryImages(?string $nameGallaryImages): self
    {
        $this->nameGallaryImages = $nameGallaryImages;

        return $this;
    }
}
