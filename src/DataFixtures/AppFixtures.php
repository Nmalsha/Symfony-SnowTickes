<?php

namespace App\DataFixtures;

use App\Entity\Comments;
use App\Entity\Images;
use App\Entity\Trick;
use App\Entity\Videos;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // // Créer 10 tricks
        // for ($u = 1; $u <= 10; $u++) {
        //     $tricks = new Trick();
        //     $tricks->setTrickName("titre de l'article n°$u")
        //         ->setDescription("Contenu de l'article n°$u")
        //         ->setslug('trick')
        //         ->setCreatedAt(new \DateTime())
        //         ->setCategorie('Cat 1');

        //     $manager->persist($tricks);

        //     $manager->flush();
        // }

        // Créer 10 tricks
        for ($u = 1; $u <= 10; $u++) {
            $tricks = new Trick();
            $tricks->setTrickName("Titre de l'article n°$u")
                ->setDescription("Contenu de l'article n°$u")
                ->setslug('trick')
                ->setCreatedAt(new \DateTime())
                ->setCategorie('Cat 1');

            $manager->persist($tricks);

            // Ajout des commentaires à l'article
            for ($k = 1; $k <= mt_rand(1, 4); $k++) {

                $comment = new Comments();

                $now = new \DateTime();

                $comment->setContent("Commentaire de l'article n°$u")
                    ->setCreatedAt($now)
                    ->setTrick($tricks);

                $manager->persist($comment);

                // Ajout des images à l'article
                for ($l = 1; $l <= mt_rand(1, 4); $l++) {

                    $image = new Images();
                    $image->setPath('https://via.placeholder.com/130.png/09f/fff')
                        ->setName('nom image $l')
                        ->setNameGallaryImages('nom image $l')
                        ->setTrick($tricks);

                    $manager->persist($image);

                    // Ajout des vidéos à l'article
                    for ($v = 1; $v <= mt_rand(1, 4); $v++) {

                        $video = new Videos();
                        $video->setUrl('https://via.placeholder.com/140.png/09f/fff')
                            ->setTrick($tricks);

                        $manager->persist($video);
                    }
                }
            }
        }

        $manager->flush();
    }

}
