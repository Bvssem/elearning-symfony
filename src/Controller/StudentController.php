<?php

namespace App\Controller;

use App\Repository\EnrollmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/student', name: 'app_student_')]
class StudentController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EnrollmentRepository $enrollmentRepository): Response
    {
        $user = $this->getUser();
        $enrollments = $enrollmentRepository->findByUser($user);

        return $this->render('student/dashboard.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }
}
