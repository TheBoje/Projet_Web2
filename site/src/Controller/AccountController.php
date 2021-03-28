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
     * @Route("/welcome", name = "account_welcome")
     */
    public function welcomeAction() : Response
    {
        return $this->render("vues/account/welcome.html.twig");
    }


    /**
     * @Route("/connect", name = "account_connect")
     */
    public function connectAction() : Response
    {
        return $this->render("vues/account/connect.html.twig");
    }

    /**
     * @Route("/create", name = "account_createAccount")
     */
    public function createAccountAction() : Response
    {
        return $this->render("vues/account/createAccount.html.twig");
    }

    /**
     * @Route("/disconnect", name = "account_disconnect")
     */
    public function disconnectAction() : Response
    {
        return $this->render("vues/account/disconnect.html.twig");
    }

    /**
     * @Route("/edit", name = "account_editProfile")
     */
    public function editProfileAction() : Response
    {
        return $this->render("vues/account/editProfile.html.twig");
    }
}
