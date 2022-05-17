<?php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppUserFixtures extends Fixture
{
    protected $hasher;

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
        $manager->flush();
    }
}
