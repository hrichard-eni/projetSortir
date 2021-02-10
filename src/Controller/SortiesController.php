<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\NewSortieType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{


    /**
     * @Route("/newSortie", name="sorties_new")
     */
    public function newSortie(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();

        //Préremplissage l'organisateur et du campus organisateur
        $sortie->setOrganisateur($this->getUser());
        $sortie->setCampusOrganisateur($this->getUser()->getCampus());

        //Création du formulaire
        $form = $this->createForm(NewSortieType::class, $sortie);
        $form->handleRequest($request);

        //Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //Hydrater la propriété état à 'Créé'
            $sortie->setEtat($etatRepository->find(1));

            //Envoi à la BDD
            $entityManager->persist($sortie);
            $entityManager->flush();

            //Renvoi sur la page d'accueuil
            return $this->redirectToRoute('main_home');
        }

        //Si le formulaire n'est pas soumis affiche ce dernier
        return $this->render('sorties/newSortie.html.twig', [
            'newSortie' => $form->createView(),
        ]);
    }
}