<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivreController extends AbstractController
{
    #[Route('/livre', name: 'livre')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
       $livre = new Livre(); // Création d'un livre vide
       // Création d'un objet formulaire spécifique à un livre
       $form = $this->createForm(LivreType::class, $livre);
       // Récupération du $_GET ou du $_POST
       $form->handleRequest($request);
       // Récupération de l'utilisateur connecté
       $user = $this->getUser();

       // Si le formulaire est envoyé 
       if ($form->isSubmitted() && $form->isValid()){
           $livre->setUser($user);

           // Sauvegarde dans la base de données
           $em = $doctrine->getManager();
           $em->persist($livre);
           $em->flush();
           $this->addFlash('Sucess', 'Votre livre est enregistré');

           // Redirection vers la page login 
           return $this->redirectToRoute('show_livre');
       }

       

        return $this->render('livre/index.html.twig', [
           'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/livres", name="show_livre")
     */
    public function show(): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();

        // Récupération des livres de l'utilisateur connecté
        $livres = $user->getLivres();

        return $this->render('livre/show.html.twig', [
            'livres'=> $livres,
        ]);
    }
}
