<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    /**
     * PersonneRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    /**
     * @param $min
     * @param $max
     * @return mixed
     */
    public function getPersonneByAge($min, $max) {

        $qb = $this->createQueryBuilder('p');
        $qb = $this->findByAge($qb, $min, $max);
        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param $min
     * @param $max
     * @return QueryBuilder
     */
    private function findByAge(QueryBuilder $qb, $min, $max) {
        if($min) {
            $qb->andWhere('p.age > :minAge')
                ->setParameter('minAge', $min);
        }
        if($max) {
            $qb->andWhere('p.age < :maxAge')
                ->setParameter('maxAge', $max);
        }
        return $qb;
    }
    // /**
    //  * @return Personne[] Returns an array of Personne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Personne
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
