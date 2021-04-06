<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Services\InvertString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * Page d'accueil
     * @Route("/", name = "menu_welcome")
     */
    public function welcomeAction(InvertString $invertString): Response
    {
        // Récupération de l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        return $this->render("vues/menu/welcome.html.twig",
            ['isAdmin' => $user->getIsAdmin(),
                'username' => $invertString->getInvertString($user->getLogin())]); // On utilise le service pour inverser le nom de l'utilisateur
    }

    /**
     * Transmet le nombre de produits disponibles et si l'utilisateur connecté est admin ou non
     * Utilisé dans _menu.html.twig
     *
     * @return Response
     */
    public function menuAction(): Response
    {
        // Récupération de l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));
        // Récupération du nombre de produits contenu dans la base
        $nbProducts = count($em->getRepository(Product::class)->findAll());

        return $this->render('commons/_menu.html.twig', ['isAdmin' => $user->getIsAdmin(), 'nbProducts' => $nbProducts]);
    }
}

/* ============================================================================
 * ============= Fichier créé par Vincent Commin et Louis Leenart =============
 * ============================================================================
 * */
