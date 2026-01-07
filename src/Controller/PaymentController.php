<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    #[Route('/payment/checkout/{id}', name: 'app_payment_checkout')]
    public function checkout(Course $course, EnrollmentRepository $enrollmentRepository): Response
    {
        dd('KEY: "' . $this->getParameter('app.stripe_secret_key') . '"');
        $user = $this->getUser();

        // 1. Check if already enrolled
        if ($enrollmentRepository->isEnrolled($user, $course)) {
            $this->addFlash('warning', 'You are already enrolled in this course.');
            return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
        }

        // 2. If Price is 0, enroll directly (Free Course)
        if ($course->getPrice() <= 0) {
            return $this->redirectToRoute('app_payment_success', ['id' => $course->getId()]);
        }

        // 3. Initialize Stripe
        Stripe::setApiKey($this->getParameter('app.stripe_secret_key'));

        // 4. Create Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'tnd', // Tunisian Dinar (or 'usd', 'eur')
                    'product_data' => [
                        'name' => $course->getTitle(),
                        'description' => $course->getShortDescription(),
                    ],
                    'unit_amount' => (int) ($course->getPrice() * 100), // Stripe expects cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', ['id' => $course->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_course_show', ['id' => $course->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/payment/success/{id}', name: 'app_payment_success')]
    public function success(Course $course, EnrollmentRepository $enrollmentRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Check if already enrolled to avoid duplicates
        if (!$enrollmentRepository->isEnrolled($user, $course)) {
            $enrollment = new Enrollment();
            $enrollment->setCourse($course);
            $enrollment->setStudent($user);
            
            $entityManager->persist($enrollment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Payment Successful! You are now enrolled.');
        } else {
            $this->addFlash('info', 'You are already enrolled.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }
}