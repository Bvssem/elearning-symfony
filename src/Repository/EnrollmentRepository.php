<?php

namespace App\Repository;

use App\Entity\Enrollment;
use App\Entity\User;
use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EnrollmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrollment::class);
    }

    public function findByUserAndCourse(User $user, Course $course): ?Enrollment
    {
        return $this->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['enrolledAt' => 'DESC']);
    }

    public function findByCourse(Course $course): array
    {
        return $this->findBy(['course' => $course], ['enrolledAt' => 'DESC']);
    }

    public function isEnrolled(User $user, Course $course): bool
    {
        return $this->findByUserAndCourse($user, $course) !== null;
    }
}
