<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\NewSortieType;
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
    public function newSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(NewSortieType::class, $sortie);
        $form->handleRequest($request);

        dump($this->getUser());

        //Préremplissage l'organisateur et l'état
        $sortie->setOrganisateur($this->getUser());
//        $sortie->setCampusOrganisateur($this->getUser()->getCampus());

        if ($form->isSubmitted() && $form->isValid()) {
            //Hydrater les propriétés manquantes
            $sortie->setEtat(1);

            //Envoi à la BDD
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('main_home');
        }

        return $this->render('sorties/newSortie.html.twig', [
            'newSortie' => $form->createView(),
        ]);
    }
}
