<?php

namespace App\Controller;


use App\Entity\Filtre;
use App\Form\FiltresSortiesFormType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     * @param SortieRepository $sortieRepository
     * @return Response
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        //Afficher toutes les sorties

        $sorties = $sortieRepository->findAll();

        //Menu déroulant affichant les campus
        $filtrecampus = new Filtre();
        $form = $this->createForm(FiltresSortiesFormType::class, $filtrecampus);




        return $this->render('main/home.html.twig', [
            "sorties" => $sorties,
            "filtre_form" => $form->createView(),

        ]);


    }


}
