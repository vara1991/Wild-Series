<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Création d’un utilisateur de type “auteur”
        $subscriber = new User();
        $subscriber->setUsername('user');
        $subscriber->setEmail('subscriber@monsite.com');
        $subscriber->setRoles(['ROLE_SUBSCRIBER']);
        $subscriber->setPassword($this->passwordEncoder->encodePassword($subscriber, 'subscriberpassword'));
        $subscriber->setBio('Je suis un user');
        $manager->persist($subscriber);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'adminpassword'));
        $admin->setBio('Je suis un admin');
        $manager->persist($admin);

        // Sauvegarde des 2 nouveaux utilisateurs :
        $manager->flush();

    }
}
