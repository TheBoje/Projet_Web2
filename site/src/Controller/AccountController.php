<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
     * @Route("/connect", name = "connect")
     */
    public function connectAction() : Response
    {
        return $this->render("vues/account/connect.html.twig");
    }

    /**
     * @Route("/create", name = "createAccount")
     */
    public function createAccountAction(Request $request) : Response
    {

        $em = $this->getDoctrine()->getManager();

        $user = new User(); // le compte créé ne sera jamais admin car le constructeur met admmin à false par défaut

        $form = $this->createForm(UserType::class, $user);
        $form->add('send', SubmitType::class, ['label' => 'Créer l\'utilisateur']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($user);
            $em->flush();
            $this->addFlash('info', 'Ajout d\'un utilisateur');
            return $this->redirectToRoute('account_welcome');
        }
        else
        {
            if($form->isSubmitted())
                $this->addFlash('info', 'Erreur lors de la création');
            
            $args = array('userForm' => $form->createView());
            return $this->render('vues/account/createAccount.html.twig', $args);
        }


    }

    /**
     * @Route("/disconnect", name = "disconnect")
     */
    public function disconnectAction() : Response
    {
        $this->addFlash('info', 'Vous pourrez vous déconnecter ultérieurement');
        return $this->redirectToRoute('account_welcome');
    }

    /**
     * @Route("/edit", name = "editProfile")
     */
    public function editProfileAction(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->find($this->getParameter('id-user'));

        $form = $this->createForm(UserType::class, $user);


        $form->add('send', SubmitType::class, ['label' => 'Editer le profil']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('info', 'Édition de l\'utilisateur');
            return $this->redirectToRoute('product_productList');
        }

        if($form->isSubmitted())
            $this->addFlash('info', 'Erreur lors de l\'édition');

        $args = array('userForm' => $form->createView());
        return $this->render('vues/account/editProfile.html.twig', $args);
    }
}
