<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    const ACTORS = ['Andrew Lincoln', 'Norman Reedus', 'Lauren Cohan', 'Danai Gurira'];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker  =  Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++){
            $actor = new Actor();
            $slugify = new Slugify();
            $actor->setName($faker->name);
            $actor->setSlug($slugify->generate($actor->getName()));
            $actor->addProgram($this->getReference('program_'.$faker->numberBetween($min = 0, $max = 5)));
            $manager->persist($actor);
        }
        $manager->flush();
    }
}
