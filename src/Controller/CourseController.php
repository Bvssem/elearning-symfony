<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Review;
use App\Form\CourseType;
use App\Form\ReviewType;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface; // <--- IMPORTANT

#[Route('/course')]
#[IsGranted('ROLE_USER')]
final class CourseController extends AbstractController
{
    #[Route('/', name: 'app_course_index', methods: ['GET'])]
    public function index(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/new', name: 'app_course_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger // <--- Inject Slugger
    ): Response
    {
        $course = new Course();
        // Automatically set the logged-in user as the teacher
        $course->setTeacher($this->getUser());

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // --- AUTOMATICALLY GENERATE SLUG ---
            // e.g. "My Course" -> "my-course-65a8f..."
            $slug = $slugger->slug($course->getTitle())->lower();
            $course->setSlug($slug . '-' . uniqid()); 
            // -----------------------------------

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Course $course,
        EnrollmentRepository $enrollmentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $isEnrolled = false;
        
        // 1. Check Enrollment
        if ($user) {
            $isEnrolled = $enrollmentRepository->isEnrolled($user, $course);
        }

        // 2. Handle Review Form
        $review = new Review();
        $reviewForm = null;

        if ($isEnrolled) {
            $review->setCourse($course);
            $review->setStudent($user);
            
            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $review->setIsApproved(true);
                $entityManager->persist($review);
                $entityManager->flush();

                $this->addFlash('success', 'Thank you for your review!');
                return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
            }
            $reviewForm = $form->createView();
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'is_enrolled' => $isEnrolled,
            'review_form' => $reviewForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Security Check
        if ($course->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You can only edit your own courses.');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_delete', methods: ['POST'])]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        // Security Check
        if ($course->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You can only delete your own courses.');
        }

        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_course_index', [], Response::HTTP_SEE_OTHER);
    }
}