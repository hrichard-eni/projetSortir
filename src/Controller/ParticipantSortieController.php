<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ParticipantSortieController extends AbstractController
{
    private $participants;

    /**
     * @Route("/inscriptionSortie/{id}", name="inscription_sortie",methods={"GET"}, requirements={"id": "\d+"})
     * @return string
     */
    public function ajouterParticipant($id, EntityManagerInterface $entityManager): Response
    {

        //qui est le participant ?
        $user = $this->getUser();

        $participantsortie = $entityManager->getRepository(Participant::class)->find($user->getId());
        $sortieSelectionnee = $entityManager->getRepository(Sortie::class)->find($id);

        //j'appelle la methode prévue par l'entity Sortie
             $sortieSelectionnee->addParticipant($participantsortie);
             $entityManager->persist($sortieSelectionnee);
             $entityManager->flush();
            $this->addFlash('success', 'Inscription réussie');
        return $this->redirectToRoute("sorties_detail", ['id'=> $id]);
            }

    /**
     * @Route("/desinscriptionSortie/{id}", name="desinscription_sortie",methods={"GET"}, requirements={"id": "\d+"})
     * @return string
     */
    public function retirerParticipant($id, EntityManagerInterface $entityManager): Response
    {

        //qui est le participant ?
        $user = $this->getUser();

        $participantsortie = $entityManager->getRepository(Participant::class)->find($user->getId());
        $sortieSelectionnee = $entityManager->getRepository(Sortie::class)->find($id);

        //j'appelle la methode prévue par l'entity Sortie
        $sortieSelectionnee->removeParticipant($participantsortie);
        $entityManager->persist($sortieSelectionnee);
        $entityManager->flush();
        $this->addFlash('success', 'Désinscription réussie');
        return $this->redirectToRoute("sorties_detail", ['id'=> $id]);
    }






}