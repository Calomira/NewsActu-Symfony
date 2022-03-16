<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre de l\'article',
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champ ne peut être vide/Попълнете полето"
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => "Votre titre  est trop court.Le nombre de caractéres minimal est {{limit}}",
                        'minMessage' => "Votre titre est trop long {{limit}} : votre titre contient {{value}} caractères"
                    ])
                    ],
            ])
            ->add('subtitle',TextType::class,[
                'label' => 'Sous-titre',
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champ ne peut être vide/Попълнете полето"
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => "Votre titre  est trop court.Le nombre de caractéres minimal est {{limit}}",
                        'minMessage' => "Votre titre est trop long {{limit}} : votre titre contient {{value}} caractères"
                    ])
                    ],
            ])
            
            ->add('content', TextareaType::class,[
                'label' =>false ,
                'attr' => [
                    'placeholder' => 'Ici le centenu de l\'article'
                ],
                // Les contraintes de validation pour 'content' sont dans Article Entity (propriété $content)

            ])
            ->add('category', EntityType::class,[
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Choissisez une catègorie'
            ])
            ->add('photo', FileType::class,[
                'label' => 'Photo d\'illustration',
                # 'data_class' => permet de paramétrer le type de class de données à null.
                # (par defaut data_class = File)
                'data_class' => null,
                'attr' => [
                    'data-default-file' => $options['photo'],
                ],
                'constraints' => [
                    new Image ([
                       'mimeTypes' => ['image/jpeg' , 'image/png'],
                       'mimeTypesMessage' => "les types de photo autorisés sont : .jpg et .png",

                    ]),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            //'allow_file_upload' => permet d'autoriser les upload de fichier dans le formulaire
            'allow_file_upload' => true,
            //'photo => permet de récupérer la photo existante lors d'un update
            'photo' => null, 
        ]);
    }
}
