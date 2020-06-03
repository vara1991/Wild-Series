<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker  =  Faker\Factory::create('fr_FR');
        for ($i = 1; $i < 51; $i++){
            $episode = new Episode();
            $slugify = new Slugify();
            $episode->setTitle($faker->sentence($nbWords = 5, $variableNbWords = true));
            $episode->setNumber($i);
            $episode->setSynopsis($faker->text);
            $episode->setSeason($this->getReference('season_'.$faker->numberBetween($min = 0, $max = 19)));
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $manager->persist($episode);
        }
        $manager->flush();
    }
}