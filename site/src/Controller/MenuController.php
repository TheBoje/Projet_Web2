<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/", name = "account_welcome")
     */
    public function welcomeAction() : Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        return $this->render("vues/account/welcome.html.twig", ['isAdmin' => $user->getIsAdmin()]);
    }


    public function menuAction(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        $nbProducts = count($em->getRepository(Product::class)->findAll());

        return $this->render('commons/_menu.html.twig', ['isAdmin' => $user->getIsAdmin(), 'nbProducts' => $nbProducts]);
    }
}
