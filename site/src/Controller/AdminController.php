<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    private function isAccessGranted()
    {
        if(!$this->getParameter('is-auth') || !$this->getParameter('is-admin'))
        {
            throw $this->createNotFoundException('You\'re not allowed here');
        }
    }


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
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render("vues/admin/listUsers.html.twig", ['users' => $users]);
    }

    /**
     * @Route("/edit/products", name = "editProducts")
     */
    public function editProductsAction() : Response
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('vues/admin/editProducts.html.twig', ['products'=>$products]);
    }

    /**
     * @Route("/edit/product/{id}", name = "editProductID")
     */
    public function editProductAction($id) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find($id);

        return $this->render('vues/admin/editProduct.html.twig', ['product'=>$product]);
    }

    /**
     * @Route("/add/product", name = "addProduct")
     */
    public function addProductAction(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->add('send', SubmitType::class, ['label'=>'add product']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Formulaire valide, ajout à la base de données');
            return $this->redirectToRoute('admin_editProducts');
        }

        if ($form->isSubmitted()){
            $this->addFlash('info', 'Erreur lors de la création');
        }
        $args = array('form_add_product'=>$form->createView());
        return $this->render('vues/admin/addProduct.html.twig', $args);
    }

}
