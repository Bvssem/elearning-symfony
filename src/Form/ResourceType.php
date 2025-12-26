<?php

namespace App\Form;

use App\Entity\Resource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Resource Title',
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Course Slides PDF']
            ])
            ->add('file', FileType::class, [
                'label' => 'Upload File (PDF, ZIP, Image)',
                'mapped' => false, // This field is not in the database, we handle it manually
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/zip',
                            'application/x-zip-compressed',
                            'image/*',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF, ZIP, or Image file',
                    ])
                ],
            ])
            ->add('isFree', CheckboxType::class, [
                'label' => 'Free Preview? (Allow non-enrolled students to download)',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}