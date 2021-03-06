<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
  /**
   * @Route("/tableau-de-bord", name="show_dashboard" ,methods={"GET"})
   */
  public function showDashboard(EntityManagerInterface $entityManager): Response
  {
    $articles = $entityManager->getRepository(Article::class)->findAll();

    return $this->render('admin/show_dashboard.html.twig', [
      'articles' => $articles,
    ]);
  }

  /**
   * @Route("/creer-un-article", name="create_article", methods={"GET|POST"})
   */
  public function createArticle(
    Request $request,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
  ): Response {
    $article = new Article();
    $form = $this->createForm(ArticleFormType::class, $article)->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      //POur acceder à une valeur d'un input de $form ,on fait :
      // $form->get('title')->getData()

      $article->setAlias($slugger->slug($article->getTitle()));
      $article->setCreatedAt(new DateTime());
      $article->setUpdatedAt(new DateTime());

      //Variabilisation du fichier 'photo' upload2.
      $file = $form->get('photo')->getData();

      //if(isset($file) === true)
      //si un ficheir est uploadé (depuis le formulaire)

      if ($file) {
        //maintenant il s'agit de reconstruire le nom du fichier pour sécuriser.
        // 1ère ETAPE  : on deconstruit le nom du fichier et on variabilise.

        $extension = '.' . $file->guessExtension();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        //Assainissement du nom de fichier (du filename)(choix entre les deux $ en bas)
        $safeFilename = $slugger->slug($originalFilename);
        // $safeFilename = $article->getAlias();

        //Dème ETAPE : on reconstruit le nom du fichier maintenant qu'il est safe.
        //uniqid() est une fonction native de PHP ,elle permet d'ajouter une valeur numérique (id) unique et auto-générée
        $newFilename = $safeFilename . '_' . uniqid() . $extension;
        // try/catch fait partie de PHP nativement.
        try {
          //On a configuré un paramètre 'uploads_dir' dans fichier service.yaml
          //Ce param contient le chemin absolu de notre dossier d'upload de photo.
          $file->move($this->getParameter('uploads_dir'), $newFilename);

          //On set le NOM de la photo ,pas le CHEMIN
          $article->setPhoto($newFilename);
        } catch (FileException $exception) {
        } //END catch()


      } // END if($file)

      $entityManager->persist($article);
      $entityManager->flush();

      // Ici on ajoute un message qu'on affichera en twig
      $this->addFlash('success', 'Bravo,votre article est bien en ligne');

      return $this->redirectToRoute('show_dashboard');
    } //END if($form)

    return $this->render('admin/form/form_article.html.twig', [
      'form' => $form->createView()
    ]);
  }

  /**
   *@Route("/modifier-un-article/{id}", name="update_article", methods={"GET|POST"})  
   * L'action est exécutée 2x et accessible par les deux méthods (GET|POST)
   */
  public function updateArticle(Article $article, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
  { #Condition ternaire : $article->getPhoto ?? '' ;
    # => est égal à : isset($article->getPhoto()) ? $article->getPhoto() : '' ;
    $originalPhoto = $article->getPhoto() ?? '';

    //1er TOUR en méthode GET
    $form = $this->createForm(ArticleFormType::class, $article, [
      'photo' => $originalPhoto
    ])->handleRequest($request);

    // 2éme TOUR de l'action en méthode POST
    if ($form->isSubmitted() && $form->isValid()) {
      $article->setAlias($slugger->slug($article->getTitle()));
      $article->setUpdatedAt(new DateTime());

      $file = $form->get('photo')->getData();
      if ($file) {

        $extension = '.' . $file->guessExtension();
        $originalFilename = $file->pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $slugger->slug($originalFilename);

        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        try {


          $file->move($this->getParameter('uploads_dir'), $newFilename);

          $article->setPhoto($newFilename);
        } catch (FileException $exception) {
          # code à exécuter si une erreur est attrapée


        } //END catch()


      } else {
        $article->setPhoto($originalPhoto);
      } // END if($file)
      $entityManager->persist($article);
      $entityManager->flush();

      $this->addFlash('success', "L'article" . $article->getTitle() . "à bien été modifié !");

      return $this->redirectToRoute("show_dashboard");
    } //END if($form)

    //On rend la vue pour la méthode GET
    return $this->render('admin/form/form_article.html.twig', [
      'form' => $form->createView(),
      'article' => $article
    ]);
  }

  /**
   * @Route("/archiver-un-article/{id}", name="soft_delete_article", methods={"GET"})
   */
  public function softDeleteArticle(
    Article $article,
    EntityManagerInterface $entityManager
  ): Response {      #On set la propriete deletedAt pour archiver l'article.
    # De l'autre coté on affichera les articles où deletedAt === null

    $article->setDeletedAt(new DateTime());

    $entityManager->persist($article);
    $entityManager->flush();
    $this->addFlash('success', "L'article" . $article->getTitle() . "a bien été archivé");
    return $this->redirectToRoute('show_dashboard');
  }

  /**
   * @Route("/suprimer-un-article/{id}", name="hard_delete_article", methods={"GET"})
   */
  public function hardDeleteArticle(Article $article, EntityManagerInterface $entityManager): Response
  {
    $entityManager->remove($article);
    $this->addFlash('success', "L'article" . $article->getTitle() . " a bien été supprimé de la BDD");
    return $this->redirectToRoute("show_dashboard");
  }
  /**
   * @Route("/restaurer-un-article/{id}", name="restore_article", methods={"GET"})            
   */
  public function restoreArticle(Article $article, EntityManagerInterface $entityManager): Response
  {
    $article->setDeletedAt();

    $entityManager->persist($article);
    $entityManager->flush();
    $this->addFlash('success', "L'article" . $article->getTitle() . " a bien été restaure");
    return $this->redirectToRoute("show_dashboard");
  }
} // END Class
