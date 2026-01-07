<?php

namespace App\Repository;

use App\Entity\Category; // <--- Changed from Course to Category
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // 👇 The error was here. It must say Category::class
        parent::__construct($registry, Category::class);
    }
}