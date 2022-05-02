<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 */
class Trick
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $trickName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $categorie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdOn;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="Trick", orphanRemoval=true,cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=GallaryImage::class, mappedBy="Trick", orphanRemoval=true,cascade={"persist"})
     */
    private $gallaryImages;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTrickName(): ?string
    {
        return $this->trickName;
    }

    public function setTrickName(string $trickName): self
    {
        $this->trickName = $trickName;

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

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
        }

        return $this;
    }
    // public function addUserId($userId): self
    // {
    //     if (!$this->userId->contains($userId)) {
    //         $this->userId[] = $userId;
    //         $userId->setTrick($this);
    //     }

    //     return $this;
    // }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, GallaryImages>
     */
    public function getGallaryImages(): ?string
    {
        return $this->gallaryImages;
    }

    public function setGallaryImages(?string $gallaryImages): self
    {
        $this->gallaryImages = $gallaryImages;

        return $this;
    }
    public function addGallaryImage(GallaryImages $gallaryImage): self
    {
        if (!$this->gallaryImages->contains($gallaryImage)) {
            $this->gallaryImages[] = $gallaryImage;
            $gallaryImage->setTrick($this);
        }

        return $this;
    }
}
