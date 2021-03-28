<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/edit/user/{id}", name = "editUser")
     */
    public function editUserAction(int $id) : Response
    {
        return $this->render("vues/admin/editUser.html.twig");
    }

    /**
     * @return Response
     *
     * @Route("/list/users", name = "listUsers")
     */
    public function listUsersAction() : Response
    {
        return $this->render("vues/admin/listUsers.html.twig");
    }

    /**
     * @Route("/edit/products", name = "editProducts")
     */
    public function editProductsAction() : Response
    {
        return $this->render("vues/admin/editProducts.html.twig");
    }
}
