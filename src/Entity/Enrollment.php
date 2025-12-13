<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_enrollment', columns: ['student_id', 'course_id'])]
class Enrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;  // <--- This was likely missing or named wrong

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $enrolledAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getEnrolledAt(): ?\DateTimeImmutable
    {
        return $this->enrolledAt;
    }

    public function setEnrolledAt(\DateTimeImmutable $enrolledAt): self
    {
        $this->enrolledAt = $enrolledAt;

        return $this;
    }
}