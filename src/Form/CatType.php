<?php

namespace App\Form;

use App\Entity\Cat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class CatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Sovereign Name',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'The Sovereign name cannot be empty.'),
                ],
            ])
            ->add('des', TextareaType::class, [
                'label' => 'Short Description',
                'required' => false
            ])
            ->add('dess', TextareaType::class, [
                'label' => 'Long Description',
                'required' => false
            ])
            ->add('img_file', FileType::class, [
                'label' => 'Category Image',
                'mapped' => false, 
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '22M',
                        mimeTypes: [
                            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 
                            'image/tiff', 'image/svg+xml', 'image/x-icon'
                        ],
                        mimeTypesMessage: 'Please upload a valid image format.'
                    ),
                ],
            ])
            ->add('filer_file', FileType::class, [
                'label' => 'Documentation Attachment',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '22M',
                        mimeTypes: [
                            'application/pdf', 'text/plain', 'text/html', 'text/csv', 'application/json',
                            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/jpeg', 'image/png', 'image/webp', 'image/gif'
                        ],
                        mimeTypesMessage: 'This file format is not permitted in the registry.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Cat::class]);
    }
}