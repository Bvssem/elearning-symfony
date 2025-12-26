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
    // Redirect /student to /student/dashboard
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_student_dashboard');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EnrollmentRepository $enrollmentRepository): Response
    {
        $user = $this->getUser();
        $enrollments = $enrollmentRepository->findBy(['student' => $user]);

        // Calculate progress for each enrollment
        $enrollmentData = [];
        
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->getCourse();
            $lessons = $course->getLessons();
            $totalLessons = count($lessons);
            
            $completedCount = 0;
            foreach ($lessons as $lesson) {
                if ($user->getCompletedLessons()->contains($lesson)) {
                    $completedCount++;
                }
            }

            $percentage = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

            $enrollmentData[] = [
                'enrollment' => $enrollment,
                'progress' => $percentage
            ];
        }

        return $this->render('student/dashboard.html.twig', [
            'enrollmentData' => $enrollmentData, // Pass the new structured data
        ]);
    }
}