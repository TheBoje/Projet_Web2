<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/admin", name="admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/edit/user")
     */
    public function editUserAction() : Response
    {
        return $this->render("vues/admin/editUser.html.twig");
    }

    /**
     * @Route("/edit/products")
     */
    public function editProductsAction() : Response
    {
        return $this->render("vues/admin/editProducts.html.twig");
    }
}
