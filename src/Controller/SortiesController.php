<?php

namespace App\Controller;

use App\Entity\Archive;
use App\Entity\Sortie;
use App\Form\NewSortieType;
use App\Form\UpdateSortieType;
use App\Repository\ArchiveRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\See;
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
            //Hydrater la propriété état à 'Créé' ou 'Ouverte'
            if ($form->get('publish') == true) {
                $sortie->setEtat($etatRepository->find(2));
            } else {
                $sortie->setEtat($etatRepository->find(1));
            }

            $duree_nb = $form->get('duree_nombre')->getData(); //Récupère la durée
            $duree_unite = $form->get('duree_unite')->getData(); //Récupère l'unité de durée

            $fin = $this->calculerDateFin($form->getData()->getDateHeureDebut(), $duree_nb, $duree_unite);

            $sortie->setDuree($fin);

            //Envoi à la BDD
            $entityManager->persist($sortie);
            $entityManager->flush();

            //On rajoute un petit message si le formulaire soumis est validé
            $this->addFlash('success', 'Sortie ajoutée avec succès');

            //Renvoi sur la page d'accueuil
            return $this->redirectToRoute('inscription_sortie', [
                'id' => $sortie->getId()
            ]);
        }

        //Si le formulaire n'est pas soumis affiche ce dernier
        return $this->render('sorties/newSortie.html.twig', [
            'newSortie' => $form->createView(),
        ]);
    }

    /**
     * @Route("/detail/{id}", name="sorties_detail", requirements={"id": "\d+"})
     */
    public function detail(SortieRepository $sortieRepository,
                           EtatRepository $etatRepository,
                           ArchiveRepository $archiveRepository, int $id,
                           EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        //Mettre à jour l'état de la sortie
        if ($selectedSortie->getEtat() != $etatRepository->find(1)
            && $selectedSortie->getEtat() != $etatRepository->find(6)
            && $selectedSortie->getEtat() != $etatRepository->find(7)) {

            if ($selectedSortie->getDuree() < new DateTime()) {
                //Date de fin passée -> PA
                $selectedSortie->setEtat($etatRepository->find(5));

            } elseif ($selectedSortie->getDateHeureDebut() < new DateTime()) {
                //Date de début passée -> EC
                $selectedSortie->setEtat($etatRepository->find(4));

            } elseif ($selectedSortie->getDateLimiteInscription() < new DateTime()
                || sizeof($selectedSortie->getParticipants()) >= $selectedSortie->getNbInscriptionsMax()) {
                //Date limite d'inscription passée OU nombre max de participants -> CL
                $selectedSortie->setEtat($etatRepository->find(3));

            } elseif (sizeof($selectedSortie->getParticipants()) < $selectedSortie->getNbInscriptionsMax()) {
                //Si les condidtions précédentes ne sont pas remplies et
                //qu'il reste de la place (cas du desistement) -> OU
                $selectedSortie->setEtat($etatRepository->find(2));
            }
            $entityManager->persist($selectedSortie);
            $entityManager->flush();
        }
        if ($selectedSortie->getEtat() == $etatRepository->find(5)
            && $selectedSortie->getDuree() <= date_add(new DateTime(), new DateInterval('P1M'))) {
            //Passer l'état de la sortie à 'Archivée'
            $selectedSortie->setEtat($etatRepository->find(7));
            //Ajout de la sortie dans la table d'archive
            $archive = new Archive();
            $archive->setSortie($selectedSortie);
            $entityManager->persist($archive);
            $entityManager->flush();
        }

        //récupérer la liste des participants
        $participants = $selectedSortie->getParticipants()->toArray();
        //dd($participants->getId());

        //récupérer l'id de l'user
        $user = $this->getUser()->getId();

        //Créer un tableau des Id des participants à la sortie
        $participantsId = [];

        //vérifier si l'Id de l'user fait partie des Id des participants à la sortie
        //Cela permet ensuite dans le detail de la sortie d'afficher ou non le bouton s'inscrire
        foreach ($participants as $p) {
            array_push($participantsId, $p->getId());
        }
        $estInscrit = false;
        $estInscrit = in_array($user, $participantsId) ? true : false;

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        return $this->render('sorties/detailSortie.html.twig', [
            "id" => $id,
            "sortie" => $selectedSortie,
            "estInscrit" => $estInscrit
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
     * @Route("/publish/{id}", name="sorties_publish", requirements={"id": "\d+"})
     */
    public function publier(SortieRepository $sortieRepository,
                            EtatRepository $etatRepository, int $id,
                            EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        } elseif ($selectedSortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('danger', 'Votre ne pouvez pas publier une sortie que vous n\'avez pas organisé !');
            return $this->redirectToRoute('main_home');
        } elseif ($selectedSortie->getEtat() == $etatRepository->find(2)) {
            $this->addFlash('danger', 'Votre ne pouvez pas republier une sortie déjà publiée');
            return $this->redirectToRoute('main_home');
        } else {
            $selectedSortie->setEtat($etatRepository->find(2));
            $entityManager->persist($selectedSortie);
            $entityManager->flush();
        }

        $this->addFlash('info', 'Votre sortie à bien été publiée');
        return $this->redirectToRoute("sorties_detail", ['id' => $id]);
    }

    /**
     * @Route("/cancel/{id}", name="sorties_cancel", requirements={"id": "\d+"})
     */
    public function annuler(SortieRepository $sortieRepository,
                            EtatRepository $etatRepository, int $id,
                            EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        } elseif ($selectedSortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('danger', 'Votre ne pouvez pas annuler une sortie que vous n\'avez pas organisé !');
            return $this->redirectToRoute('main_home');
        } elseif ($selectedSortie->getEtat() == $etatRepository->find(6)) {
            $this->addFlash('danger', 'Votre ne pouvez pas annuler une sortie déjà annulée');
            return $this->redirectToRoute('main_home');
        } else {
            $selectedSortie->setEtat($etatRepository->find(6));
            $entityManager->persist($selectedSortie);
            $entityManager->flush();
        }

        $this->addFlash('info', 'Votre sortie à bien été annulée');
        return $this->redirectToRoute("sorties_detail", ['id' => $id]);
    }

    /**
     * @Route("/recreate/{id}", name="sorties_recreate", requirements={"id": "\d+"})
     */
    public function recreer(SortieRepository $sortieRepository,
                            EtatRepository $etatRepository, int $id,
                            EntityManagerInterface $entityManager): Response
    {
        //Aller chercher en BDD la sortie correspondant à l'id passé dans l'URL
        $selectedSortie = $sortieRepository->find($id);

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        } elseif ($selectedSortie->getOrganisateur() != $this->getUser()) {
            $this->addFlash('danger', 'Votre ne pouvez pas recréer une sortie que vous n\'avez pas organisé !');
            return $this->redirectToRoute('main_home');
        } elseif ($selectedSortie->getEtat() != $etatRepository->find(6)) {
            $this->addFlash('danger', 'Votre ne pouvez pas recréer une sortie déjà créée');
            return $this->redirectToRoute('main_home');
        } else {
            $selectedSortie->setEtat($etatRepository->find(1));
            $entityManager->persist($selectedSortie);
            $entityManager->flush();
        }

        $this->addFlash('info', 'Votre sortie à bien été recréée, publiez la pour la rendre effective');
        return $this->redirectToRoute("sorties_detail", ['id' => $id]);
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


    private function calculerDateFin(DateTime $debut, int $nombre, int $unite)
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