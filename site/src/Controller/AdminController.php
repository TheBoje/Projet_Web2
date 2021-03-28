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
     * @Route("/edit/user/{id}", name = "admin_editUser")
     */
    public function editUserAction(int $id) : Response
    {
        return $this->render("vues/admin/editUser.html.twig");
    }

    /**
     * @return Response
     *
     * @Route("/list/users", name = "admin_listUsers")
     */
    public function listUsersAction() : Response
    {
        return $this->render("vues/admin/listUsers.html.twig");
    }

    /**
     * @Route("/edit/products", name = "admin_editProducts")
     */
    public function editProductsAction() : Response
    {
        return $this->render("vues/admin/editProducts.html.twig");
    }
}
