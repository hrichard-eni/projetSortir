<?php


namespace App\Controller;


use App\Entity\Participant;
use App\Form\ParticipantFormType;
use claviska\SimpleImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/profil/", name="participant_profil")
     */
    public function profil(Request $request,
                           EntityManagerInterface $entityManager,
                           UserPasswordEncoderInterface $passwordEncoder,
                           string $uploadDir): Response
    {

        //On récupère les infos de l'utilisateur connecté pour lui afficher directement dans le formulaire
        $user = $this->getUser();
        $participant = $entityManager->getRepository(Participant::class)->find($user->getId());

        if ($user) {
            $participant->setPseudo($user->getPseudo());
            $participant->setNom($user->getNom());
            $participant->setPrenom($user->getPrenom());
            $participant->setTelephone($user->getTelephone());
            $participant->setEmail($user->getEmail());
        }
        $form = $this->createForm(ParticipantFormType::class, $participant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            /** @var UploadedFile $avatar */
            $avatar = $form->get('avatar')->getData();

            if ($avatar) {

                $newFilename = md5(uniqid()) . "." . $avatar->guessExtension();
                $avatar->move($uploadDir, $newFilename);
                $participant->setUrlAvatar($newFilename);

                $image = new SimpleImage();
                $image->fromFile($uploadDir . $newFilename)
                    ->bestFit(200, 200)
                    ->toFile($uploadDir . "small/" . $newFilename);
            }


            $entityManager->persist($participant);
            $entityManager->flush();

            //On rajoute un petit message si le formulaire soumis est validé
            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute("participant_profil", []);
        }

        return $this->render('participant/profil.html.twig',
            ["participant_form" => $form->createView(),
                "participant" => $participant]);

    }


}