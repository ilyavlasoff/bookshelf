<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddBookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bookName', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Field name must be filled'
                    ])
                ]
            ])
            ->add('author', TextType::class, [
                'label' => 'Author',
                'constraints' => [

                ]
            ])
            ->add('cover', FileType::class, [
                'label' => 'Book cover',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        /*'mimeTypes' => [
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PNG or JPG file'*/
                    ])
                ]
            ])
            ->add('bookFile', FileType::class, [
                'label' => 'Book file',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document'
                    ])
                ]
            ])
            ->add('readDate', DateType::class, [
                'label' => 'Reading date'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Book description'
            ])
            ->add('mark', HiddenType::class)
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data-class' => Book::class
        ]);
    }
}