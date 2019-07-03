<?php

namespace App\DataFixtures;

use App\Entity\Formateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class FormateurFixture extends Fixture
{
    const FIELDS = array(
        'Informatique',
        'Ressources humaines',
        'marketing',
        'Hotelerie'
    );
    public function load(ObjectManager $manager)
    {
         $faker = Faker\Factory::create('fr_FR');
         for ($i= 0; $i<6; $i++) {
             $formateur = new Formateur();
             $formateur->setName($faker->firstName.' '.$faker->name);
             $formateur->setField(self::FIELDS[$faker->numberBetween(0, count(self::FIELDS) - 1 )]);
             $manager->persist($formateur);
         }
         $manager->flush();
    }
}
