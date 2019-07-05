<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Formation::class);
    }

     /**
      * @return Formation[] Returns an array of Formation objects
      */
        public function findByFormationState($value)
        {
            return $this->createQueryBuilder('f')
                ->andWhere('f.state = :val')
                ->setParameter('val', $value)
                ->orderBy('f.startDate', 'DESC')
                ->getQuery()
                ->getResult()
            ;
        }

        /**
         * @param $state
         * @return Formation[] Returns an array of Formation objects
         */
        public function getNotStartedFormationByState($state) {
            $qb = $this->createQueryBuilder('f');
            $qb = $this->getOrdredNotStartedFormation($qb);
            return $qb->andWhere('f.state = :state')
                      ->setParameter('state',$state)
                      ->getQuery()
                      ->getResult();
        }

    /**
     * @param $state
     * @return Formation[] Returns an array of Formation objects
     */
    public function getNotStartedFormation() {
        $qb = $this->createQueryBuilder('f');
        return $qb = $this->getOrdredNotStartedFormation($qb)
                          ->getQuery()
                          ->getResult();
    }

        private function getOrdredNotStartedFormation(QueryBuilder $qb) {
            return $qb->andWhere('f.startDate > :currentDate')
                      ->setParameter('currentDate', new \DateTime())
                      ->orderBy('f.startDate', 'DESC');
        }

    /*
    public function findOneBySomeField($value): ?Formation
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
