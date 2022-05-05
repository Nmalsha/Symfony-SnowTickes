<?php

// namespace App\Entity;

// use App\Repository\GallaryImagesRepository;
// use Doctrine\ORM\Mapping as ORM;

// /**
//  * @ORM\Entity(repositoryClass=GallaryImagesRepository::class)
//  */
// class GallaryImages
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
