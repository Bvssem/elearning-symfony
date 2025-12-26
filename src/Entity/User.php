<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarFilename = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // --- TRACK COMPLETED LESSONS (Student) ---
    #[ORM\ManyToMany(targetEntity: Lesson::class)]
    #[ORM\JoinTable(name: 'user_lesson_completion')]
    private Collection $completedLessons;

    // --- NEW: TRACK COURSES TAUGHT (Teacher) ---
    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private Collection $coursesTaught;
    // -------------------------------------------

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->completedLessons = new ArrayCollection();
        $this->coursesTaught = new ArrayCollection(); // <--- Initialize this!
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAvatarFilename(): ?string
    {
        return $this->avatarFilename;
    }

    public function setAvatarFilename(?string $avatarFilename): self
    {
        $this->avatarFilename = $avatarFilename;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCompletedLessons(): Collection
    {
        return $this->completedLessons;
    }

    public function addCompletedLesson(Lesson $lesson): self
    {
        if (!$this->completedLessons->contains($lesson)) {
            $this->completedLessons->add($lesson);
        }
        return $this;
    }

    public function removeCompletedLesson(Lesson $lesson): self
    {
        $this->completedLessons->removeElement($lesson);
        return $this;
    }

    // --- NEW GETTERS FOR TEACHER ---
    /**
     * @return Collection<int, Course>
     */
    public function getCoursesTaught(): Collection
    {
        return $this->coursesTaught;
    }

    public function addCoursesTaught(Course $course): self
    {
        if (!$this->coursesTaught->contains($course)) {
            $this->coursesTaught->add($course);
            $course->setTeacher($this);
        }
        return $this;
    }

    public function removeCoursesTaught(Course $course): self
    {
        if ($this->coursesTaught->removeElement($course)) {
            if ($course->getTeacher() === $this) {
                $course->setTeacher(null);
            }
        }
        return $this;
    }
}