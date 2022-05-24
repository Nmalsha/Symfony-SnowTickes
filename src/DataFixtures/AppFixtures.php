<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager): void
    {

        $user = new User();

        $hash = $this->hasher->hashPassword($user, 'test123');
        $user->setFirstName('David')
            ->setSecondName('Levin')
            ->setEmail('david@gmail.com')
            ->setprofieImage('https://via.placeholder.com/120.png/09f/fff')
            ->setPassword($hash);

        $manager->persist($user);

        for ($u = 1; $u <= 10; $u++) {
            $tricks = new Trick();
            $tricks->setTrickName("Titre_n°$u")
                ->setDescription("Contenu de l'article n°$u")
                ->setslug("Titre_n°$u")
                ->setCreatedOn(new \DateTime())
                ->setCategorie('Cat 1');
            $tricks->setUser($user);
            $manager->persist($tricks);

            // Ajout des commentaires à l'article
            for ($k = 1; $k <= mt_rand(1, 4); $k++) {

                $comment = new Comments();

                $comment->setContent("Commentaire de l'article n°$u")
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setTrick($tricks);
                $comment->setUser($user);
                $manager->persist($comment);

                // Ajout des images à l'article
                for ($l = 1; $l <= mt_rand(1, 2); $l++) {

                    $image = new Images();
                    $image->setName('https://via.placeholder.com/120.png/09f/fff')
                        ->setIsMainImage(0)

                        ->setTrick($tricks);

                    $manager->persist($image);

                    // Ajout des vidéos à l'article
                    $video = new Videos();
                    $video->setUrl('https://www.youtube.com/embed/XUFLq6dKQok')
                        ->setTrick($tricks);

                    $manager->persist($video);

                }

            }

        }

        $manager->flush();
    }

}
