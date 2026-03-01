<?php

namespace App\Service;

use App\Entity\Subcat;
use App\Repository\SubcatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class SubcatService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubcatRepository $subcatRepository
    ) {}

    /**
     * Requirement: Find One by ID
     * Upgraded: Supports eager loading via $with
     */
    public function find(int $id, array $with = []): ?Subcat
    {
        if (empty($with)) {
            return $this->subcatRepository->find($id);
        }

        return $this->findOneByCustom(['id' => $id], ['*'], $with);
    }

    /**
     * Requirement: Convenient lookup by Category
     * Supports: Additional filters, custom ordering, and related models
     */
    public function findByCat(
        int $catId, 
        array $extraFilters = [], 
        string $orderBy = 'id', 
        string $direction = 'DESC', 
        array $with = []
    ): array {
        $filters = array_merge(['cat' => $catId], $extraFilters);
        
        return $this->findAllCustom($filters, $orderBy, $direction, ['*'], $with);
    }

    /**
     * Sovereign Engine: Find All with sorting and filters
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
        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countAll(array $filters = []): int
    {
        $qb = $this->subcatRepository->createQueryBuilder('s')->select('COUNT(s.id)');
        $this->applyFilters($qb, $filters);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findOneByCustom(
        array $criteria, 
        array $fields = ['*'], 
        array $with = []
    ): ?Subcat {
        $qb = $this->createCustomQueryBuilder($criteria, '', '', $fields, $with);
        $qb->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function save(Subcat $subcat): void
    {
        $this->entityManager->persist($subcat);
        $this->entityManager->flush();
    }

    public function delete(Subcat $subcat): void
    {
        $this->entityManager->remove($subcat);
        $this->entityManager->flush();
    }

    public function deleteBulk(array $ids): void
    {
        foreach ($ids as $id) {
            $subcat = $this->find((int)$id);
            if ($subcat) $this->entityManager->remove($subcat);
        }
        $this->entityManager->flush(); 
    }

    private function createCustomQueryBuilder(
        array $filters, 
        string $orderBy, 
        string $direction, 
        array $fields, 
        array $with
    ): QueryBuilder {
        $qb = $this->subcatRepository->createQueryBuilder('s');

        if (!in_array('*', $fields)) {
            $qb->select('partial s.{' . implode(',', $fields) . '}');
        }

        foreach ($with as $relation) {
            $qb->leftJoin('s.' . $relation, $relation)->addSelect($relation);
        }

        $this->applyFilters($qb, $filters);

        if (!empty($orderBy)) {
            $orderPath = str_contains($orderBy, '.') ? $orderBy : "s.$orderBy";
            $qb->orderBy($orderPath, $direction);
        }

        return $qb;
    }


    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $field => $data) {
            $value = is_array($data) ? $data['value'] : $data;
            $operator = is_array($data) && isset($data['operator']) ? strtoupper($data['operator']) : '=';
            
            $paramName = str_replace('.', '_', $field);

            // Mapping: If the user sends 'catid', we map it to the property 'cat'
            $targetField = ($field === 'catid') ? 'cat' : $field;
            $target = str_contains($targetField, '.') ? $targetField : "s.$targetField";

            if ($operator === 'LIKE') {
                $qb->andWhere("$target LIKE :$paramName")
                   ->setParameter($paramName, '%' . $value . '%');
            } else {
                $qb->andWhere("$target $operator :$paramName")
                   ->setParameter($paramName, $value);
            }
        }
    }






/**
     * Requirement: Check if a name is taken within a specific Category scope
     */
    public function nameExistsInSubcat(string $name, int $catId, ?int $ignoreId = null): bool
    {
        $filters = [
            'name' => $name, 
            'cat' => $catId
        ];
        
        if ($ignoreId) {
            $filters['id'] = ['value' => $ignoreId, 'operator' => '!='];
        }

        return $this->countAll($filters) > 0;
    }



    
}