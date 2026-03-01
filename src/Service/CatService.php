<?php

namespace App\Service;

use App\Entity\Cat;
use App\Repository\CatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class CatService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CatRepository $catRepository
    ) {}

    /**
     * Requirement: Read All with sorting and filters
     */
    public function findAllCustom(
        array $filters = [], 
        string $orderBy = 'id', 
        string $direction = 'DESC', 
        array $fields = ['*'],
        array $with = []
    ): array {
        $qb = $this->createCustomQueryBuilder($filters, $orderBy, $direction, $fields, $with);
        return $qb->getQuery()->getResult();
    }

    /**
     * Requirement: Paginated result (Discipline for Large Data)
     */
    public function findPaginated(
        int $page = 1,
        int $limit = 10,
        array $filters = [],
        string $orderBy = 'id',
        string $direction = 'DESC',
        array $fields = ['*'],
        array $with = []
    ): array {
        $qb = $this->createCustomQueryBuilder($filters, $orderBy, $direction, $fields, $with);
        
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->catRepository->createQueryBuilder('c')->select('COUNT(c.id)');

        foreach ($filters as $field => $data) {
            $value = is_array($data) ? $data['value'] : $data;
            $operator = is_array($data) && isset($data['operator']) ? strtoupper($data['operator']) : '=';

            if ($operator === 'LIKE') {
                $qb->andWhere("c.$field LIKE :$field")->setParameter($field, '%' . $value . '%');
            } else {
                $qb->andWhere("c.$field = :$field")->setParameter($field, $value);
            }
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }


    /**
     * Requirement: Find One by ID (The most common use case)
     */



/**
     * Requirement: Find One by ID
     * Upgraded: Now supports dynamic relations (eager loading)
     */
    public function find(int $id, array $with = []): ?Cat
    {
        // If no relations are needed, stay on the fast track
        if (empty($with)) {
            return $this->catRepository->find($id);
        }
        return $this->findOneByCustom(['id' => $id], ['*'], $with);
    }


    public function findOneByCustom(
        array $criteria, 
        array $fields = ['*'], 
        array $with = []
    ): ?Cat {
        // We pass empty strings or nulls for order, as they are irrelevant for 1 record
        $qb = $this->createCustomQueryBuilder($criteria, '', '', $fields, $with);
        $qb->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * The Executioners: Save and Delete
     */
    public function save(Cat $cat): void
    {
        $this->entityManager->persist($cat);
        $this->entityManager->flush();
    }

    public function delete(Cat $cat): void
    {
        $this->entityManager->remove($cat);
        $this->entityManager->flush();
    }

    public function deleteBulk(array $ids): void
    {
        foreach ($ids as $id) {
            $cat = $this->find((int)$id);
            if ($cat) {
                $this->entityManager->remove($cat);
            }
        }
        $this->entityManager->flush(); 
    }

    /**
     * The Engine: Refined to handle cases without ordering
     */

        private function createCustomQueryBuilder(
        array $filters, 
        string $orderBy, 
        string $direction, 
        array $fields, 
        array $with
    ): QueryBuilder {
        $qb = $this->catRepository->createQueryBuilder('c');

        if (!in_array('*', $fields)) {
            $qb->select('partial c.{' . implode(',', $fields) . '}');
        }

        foreach ($with as $relation) {
            $qb->leftJoin('c.' . $relation, $relation)->addSelect($relation);
        }

        // 3. ENHANCED FILTERS
        foreach ($filters as $field => $data) {
            // Handle both simple ['id' => 1] and complex ['name' => ['value' => 'A', 'operator' => 'LIKE']]
            $value = is_array($data) ? $data['value'] : $data;
            $operator = is_array($data) && isset($data['operator']) ? strtoupper($data['operator']) : '=';

            if ($operator === 'LIKE') {
                $qb->andWhere("c.$field LIKE :$field")->setParameter($field, '%' . $value . '%');
            } else {
                $qb->andWhere("c.$field = :$field")->setParameter($field, $value);
            }
        }

        if (!empty($orderBy)) {
            $qb->orderBy("c.$orderBy", $direction);
        }

        return $qb;
    }





    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        $qb = $this->catRepository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.name = :name')
            ->setParameter('name', $name);

        if ($ignoreId) {
            $qb->andWhere('c.id != :id')
                ->setParameter('id', $ignoreId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }



}