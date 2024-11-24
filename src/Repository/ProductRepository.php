<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getPaginatedProductsQuery($limit): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->addSelect('i')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getPaginatedProductsByCategoryQuery($categoryName)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->join('p.category', 'c')
            ->where('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->getQuery();
    }

    public function getPaginatedProductsByCategoryAndNameQuery($categoryName, $name)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->join('p.category', 'c')
            ->where('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->andWhere('p.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
    }

    public function getPaginatedProductsByCategoryWithLimitQuery($categoryName, $limit): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i')
            ->join('p.category', 'c')
            ->where('c.name = :name')
            ->setParameter('name', $categoryName)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
