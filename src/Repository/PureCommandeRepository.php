<?php

namespace App\Repository;

use App\Entity\PureCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PureCommande>
 */
class PureCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PureCommande::class);
    }
    public function findByAnnonceId(int $annonceId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.pureAnnonce = :annonceId')
            ->setParameter('annonceId', $annonceId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return PureCommande[] Returns an array of PureCommande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PureCommande
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
