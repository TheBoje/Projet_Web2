<?php

namespace App\Controller;

use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/add/product", name = "addProduct")
     */
    public function addProductAction(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProductType::class);
        $form->add('send', SubmitType::class, ['label'=>'add product']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();
            $this->addFlash('info', 'Formulaire valide, ajout à la base de données');
            return $this->redirectToRoute('admin_editProducts');
        }

        $args = array('form_add_product'=>$form->createView());
        return $this->render('vues/admin/addProduct.html.twig', $args);
    }

}
