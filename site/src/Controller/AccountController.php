<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/account", name="account_")
 */
class AccountController extends AbstractController
{
    /**
     * Page de connexion
     *
     * @Route("/connect", name = "connect")
     */
    public function connectAction(): Response
    {
        return $this->render("vues/account/connect.html.twig");
    }

    /**
     * Page de création de compte
     *
     * @Route("/create", name = "createAccount")
     */
    public function createAccountAction(Request $request): Response
    {

        $em = $this->getDoctrine()->getManager();

        $user = new User(); // le compte créé ne sera jamais admin car le constructeur met admmin à false par défaut
        // Création du formulaire
        $form = $this->createForm(UserType::class, $user);
        $form->add('send', SubmitType::class, ['label' => 'Créer l\'utilisateur']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout de l'utilisateur dans la base de données
            $em->persist($user);
            $em->flush();
            $this->addFlash('info', 'Ajout d\'un utilisateur');
            return $this->redirectToRoute('account_welcome');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('info', 'Erreur lors de la création');
        }

        $args = array('userForm' => $form->createView());
        return $this->render('vues/account/createAccount.html.twig', $args);


    }

    /**
     * Page de déconnexion
     * TODO Rendre la page fonctionnelle
     *
     * @Route("/disconnect", name = "disconnect")
     */
    public function disconnectAction(): Response
    {
        $this->addFlash('info', 'Vous pourrez vous déconnecter ultérieurement');
        return $this->redirectToRoute('account_welcome');
    }

    /**
     * Edition du profil
     * Permet de modifier tous les champs (sauf isAdmin)
     * @Route("/edit", name = "editProfile")
     */
    public function editProfileAction(Request $request): Response
    {
        // Récupération de l'utilisateur dans la base de données
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getParameter('id-user'));
        // Création du formulaire à partir des données utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->add('send', SubmitType::class, ['label' => 'Editer le profil']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout des modifications dans la base de données
            $em->flush();
            $this->addFlash('info', 'Édition de l\'utilisateur');
            return $this->redirectToRoute('product_productList');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('info', 'Erreur lors de l\'édition');
        }
        
        $args = array('userForm' => $form->createView());
        return $this->render('vues/account/editProfile.html.twig', $args);
    }
}

/* ============================================================================
 * ============= Fichier créé par Vincent Commin et Louis Leenart =============
 * ============================================================================
 * */