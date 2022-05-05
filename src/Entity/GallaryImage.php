<?php

// namespace App\Entity;

// use App\Repository\GallaryImageRepository;
// use Doctrine\ORM\Mapping as ORM;

// /**
//  * @ORM\Entity(repositoryClass=GallaryImageRepository::class)
//  */
// class GallaryImage
// {
//     /**
//      * @ORM\Id
//      * @ORM\GeneratedValue
//      * @ORM\Column(type="integer")
//      */
//     private $id;

//     /**
//      * @ORM\Column(type="string", length=255)
//      */
//     private $name;

//     /**
//      * @ORM\Column(type="integer")
//      */
//     private $trickId;

//     /**
//      * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="gallaryImages")
//      * @ORM\JoinColumn(nullable=false)
//      */
//     private $Trick;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getName(): ?string
//     {
//         return $this->name;
//     }

//     public function setName(string $name): self
//     {
//         $this->name = $name;

//         return $this;
//     }

//     public function getTrickId(): ?int
//     {
//         return $this->trickId;
//     }

//     public function setTrickId(int $trickId): self
//     {
//         $this->trickId = $trickId;

//         return $this;
//     }

// }
