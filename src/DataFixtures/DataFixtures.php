<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class DataFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    #Cette fonction load() sera exécutée en ligne de commande ,avec : php bin/console doctrinedoctrine:fixtures:load --append
    # => le drapeau --append permet de ne pas purger la BDD.

    public function load(ObjectManager $manager): void
    {
        //Déclaration d'une variable de type array ,avec le nom des differents catégories de NewsActu.

        $categories = [
            'Politique',
            'Société',
            'People',
            'Economie',
            'Santé',
            'Espace',
            'Science',
            'Mode',
            'Informatique',
            'Ecologie',
            'Cinéma',
            'Hi Tech',
        ];

         //La boucle foreach() est optimisée pour les array.
         //La syntaxe complète à l'interieur des parenthèses est : ($key=>$value)
         foreach($categories as $cat){

            //Instanciation d'un objet Categorie()
              $categorie = new Categorie();

              //Appel des setters de notre Objet $categorie
              $categorie->setName($cat);
              $categorie->setAlias($this->slugger->slug($cat));
              $categorie->setCreatedAt(new DateTime());
              $categorie->setUpdatedAt(new DateTime());
              
              //EntityManager ,on appel sa méthode persist() pour insérer en BDD l'Objet $categorie
              $manager->persist($categorie);

         }
         //On vide l'EntityManager pour la suite.
        $manager->flush();
    }
}
