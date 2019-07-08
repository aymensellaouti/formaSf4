<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StudentFixture extends Fixture implements FixtureGroupInterface
{
    const STUDENT_STATUS = ['Etudiant', 'employée', 'chomeur'];

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i<3; $i++){
            $student = new Student();
            $student->setName("student $i");
            $student->setEtablissement("etablissement $i");
            $student->setStatut(self::STUDENT_STATUS[rand(0,count(self::STUDENT_STATUS)-1)]);
            $manager->persist($student);
            $user = new User();
            $user->setEmail("student$i@gmail.com");
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    'secret'
                )
            );
            $user->setStudent($student);
            $user->setRoles(['ROLE_STUDENT']);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['groupeStudent'];
    }
}
