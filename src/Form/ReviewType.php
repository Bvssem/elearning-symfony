<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'label' => 'Your Rating',
                'choices' => [
                    '⭐⭐⭐⭐⭐ (Excellent)' => 5,
                    '⭐⭐⭐⭐ (Very Good)' => 4,
                    '⭐⭐⭐ (Good)' => 3,
                    '⭐⭐ (Fair)' => 2,
                    '⭐ (Poor)' => 1,
                ],
                'expanded' => false, // Set to true if you want radio buttons
                'attr' => ['class' => 'form-select']
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Your Review',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'What did you think of this course?']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Review',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}