<?php

namespace App\Repository;

use App\Entity\BestProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BestProducts>
 *
 * @method BestProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method BestProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method BestProducts[]    findAll()
 * @method BestProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BestProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BestProducts::class);
    }

//    /**
//     * @return BestProducts[] Returns an array of BestProducts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BestProducts
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
