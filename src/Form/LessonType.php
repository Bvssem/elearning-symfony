<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Lesson Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('number', IntegerType::class, [
                'label' => 'Order Number',
                'help' => 'Type 1 for the first lesson, 2 for the second, etc.',
                'attr' => ['class' => 'form-control']
            ])
            ->add('videoUrl', TextType::class, [
                'label' => 'YouTube Embed URL',
                'help' => 'Example: https://www.youtube.com/embed/VIDEO_ID',
                'attr' => ['class' => 'form-control']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description / Content',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}