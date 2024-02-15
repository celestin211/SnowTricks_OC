<?php

namespace App\Repository;

use App\Entity\DocumentAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentAccueil>
 *
 * @method DocumentAccueil|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentAccueil|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentAccueil[]    findAll()
 * @method DocumentAccueil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentAccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentAccueil::class);
    }

//    /**
//     * @return DocumentAccueil[] Returns an array of DocumentAccueil objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentAccueil
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
