<?php

namespace App\Controller\Api;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiSortiesController extends AbstractController
{
    /**
     * @Route("/api/v1/sorties", name="api_sorties_list", methods={"GET"})
     */

    //Objectif : aller chercher toutes les sorties en BDD & les renvoyer en JSON
    public function list(SortieRepository $sortieRepository, SerializerInterface $serializer): Response
    {
        $sorties = $sortieRepository->findAll();
        // Transformer le tableau sorties en chaîne
        $json = $serializer->serialize($sorties, 'json', ['groups' => 'sorties_list']);
        // On renvoie nos data ($json), le code de status confirmant que tout s'est bien passé (200), un tableau d'entêtes vides et un booléen qui indique si nos datas sont déjà converties en JSON
        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/v1/sorties/{id}", name="api_sorties_detail", methods={"GET"})
     */

    //Objectif : aller chercher toutes les sorties en BDD & les renvoyer en JSON
    public function detail(Sortie $sortie, SerializerInterface $serializer): Response
    {

        // Transformer le tableau sorties en chaîne
        $json = $serializer->serialize($sortie, 'json', ['groups' => 'sorties_list']);
        // On renvoie nos data ($json), le code de status confirmant que tout s'est bien passé (200), un tableau d'entêtes vides et un booléen qui indique si nos datas sont déjà converties en JSON
        return new JsonResponse($json, 200, [], true);
    }

}
