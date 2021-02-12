<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\NewSortieType;
use App\Form\UpdateSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use ContainerAqVPMxW\getDebug_DumpListenerService;
use Doctrine\ORM\EntityManagerInterface;
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

            $duree_nb = $form->get('duree_nombre')->getData(); //Récupère la durée
            $duree_unite = $form->get('duree_unite')->getData(); //Récupère l'unité de durée

            $fin = $this->calculerDateFin($form->getData()->getDateHeureDebut(), $duree_nb, $duree_unite);

            $sortie->setDuree($fin);

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

    /**
     * @Route("/detail/{id}", name="sorties_detail", requirements={"id": "\d+"})
     */
    public function inscription(SortieRepository $sortieRepository, int $id): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        return $this->render('sorties/detailSortie.html.twig', [
            "id" => $id,
            "sortie" => $selectedSortie
        ]);
    }

    /**
     * @Route("/update/{id}", name="sorties_update", requirements={"id": "\d+"})
     */
    public function modifier(Request $request, SortieRepository $sortieRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        if ($selectedSortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier une sortie que vous n\'avez pas créé');
            return $this->redirectToRoute('main_home');
        }

        $duree = $selectedSortie->getDateHeureDebut()->diff($selectedSortie->getDuree());

        //Pour le remplissage des valeurs non mappées
        if ($duree->d != 0) {
            $duree_nb = $duree->d;
            $duree_unite = 2;
        } else {
            $duree_nb = $duree->h;
            $duree_unite = 1;
        }

        $form = $this->createForm(UpdateSortieType::class, $selectedSortie, [
            'nombre' => $duree_nb,
            'unite' => $duree_unite
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //Hydrater propriétés modifiées dindirectement
            $duree_nb = $form->get('duree_nombre')->getData(); //Récupère la durée
            $duree_unite = $form->get('duree_unite')->getData(); //Récupère l'unité de durée

            $fin = $this->calculerDateFin($form->getData()->getDateHeureDebut(), $duree_nb, $duree_unite);
            $selectedSortie->setDuree($fin);

            //Envoi à la BDD
            $entityManager->persist($selectedSortie);
            $entityManager->flush();

            $this->addFlash('success', 'Votre sortie a bien été modifiée');
            return $this->redirectToRoute('sorties_detail', [
                'id' => $id
            ]);
        }

        return $this->render('sorties/updateSortie.html.twig', [
            'updateSortie' => $form->createView(),
            'sortie' => $selectedSortie
        ]);
    }

    /**
     * @Route("/suppr/{id}", name="sorties_suppr", requirements={"id": "\d+"})
     */
    public function supprimer(SortieRepository $sortieRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        } elseif ($selectedSortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('danger', 'Votre ne pouvez pas supprimer une sortie que vous n\'avez pas organisé !');
            return $this->redirectToRoute('main_home');
        } else {
            $entityManager->remove($selectedSortie);
            $entityManager->flush();
        }

        $this->addFlash('info', 'Votre sortie à bien été supprimée');
        return $this->redirectToRoute('main_home');
    }


    private function calculerDateFin(\DateTime $debut, int $nombre, int $unite)
    {
        //Nombres : correspond
        //Unite : 1 > Heures, 2 > Jours

        //On utilise clone sinon $debut se met à jour en même temmps que $fin et la valeur initiale est donc perdue
        $fin = clone $debut;
        switch ($unite) {
            case 1:
                $fin->modify('+' . $nombre . 'hours');
                break;
            default:
                $fin->modify('+' . $nombre . 'days');
                break;
        }

        return $fin; //Qui est devenu la date de fin ^^

    }
}