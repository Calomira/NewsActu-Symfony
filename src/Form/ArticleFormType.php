<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre de l\'article',
            ])
            ->add('subtitle',TextType::class,[
                'label' => 'Sous-titre',
            ])
            ->add('content', TextareaType::class,[
                'label' =>false ,
                'attr' => [
                    'placeholder' => 'Ici le centenu de l\'article'
                ],
            ])
            ->add('category', EntityType::class,[
                'class' => Categorie::class,
                'choice_label' => 'name',
                'label' => 'Choissisez une catègorie'
            ])
            ->add('photo', FileType::class,[
                'label' => 'Photo d\'illustration',
            ])
            
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
