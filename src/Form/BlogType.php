<?php

namespace App\Form;
use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['maxlength' => 20]
            ])
            ->add('Contenu', TextareaType::class, [
                'label' => 'Contenu'
            ])
            ->add('Date_de_creation', DateTimeType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text'
            ])
            ->add('image', FileType::class, [
                'data_class'=>null,
                'label' => 'Choisir une image',
                'attr' => ['class' => 'form-control', 'id' => 'formFile'],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])

            ->add('Auteur', TextType::class, [
                'label' => 'Créé par',
                'attr' => ['maxlength' => 20]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
