<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getFilteredPaginatedUsersQuery(array $filters = [])
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.orders', 'o')
            ->addSelect('o');

        if (!empty($filters['search'])) {
            $qb->andWhere('u.email LIKE :search OR u.username LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['role'])) {
            $qb->andWhere('u.role = :role')
               ->setParameter('role', $filters['role']);
        }

        $sortField = $filters['sortField'] ?? 'u.email';
        $sortDirection = $filters['sortDirection'] ?? 'ASC';
        $qb->orderBy($sortField, $sortDirection);

        return $qb->getQuery();
    }
}
