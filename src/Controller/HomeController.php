<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CourseRepository $courseRepository, CategoryRepository $categoryRepository): Response
    {
        // 1. Fetch the 3 newest courses
        $latestCourses = $courseRepository->findBy([], ['createdAt' => 'DESC'], 3);

        // 2. Fetch all categories (for the "Explore" section)
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'latestCourses' => $latestCourses,
            'categories' => $categories,
        ]);
    }
}