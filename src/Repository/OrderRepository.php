<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findLastOrders(int $limit): array
    {
        return $this->createQueryBuilder('o')
            ->select('o', 'u')
            ->join('o.user', 'u')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMonthlySalesForLastYear(): array
    {
        $startDate = new \DateTime('1 year ago');
        
        $results = $this->createQueryBuilder('o')
            ->select('o.createdAt', 'SUM(oi.quantity * oi.productPrice) as total')
            ->join('o.orderItems', 'oi')
            ->where('o.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('o.createdAt')
            ->getQuery()
            ->getResult();

        $groupedResults = [];
        foreach ($results as $result) {
            $month = $result['createdAt']->format('m');
            $year = $result['createdAt']->format('Y');
            $key = $year . '-' . $month;

            if (!isset($groupedResults[$key])) {
                $groupedResults[$key] = ['cmonth' => $month, 'cyear' => $year, 'total' => 0];
            }

            $groupedResults[$key]['total'] += $result['total'];
        }

        $groupedResults = array_values($groupedResults);


        $formattedData = [
            'labels' => [],
            'data' => [],
        ];

        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $currentDate = clone $startDate;
        $endDate = new \DateTime();
        
        while ($currentDate <= $endDate) {
            $monthKey = $monthNames[(int)$currentDate->format('n')] . ' ' . $currentDate->format('Y');
            $formattedData['labels'][] = $monthKey;
            
            $found = false;
            foreach ($groupedResults as $result) {
                if ($result['cyear'] == $currentDate->format('Y') && $result['cmonth'] == $currentDate->format('n')) {
                    $formattedData['data'][] = (float)$result['total'];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $formattedData['data'][] = 0;
            }
            
            $currentDate->modify('+1 month');
        }

        return $formattedData;
    }

    public function getTotalRevenue(\DateTime $startDate = null, \DateTime $endDate = null): float
    {
        $qb = $this->createQueryBuilder('o')
            ->select('SUM(oi.quantity * oi.productPrice) as total')
            ->join('o.orderItems', 'oi')
            ->where('o.status = :delivered')
            ->setParameter('delivered', 'DELIVERED');

        if ($startDate) {
            $qb->andWhere('o.createdAt >= :startDate')
               ->setParameter('startDate', $startDate);
        }

        if ($endDate) {
            $qb->andWhere('o.createdAt <= :endDate')
               ->setParameter('endDate', $endDate);
        }

        $result = $qb->getQuery()->getSingleScalarResult();
        
        return $result ? (float)$result : 0.0;
    }

    public function getPaginatedOrders(int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o', 'u', 'oi', 'p')
            ->join('o.user', 'u')
            ->join('o.orderItems', 'oi')
            ->join('oi.product', 'p')
            ->orderBy('o.createdAt', 'DESC');

        $firstResult = ($page - 1) * $limit;
        
        $query = $qb->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($limit);

        return [
            'orders' => $query->getResult(),
            'total' => $this->count([]),
            'pages' => ceil($this->count([]) / $limit),
            'currentPage' => $page,
        ];
    }

    public function getFilteredPaginatedOrdersQuery(array $filters = [])
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->leftJoin('o.orderItems', 'oi')
            ->leftJoin('oi.product', 'p')
            ->addSelect('u')
            ->addSelect('oi')
            ->addSelect('p');

        if (!empty($filters['search'])) {
            $qb->andWhere('o.reference LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $qb->andWhere('o.status = :status')
               ->setParameter('status', $filters['status']);
        }

        if (!empty($filters['dateFrom'])) {
            $qb->andWhere('o.createdAt >= :dateFrom')
               ->setParameter('dateFrom', new \DateTime($filters['dateFrom']));
        }

        if (!empty($filters['dateTo'])) {
            $qb->andWhere('o.createdAt <= :dateTo')
               ->setParameter('dateTo', new \DateTime($filters['dateTo'] . ' 23:59:59'));
        }

        $sortField = $filters['sortField'] ?? 'o.createdAt';
        $sortDirection = $filters['sortDirection'] ?? 'DESC';
        $qb->orderBy($sortField, $sortDirection);

        return $qb->getQuery();
    }

    public function hasOrders(Product $product): bool
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->join('o.orderItems', 'oi')
            ->where('oi.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
