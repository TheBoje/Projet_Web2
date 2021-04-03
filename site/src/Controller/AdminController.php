<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
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

    // Vérifie si l'utilisateur est connecté et est bien administrateur
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
    public function deleteUserAction(int $id): Response
    {
        // il faut être admin pour pouvoir modifier le tout
        $this->isAccessGranted();
        // Récupération de l'utilisateur
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('App:User');
        $user = $userRepository->find($id);
        // Vide le panier de l'utilisateur avant de le supprimer
        $this->emptyOrders($id);
        // Retire l'utilisateur de la base de données
        $em->remove($user);
        $em->flush();
        // Retour à la liste des utilisateurs
        return $this->redirectToRoute('admin_listUsers');
    }

    private function isAccessGranted()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        if (!$this->getParameter('is-auth') || $user === null || !$user->getIsAdmin()) {
            throw $this->createNotFoundException('You\'re not allowed here');
        }
    }

    /**
     * Vide le panier de l'utilisateur $id
     * @param $id
     */
    private function emptyOrders($id)
    {
        // Récupération du panier de l'utilisateurs
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);

        $orders = $orderRepository->findBy(array('client' => $userRepository->find($id)));
        // On retire les produits du panier et on les rajoute aux produits disponibles
        foreach ($orders as $order) {
            $storedProduct = $productRepository->find($order->getProduct()->getId());
            $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
            $em->remove($order);
        }
        $em->flush();
    }

    /**
     * Liste les utilisateur, et ajoute la possibilité de le supprimer
     * @return Response
     *
     * @Route("/list/users", name = "listUsers")
     */
    public function listUsersAction(): Response
    {
        // il faut être admin pour pouvoir modifier le tout
        $this->isAccessGranted();
        // Récupération de tous les utilisateurs
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findAll();
        // C'est pour cela qu'on envoie true à isAdmin pour le twig étant donné qu'une erreur 404 est levée si c'est le cas
        return $this->render("vues/admin/listUsers.html.twig", ['users' => $users]);
    }

    /**
     * Liste tous les produits pour pouvoir selectionner celui que l'on souhaite modifier
     *
     * @Route("/edit/products", name = "editProducts")
     */
    public function editProductsAction(): Response
    {
        // Il faut être admin pour pouvoir accéder à cette page
        $this->isAccessGranted();
        // Récupération de tous les produits
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();
        // Passage des produits au twig qui les affiche dans un tableau.
        // Créé un lien vers editProductId pour chaque produit.
        return $this->render('vues/admin/editProducts.html.twig', ['products' => $products]);
    }

    /**
     * Fenetre d'édition du produit d'identifiant {id}
     *
     * @Route("/edit/product/{id}",
     *     name = "editProductID",
     *     requirements = {"id" = "[1-9]\d*"})
     */
    public function editProductAction($id, Request $request): Response
    {
        // Il faut être admin pour pouvoir accéder à cette page
        $this->isAccessGranted();
        // Récupération du produit $id
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find($id);
        // Création du formulaire à partir du produit
        $form = $this->createForm(ProductType::class, $product);
        $form->add('send', SubmitType::class, ['label' => 'Modifier']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Modification du produit dans la base de données
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Modification terminée');
            // Retour vers la page de liste des produits
            return $this->redirectToRoute('admin_editProducts');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('info', 'Erreur lors de la modification');
        }

        $args = array('form_edit_product' => $form->createView(), 'id' => $id);
        return $this->render('vues/admin/editProduct.html.twig', $args);
    }

    /**
     * Ajout d'un produit dans la base de données
     *
     * @Route("/add/product", name = "addProduct")
     */
    public function addProductAction(Request $request): Response
    {
        // Il faut être admin pour pouvoir accéder à cette page
        $this->isAccessGranted();

        $em = $this->getDoctrine()->getManager();

        $product = new Product();
        // Création du formulaire
        $form = $this->createForm(ProductType::class, $product);
        $form->add('send', SubmitType::class, ['label' => 'Ajouter le produit']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout du produit dans la base de données
            $em->persist($product);
            $em->flush();
            $this->addFlash('info', 'Formulaire valide, ajout à la base de données');
            return $this->redirectToRoute('account_welcome');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('info', 'Erreur lors de la création');
        }

        $args = array('form_add_product' => $form->createView());
        return $this->render('vues/admin/addProduct.html.twig', $args);
    }

    /**
     * Supprime le produit de la base de données
     *
     * @Route("/delete/product/{id}",
     *     name = "deleteProduct",
     *     requirements = {"id" = "[1-9]\d*"})
     */
    public function deleteProductAction($id): Response
    {
        // Il faut être admin pour pouvoir accéder à cette page
        $this->isAccessGranted();
        // Récupération du produit $id
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository('App:Product');
        $product = $productRepository->find($id);
        // On retire le produit de la base de données
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_editProducts');
    }
}
