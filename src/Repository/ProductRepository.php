<?php

namespace App\Repository;

use App\Entity\Product;
use App\Enum\ProductStatus;
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

    public function getFilteredProductsQuery(?string $categoryName, array $filters)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.images', 'i');

        if ($categoryName) {
            $qb->join('p.category', 'c')
            ->where('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName);
        }

        if (!empty($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        $availabilityConditions = [];
        if (!empty($filters['inStock'])) {
            $availabilityConditions[] = 'p.status = :inStock';
            $qb->setParameter('inStock', ProductStatus::AVAILABLE);
        }
        if (!empty($filters['preOrder'])) {
            $availabilityConditions[] = 'p.status = :preOrder';
            $qb->setParameter('preOrder', ProductStatus::PRE_ORDER);
        }
        if (!empty($filters['outOfStock'])) {
            $availabilityConditions[] = 'p.status = :outOfStock';
            $qb->setParameter('outOfStock', ProductStatus::OUT_OF_STOCK);
        }
        if (!empty($availabilityConditions)) {
            $qb->andWhere('(' . implode(' OR ', $availabilityConditions) . ')');
        }

        if (!empty($filters['category'])) {
            $qb->join('p.category', 'c')
               ->andWhere('c.name = :category')
               ->setParameter('category', $filters['category']);
        }

        if (!empty($filters['priceFrom'])) {
            $qb->andWhere('p.price >= :priceFrom')
               ->setParameter('priceFrom', $filters['priceFrom']);
        }

        if (!empty($filters['priceTo'])) {
            $qb->andWhere('p.price <= :priceTo')
               ->setParameter('priceTo', $filters['priceTo']);
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('p.status = :status')
               ->setParameter('status', $filters['status']);
        }

        // if (!empty($filters['sortBy'])) {
        //     [$field, $direction] = explode(', ', $filters['sortBy']);
        //     $field = strtolower($field);
        //     $qb->orderBy("p.$field", $direction);
        // }

        return $qb->getQuery();
    }

    public function getProductStatistics(): array
    {
        $entityManager = $this->getEntityManager();
        
        $qb = $entityManager->createQueryBuilder()
            ->select('c.name as category, COUNT(p.id) as count')
            ->from('App\Entity\Category', 'c')
            ->leftJoin('c.products', 'p')
            ->groupBy('c.name');

        $results = $qb->getQuery()->getResult();
        
        return array_column($results, 'count', 'category');
    }

    public function getAvailabilityRatio(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.status, COUNT(p.id) as count')
            ->groupBy('p.status');

        $results = $qb->getQuery()->getResult();
        
        $total = array_sum(array_column($results, 'count'));
        $ratio = [];
        
        foreach ($results as $result) {
            $status = strtolower($result['status']->value);
            $ratio[$status] = round(($result['count'] / $total) * 100, 2);
        }
        
        return $ratio;
    }

    public function save(Product $product): void
    {
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
    }

    public function remove(Product $product): void
    {
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
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
