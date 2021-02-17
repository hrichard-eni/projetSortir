<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{

    /**
     * @Route("/admin/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Hydrater les propriétés manquantes
            $user->setAdministrateur(false);
            $user->setActif(true);

            //Ajout du role par défaut pour les nouveaux utilisateurs
            //NOTE : Le role admin s'obtient en remplacant par ROLE_ADMIN dans la BDD
            $user->setRoles(['ROLE_USER']);

            //Hashage du mot de passe pour sécuriser
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();


            //On rajoute un petit message si le formulaire soumis est validé
            $this->addFlash('success', 'Nouvel utilisateur ajouté');
            return $this->redirectToRoute('main_home');
        }

        return $this->render('registration/register.html.twig', [
            'register' => $form->createView(),
        ]);
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
//        if ($this->getUser()) {
//            return $this->redirectToRoute('main_home');
//        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/admin/listeUtilisateurs", name="liste_users")
     */
    public function listerUsers(ParticipantRepository $participantRepository)
    {
        $participants = $participantRepository->findAll();

        return $this->render('participant/listeUsers.html.twig', [
            'liste' => $participants
        ]);
    }

    /**
     * @Route("/admin/remove/{id}", name="remove_user", requirements={"id":"\d+"})
     */
    public function supprUser(ParticipantRepository $participantRepository, int $id,
                              EntityManagerInterface $entityManager)
    {
        $participant = $participantRepository->find($id);

        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('info', 'L\'utilisateur '.$participant->getPseudo().' a bien été supprimé');
        return $this->redirectToRoute('liste_users');
    }

}