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

    // /**
    //  * @ORM\Column(type="string", length=70)
    //  */
    // private $userId;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="Trick", orphanRemoval=true,cascade={"persist"})
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class,  mappedBy="Trick")
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getUserId(): ?string
    // {
    //     return $this->userId;
    // }

    // public function setUserId(string $userId): self
    // {
    //     $this->userId = $userId;

    //     return $this;
    // }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Videos>
     */
    public function getVideos(): ?Videos
    {
        return $this->videos;
    }

    public function setVideos(?Videos $videos): self
    {
        $this->videos = $videos;

        return $this;
    }
    public function addVideo(Videos $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }
    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): ?Comments
    {
        return $this->comments;
    }

    public function setComments(?Comments $comments): self
    {
        $this->comments = $comments;

        return $this;
    }
}
