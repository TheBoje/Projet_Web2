<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/account", name="account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/welcome")
     */
    public function welcomeAction() : Response
    {
        return $this->render("vues/account/welcome.html.twig");
    }


    /**
     * @Route("/connect")
     */
    public function connectAction() : Response
    {
        return $this->render("vues/account/connect.html.twig");
    }

    /**
     * @Route("/create")
     */
    public function createAccountAction() : Response
    {
        return $this->render("vues/account/createAccount.html.twig");
    }

    /**
     * @Route("/disconnect")
     */
    public function disconnectAction() : Response
    {
        return $this->render("vues/account/disconnect.html.twig");
    }

    /**
     * @Route("/edit")
     */
    public function editProfileAction() : Response
    {
        return $this->render("vues/account/editProfile.html.twig");
    }
}
