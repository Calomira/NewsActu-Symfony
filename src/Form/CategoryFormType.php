<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => false,
                'attr' =>[
                    'placeholder' => 'Nom de la categorie' ,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut être vide'
            ]),
            new Length([
                'min' => 3,
                        'max' => 50,
                        'minMessage' => "Votre titre est trop court. Le nombre de caractères minimal est {{ limit }}",
                        'maxMessage' => "Votre titre est trop long. Le nombre de caractères maximal est {{ limit }}",
                    ])
            ],
            ])
            
            // ->add('alias')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('deletedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
