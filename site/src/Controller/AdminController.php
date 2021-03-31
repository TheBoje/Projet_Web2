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

    // Vérifie si l'utilisateur est connecté et est bien administrateur
    private function isAccessGranted()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        if(!$this->getParameter('is-auth') || !$user->getIsAdmin() || $user === null)
        {
            throw $this->createNotFoundException('You\'re not allowed here');
        }
    }

    /**
     * Efface un utilisateur de la base de donnée
     *
     * @Route(
     *     "/delete/user/{id}",
     *     name = "deleteUser",
     *     requirements = {"id" = "[1-9]\d*"})
     * @param int $id
     * @return Response
     */
    public function deleteUserAction(int $id) : Response
    {
        // TODO vider le panier de l'utilisateur
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('App:User');

        $user = $userRepository->find($id);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_listUsers');
    }

    /**
     * @return Response
     *
     * @Route("/list/users", name = "listUsers")
     */
    public function listUsersAction() : Response
    {
        // il faut être admin pour pouvoir modifier le tout
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();

        // C'est pour cela qu'on envoie true à isAdmin pour le twig étant donné qu'une erreur 404 est levée si c'est le cas
        return $this->render("vues/admin/listUsers.html.twig", ['users' => $users]);
    }

    /**
     * @Route("/edit/products", name = "editProducts")
     */
    public function editProductsAction() : Response
    {
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('vues/admin/editProducts.html.twig', ['products'=>$products]);
    }

    /**
     * @Route("/edit/product/{id}", name = "editProductID")
     */
    public function editProductAction($id, Request $request) : Response
    {
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        $form->add('send', SubmitType::class, ['label' => 'Modifier']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Modification terminée');
            return $this->redirectToRoute('admin_editProducts');
        }

        if ($form->isSubmitted())
        {
            $this->addFlash('info', 'Erreur lors de la modification');
        }
        $args = array('form_edit_product' =>$form->createView(), 'id'=>$id);
        return $this->render('vues/admin/editProduct.html.twig', $args);
    }

    /**
     * @Route("/add/product", name = "addProduct")
     */
    public function addProductAction(Request $request) : Response
    {
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->add('send', SubmitType::class, ['label'=>'Ajout le produit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Formulaire valide, ajout à la base de données');
            return $this->redirectToRoute('account_welcome');
        }

        if ($form->isSubmitted()){
            $this->addFlash('info', 'Erreur lors de la création');
        }
        $args = array('form_add_product'=>$form->createView());
        return $this->render('vues/admin/addProduct.html.twig', $args);
    }

    /**
     * @Route("/delete/product/{id}", name = "deleteProduct")
     */
    public function deleteProductAction($id) : Response {
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository('App:Product');

        $product = $productRepository->find($id);
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_editProducts');
    }
}
