<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Entity\Course;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/enrollment', name: 'app_enrollment_')]
class EnrollmentController extends AbstractController
{
    #[Route('/enroll/{id}', name: 'enroll', methods: ['POST'])]
    public function enroll(
        Course $course,
        EnrollmentRepository $enrollmentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        // Check if already enrolled
        if ($enrollmentRepository->isEnrolled($user, $course)) {
            $this->addFlash('warning', 'You are already enrolled in this course.');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        // Create enrollment
        $enrollment = new Enrollment();
        $enrollment->setUser($user);
        $enrollment->setCourse($course);
        $enrollment->setStatus('active');

        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully enrolled in ' . $course->getTitle());
        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }

    #[Route('/unenroll/{id}', name: 'unenroll', methods: ['POST'])]
    public function unenroll(
        Course $course,
        EnrollmentRepository $enrollmentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();

        $enrollment = $enrollmentRepository->findByUserAndCourse($user, $course);

        if (!$enrollment) {
            $this->addFlash('danger', 'You are not enrolled in this course.');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        $entityManager->remove($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully unenrolled from ' . $course->getTitle());
        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }
}
