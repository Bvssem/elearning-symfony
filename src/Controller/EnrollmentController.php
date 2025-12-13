<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Enrollment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EnrollmentController extends AbstractController
{
    #[Route('/enroll/{id}', name: 'app_enrollment_enroll', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function enroll(Course $course, EntityManagerInterface $entityManager): Response
    {
        // 1. Check if user is already enrolled (simple check)
        $user = $this->getUser();
        // You might want to add a check here later to prevent double enrollment

        // 2. Create new Enrollment
        $enrollment = new Enrollment();
        $enrollment->setCourse($course);
        $enrollment->setStudent($user);
        $enrollment->setEnrolledAt(new \DateTimeImmutable());
        
        // 3. Save to database
        $entityManager->persist($enrollment);
        $entityManager->flush();

        $this->addFlash('success', 'You have successfully enrolled in ' . $course->getTitle());

        return $this->redirectToRoute('app_student_dashboard');
    }
}