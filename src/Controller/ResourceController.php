<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Resource;
use App\Form\ResourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ResourceController extends AbstractController
{
    #[Route('/course/{id}/resource/new', name: 'app_resource_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        Course $course, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger
    ): Response
    {
        // Security: Only teacher can add resources
        if ($course->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Only the teacher can add resources.');
        }

        $resource = new Resource();
        $resource->setCourse($course);
        
        $form = $this->createForm(ResourceType::class, $resource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle File Upload
            $file = $form->get('file')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('resource_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading file');
                    return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
                }

                $resource->setFilenameOrUrl($newFilename);
                $resource->setType($file->guessExtension() ?? 'file');
            }

            $entityManager->persist($resource);
            $entityManager->flush();

            $this->addFlash('success', 'Resource added successfully!');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        return $this->render('resource/new.html.twig', [
            'form' => $form,
            'course' => $course
        ]);
    }

    #[Route('/resource/{id}/delete', name: 'app_resource_delete', methods: ['POST'])]
    public function delete(Request $request, Resource $resource, EntityManagerInterface $entityManager): Response
    {
        // Security
        if ($resource->getCourse()->getTeacher() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$resource->getId(), $request->request->get('_token'))) {
            // Optional: Delete the physical file from uploads folder here if you want
            $entityManager->remove($resource);
            $entityManager->flush();
            $this->addFlash('success', 'Resource deleted.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $resource->getCourse()->getId()]);
    }
}