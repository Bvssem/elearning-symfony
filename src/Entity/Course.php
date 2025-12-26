<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: "course")]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $title = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $shortDescription = null;

    #[ORM\Column(type: 'text')]
    private ?string $fullDescription = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $imageFilename = null;

    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'coursesTaught')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $teacher = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Enrollment::class, orphanRemoval: true)]
    private Collection $enrollments;

    // --- THIS WAS MISSING ---
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Lesson::class, orphanRemoval: true)]
    private Collection $lessons;
    // ------------------------

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Resource::class, orphanRemoval: true)]
    private Collection $resources;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: LiveSession::class, orphanRemoval: true)]
    private Collection $liveSessions;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->enrollments = new ArrayCollection();
        $this->lessons = new ArrayCollection(); // <--- THIS WAS MISSING
        $this->resources = new ArrayCollection();
        $this->liveSessions = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = $slug; return $this; }
    public function getShortDescription(): ?string { return $this->shortDescription; }
    public function setShortDescription(string $shortDescription): self { $this->shortDescription = $shortDescription; return $this; }
    public function getFullDescription(): ?string { return $this->fullDescription; }
    public function setFullDescription(string $fullDescription): self { $this->fullDescription = $fullDescription; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): self { $this->price = $price; return $this; }
    public function getImageFilename(): ?string { return $this->imageFilename; }
    public function setImageFilename(string $imageFilename): self { $this->imageFilename = $imageFilename; return $this; }
    public function isPublished(): bool { return $this->isPublished; }
    public function setIsPublished(bool $isPublished): self { $this->isPublished = $isPublished; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }
    public function getTeacher(): ?User { return $this->teacher; }
    public function setTeacher(?User $teacher): self { $this->teacher = $teacher; return $this; }
    public function getEnrollments(): Collection { return $this->enrollments; }
    public function addEnrollment(Enrollment $enrollment): self { if (!$this->enrollments->contains($enrollment)) { $this->enrollments->add($enrollment); $enrollment->setCourse($this); } return $this; }
    public function removeEnrollment(Enrollment $enrollment): self { if ($this->enrollments->removeElement($enrollment)) { if ($enrollment->getCourse() === $this) { $enrollment->setCourse(null); } } return $this; }
    public function getResources(): Collection { return $this->resources; }
    public function addResource(Resource $resource): self { if (!$this->resources->contains($resource)) { $this->resources->add($resource); $resource->setCourse($this); } return $this; }
    public function removeResource(Resource $resource): self { if ($this->resources->removeElement($resource)) { if ($resource->getCourse() === $this) { $resource->setCourse(null); } } return $this; }
    public function getLiveSessions(): Collection { return $this->liveSessions; }
    public function addLiveSession(LiveSession $liveSession): self { if (!$this->liveSessions->contains($liveSession)) { $this->liveSessions->add($liveSession); $liveSession->setCourse($this); } return $this; }
    public function removeLiveSession(LiveSession $liveSession): self { if ($this->liveSessions->removeElement($liveSession)) { if ($liveSession->getCourse() === $this) { $liveSession->setCourse(null); } } return $this; }
    public function getReviews(): Collection { return $this->reviews; }
    public function addReview(Review $review): self { if (!$this->reviews->contains($review)) { $this->reviews->add($review); $review->setCourse($this); } return $this; }
    public function removeReview(Review $review): self { if ($this->reviews->removeElement($review)) { if ($review->getCourse() === $this) { $review->setCourse(null); } } return $this; }

    /** @return Collection<int, Lesson> */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCourse() === $this) {
                $lesson->setCourse(null);
            }
        }

        return $this;
    }
}