<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker  =  Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++){
            $season = new Season();
            $season->setNumber($faker->numberBetween($min = 1, $max = 5));
            $season->setYear($faker->numberBetween($min = 1991, $max = 2020));
            $season->setDescription($faker->text);
            $season->setProgram($this->getReference('program_'.$faker->numberBetween($min = 0, $max = 5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();
    }
}
