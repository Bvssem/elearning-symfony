<?php

namespace App\Entity;

use App\Repository\TeacherProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherProfileRepository::class)]
class TeacherProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'teacherProfile', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'text')]
    private $bio;

    #[ORM\Column(type: 'string', nullable: true)]
    private $paymentInfo;

    #[ORM\Column(type: 'string', length: 20)]
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    public function getPaymentInfo(): ?string
    {
        return $this->paymentInfo;
    }

    public function setPaymentInfo(?string $paymentInfo): self
    {
        $this->paymentInfo = $paymentInfo;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
