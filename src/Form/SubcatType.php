<?php

namespace App\Form;

use App\Entity\Cat;
use App\Entity\Subcat;
use App\Repository\CatRepository; // Required for QueryBuilder
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubcatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 1. THE CATEGORY DROPDOWN (Now Ordered & Pre-selectable)
            ->add('cat', EntityType::class, [
                'class' => Cat::class,
                'choice_label' => 'name',
                'label' => 'Parent Category',
                'placeholder' => '--- Select Category ---',
                'required' => true,
                // THIS ORDER BY NAME ASC:
                'query_builder' => function (CatRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank(message: 'A Subcategory must belong to a Sovereign Category.'),
                ],
                'attr' => ['class' => 'form-select select2-enable'],
            ])
            // ... rest of your fields stay the same
            ->add('name', TextType::class, [
                'label' => 'Sovereign Name',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'The Sovereign name cannot be empty.'),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Subcategory Name']
            ])
            ->add('des', TextareaType::class, [
                'label' => 'Short Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('dess', TextareaType::class, [
                'label' => 'Long Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('img_file', FileType::class, [
                'label' => 'Subcategory Image',
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
        $resolver->setDefaults([
            'data_class' => Subcat::class,
        ]);
    }
}