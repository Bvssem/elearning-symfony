<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')] // We assume teachers are regular users who created courses
class TeacherController extends AbstractController
{
    #[Route('/teacher/dashboard', name: 'app_teacher_dashboard')]
    public function dashboard(CourseRepository $courseRepository): Response
    {
        $user = $this->getUser();

        // 1. Fetch courses created by this teacher
        // (Assuming the relationship in User.php is 'coursesTaught')
        $courses = $courseRepository->findBy(['teacher' => $user], ['createdAt' => 'DESC']);

        // 2. Calculate totals
        $totalStudents = 0;
        $totalRevenue = 0;
        $courseData = [];

        foreach ($courses as $course) {
            // We assume Course has a getEnrollments() method. 
            // If not, we count them manually via the repository or relationship.
            $enrollments = $course->getEnrollments(); 
            $count = count($enrollments);
            $revenue = $count * $course->getPrice();

            $totalStudents += $count;
            $totalRevenue += $revenue;

            $courseData[] = [
                'course' => $course,
                'studentCount' => $count,
                'revenue' => $revenue,
                'enrollments' => $enrollments // Pass this if you want to show names
            ];
        }

        return $this->render('teacher/dashboard.html.twig', [
            'courseData' => $courseData,
            'totalStudents' => $totalStudents,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}