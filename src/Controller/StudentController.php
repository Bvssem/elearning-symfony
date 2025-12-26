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
    // --- NEW: Redirect /student to /student/dashboard ---
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_student_dashboard');
    }
    // ----------------------------------------------------

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EnrollmentRepository $enrollmentRepository): Response
    {
        // Get all enrollments for the current logged-in user
        $enrollments = $enrollmentRepository->findBy(['student' => $this->getUser()]);

        return $this->render('student/dashboard.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }
}