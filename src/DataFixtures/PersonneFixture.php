<?php

namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class PersonneFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /**
         * @param
         */
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i < 6; $i++) {
            $personne = new Personne();
            $personne->setName($faker->name);
            $personne->setFirstname($faker->firstName);
            $personne->setAge($faker->numberBetween(1,99));
            $personne->setCin($faker->randomNumber(8));
            $imgCompletPath = $faker->image('public/build/img', 640, 480);
            $imgPath = explode('img\\', $imgCompletPath);
            $personne->setPath($imgPath[1]);
            $manager->persist($personne);
        }
        $manager->flush();
    }
}
