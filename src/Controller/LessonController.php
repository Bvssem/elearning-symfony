<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class LessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'app_lesson_show', methods: ['GET'])]
    public function show(Lesson $lesson, EnrollmentRepository $enrollmentRepo): Response
    {
        $user = $this->getUser();
        $course = $lesson->getCourse();

        // Security: Check if enrolled
        $isEnrolled = $enrollmentRepo->findOneBy(['student' => $user, 'course' => $course]);
        $isTeacher = ($user === $course->getTeacher());

        if (!$isEnrolled && !$isTeacher && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You must be enrolled to view this lesson.');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'course' => $course,
        ]);
    }

    #[Route('/course/{course_id}/lesson/new', name: 'app_lesson_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $course_id, EntityManagerInterface $entityManager): Response
    {
        $course = $entityManager->getRepository(Course::class)->find($course_id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        if ($course->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not the owner of this course.');
        }

        $lesson = new Lesson();
        $lesson->setCourse($course);
        
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'Lesson created successfully!');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
            'course' => $course
        ]);
    }

    #[Route('/lesson/{id}/edit', name: 'app_lesson_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($lesson->getCourse()->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You do not have permission to edit this lesson.');
        }

        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Lesson updated successfully!');
            return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId()]);
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/lesson/{id}', name: 'app_lesson_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($lesson->getCourse()->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
             throw $this->createAccessDeniedException('You do not have permission to delete this lesson.');
        }

        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $courseId = $lesson->getCourse()->getId();
            $entityManager->remove($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'Lesson deleted.');
            return $this->redirectToRoute('app_course_show', ['id' => $courseId]);
        }

        return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId()]);
    }

    // --- NEW: Toggle Complete Status ---
    #[Route('/lesson/{id}/complete', name: 'app_lesson_complete', methods: ['POST'])]
    public function toggleComplete(Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // 1. Check if the user has already completed this lesson
        if ($user->getCompletedLessons()->contains($lesson)) {
            $user->removeCompletedLesson($lesson); // Un-mark
        } else {
            $user->addCompletedLesson($lesson); // Mark as done
        }

        $entityManager->flush();

        // 2. Stay on the same page
        return $this->redirectToRoute('app_lesson_show', ['id' => $lesson->getId()]);
    }
}