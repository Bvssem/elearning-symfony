<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Course;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Course Title',
                'attr' => ['placeholder' => 'e.g., Master Symfony 6']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // <--- This will now work because Repository fetches Categories!
                'placeholder' => 'Choose a category',
                'label' => 'Category',
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Short Summary',
                'attr' => ['rows' => 2, 'placeholder' => 'A brief overview...']
            ])
            ->add('fullDescription', TextareaType::class, [
                'label' => 'Full Description',
                'required' => false,
                'attr' => ['rows' => 5, 'placeholder' => 'Detailed course content...']
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'TND',
                'label' => 'Price',
            ])
            ->add('imageFilename', TextType::class, [
                'label' => 'Image URL',
                'required' => false,
                'attr' => ['placeholder' => 'https://...']
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'Publish immediately?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}