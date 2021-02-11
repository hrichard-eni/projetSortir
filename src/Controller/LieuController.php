<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\NewLieuType;
use App\Form\NewVilleType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/newSortie/newLieu", name="lieu_new")
     */
    public function newLieu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();

        $form = $this->createForm(NewLieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Pas de propriété manquante donc envoir à la BDD directement
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sorties_new');
        }

        return $this->render('lieu/newLieu.html.twig', [
            'newLieu' => $form->createView()
        ]);
    }

    /**
     * @Route("/newSortie/newVille", name="ville_new")
     */
    public function newVille(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();

        $form = $this->createForm(NewVilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Pas de propriété manquante donc envoir à la BDD directement
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('lieu_new');
        }

        return $this->render('lieu/newVille.html.twig', [
            'newVille' => $form->createView()
        ]);
    }
}
