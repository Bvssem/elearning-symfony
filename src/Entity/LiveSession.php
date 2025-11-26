<?php

namespace App\Entity;

use App\Repository\LiveSessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiveSessionRepository::class)]
class LiveSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column]
    private ?int $durationMinutes = null;

    #[ORM\Column(length: 255)]
    private ?string $meetingUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recordingUrl = null;

    #[ORM\ManyToOne(inversedBy: 'liveSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    public function getMeetingUrl(): ?string
    {
        return $this->meetingUrl;
    }

    public function setMeetingUrl(string $meetingUrl): self
    {
        $this->meetingUrl = $meetingUrl;

        return $this;
    }

    public function getRecordingUrl(): ?string
    {
        return $this->recordingUrl;
    }

    public function setRecordingUrl(?string $recordingUrl): self
    {
        $this->recordingUrl = $recordingUrl;

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
}
