<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/welcome", name = "welcome")
     */
    public function welcomeAction() : Response
    {
        return $this->render("vues/account/welcome.html.twig");
    }


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
    public function createAccountAction() : Response
    {
        return $this->render("vues/account/createAccount.html.twig");
    }

    /**
     * @Route("/disconnect", name = "disconnect")
     */
    public function disconnectAction() : Response
    {
        return $this->render("vues/account/disconnect.html.twig");
    }

    /**
     * @Route("/edit", name = "editProfile")
     */
    public function editProfileAction() : Response
    {
        return $this->render("vues/account/editProfile.html.twig");
    }
}
