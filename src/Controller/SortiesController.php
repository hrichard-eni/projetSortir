<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\NewSortieType;
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

        //récupérer la liste des participants
        $participants = $selectedSortie->getParticipants()->toArray();
        //dd($participants->getId());

        //récupérer l'id de l'user
        $user=$this->getUser()->getId();

        //Créer un tableau des Id des participants à la sortie
        $participantsId = [];

        //vérifier si l'Id de l'user fait partie des Id des participants à la sortie
        //Cela permet ensuite dans le detail de la sortie d'afficher ou non le bouton s'inscrire
        foreach ($participants as $p){
            array_push($participantsId, $p->getId());
        }
        $estInscrit=false;
        $estInscrit=in_array($user, $participantsId)? true : false;

        if (!$selectedSortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        return $this->render('sorties/detailSortie.html.twig', [
            "id" => $id,
            "sortie" => $selectedSortie,
            "estInscrit"=>$estInscrit
        ]);
    }


    private function calculerDateFin(\DateTime $debut , int $nombre, int $unite) {
        //Nombres : correspond
        //Unite : 1 > Heures, 2 > Jours

        //On utilise clone sinon $debut se met à jour en même temmps que $fin et la valeur initiale est donc perdue
        $fin = clone $debut;
        switch ($unite) {
            case 1:
                $fin->modify('+'.$nombre.'hours');
                break;
            default:
                $fin->modify('+'.$nombre.'days');
                break;
        }

        return $fin; //Qui est devenu la date de fin ^^

    }
}