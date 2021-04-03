<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/product", name="product_")
 */
class ProductController extends AbstractController
{

    public function isAllowedUser()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        if (!$this->getParameter('is-auth') || $user->getIsAdmin())
            throw $this->createNotFoundException('You\'re not allowed here');
    }

    /**
     * Liste les produits pour les ajouter au panier
     * Utilisation d'un formulaire classique
     *
     * @Route("", name = "productList")
     */
    public function productListAction(Request $request): Response
    {
        // Cette page n'est accessible que pour les utilisateur connecté non admin
        $this->isAllowedUser();
        // Récupération des repos
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);
        // Récupération des produits
        $client = $userRepository->find($this->getParameter('id-user'));
        $products = $productRepository->findAll();
        // Création d'une liste d'ordres à partir des produits dans la bdd
        $orders = [];
        foreach ($products as $index => $product) {
            $orders[$index] = new Order();
            $orders[$index]->setClient($client);
            $orders[$index]->setProduct($product);
            $orders[$index]->setQuantity($product->getQuantity());
        }

        // Récupération du formulaire via post
        if ($request->isMethod('POST')) {
            // Nombre d'articles à ajouter au panier
            // utilisé pour le retour flash
            $count = 0;

            // Parcours de tous les produits de la bdd
            foreach ($products as $product) {
                // Récupération de l'ID du produit
                $index = $product->getId();
                // La quantité du produit à ajouter au panier est dans la réponse du
                // formulaire avec l'ID du produit en tant qu'identifiant
                $quantity_form = $request->request->get($index);
                // Vérification que la quantité commandée est positive et qu'elle est aussi
                // inférieure ou égale à celle du produit (pour ne pas commander plus que le stock)
                if ($quantity_form > 0 && $product->getQuantity() >= $quantity_form) {
                    // On cherche si l'utilisateur a déjà une commande pour ce produit
                    $existing_order = $orderRepository->findOneBy(['client' => $client, 'product' => $product]);
                    // Si la commande existe, on y ajoute la quantité
                    if (isset($existing_order)) {
                        $existing_order->setQuantity($existing_order->getQuantity() + $quantity_form);
                    }
                    // Sinon, on créé un nouveau ordrer
                    else {
                        $new_order = new Order();
                        $new_order->setProduct($product);
                        $new_order->setQuantity($quantity_form);
                        $new_order->setClient($client);
                        $em->persist($new_order);
                    }
                    // On décrémente la quantité de la commande à la quantité du produit dans la bdd
                    $product->setQuantity($product->getQuantity() - $quantity_form);
                    // On compte l'order comme validé, et on l'ajoute au compteur
                    $count++;
                }
            }

            // Ajout des order dans la bdd
            if ($count > 0) {
                $em->flush();
                $this->addFlash('info', 'Ajout au panier de ' . $count . ' article(s) réussi');
            }
            else {
                $this->addFlash('info', 'Erreur dans l\'ajout au panier');
            }
        }

        return $this->render("vues/product/productList.html.twig", ['orders' => $orders]);

    }

    /**
     * @Route("/orders/list",
     *     name = "listOrders")
     */
    public function listOrdersAction(): Response
    {
        $this->isAllowedUser();

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $userRepository = $em->getRepository(User::class);
        $productRepository = $em->getRepository(Product::class);

        $user = $userRepository->find($this->getParameter('id-user'));
        $orders = $orderRepository->findBy(array('client' => $user));

        return $this->render("vues/product/listOrders.html.twig", ['orders' => $orders]);
    }

    /**
     * @param int $orderId
     * @return Response
     *
     * @Route("orders/delete/{orderId}",
     *     name = "deleteOrder",
     *     requirements = { "userId" = "[1-9]\d*", "orderId" = "[1-9]\d*"})
     */
    public function deleteOrderAction(int $orderId): Response
    {
        $this->isAllowedUser();

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Product::class);

        // On récupère la commande ainsi que le produit concerné
        $order = $orderRepository->find($orderId);
        $storedProduct = $productRepository->find($order->getProduct()->getId());

        // On modifie la quantité de produit et on efface la commande du panier
        $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
        $em->remove($order);

        $em->flush();

        return $this->redirectToRoute('product_listOrders', ['id' => $this->getParameter('id-user')]);
    }

    /**
     * Vide le panier d'un utilisateur
     *
     * @Route("orders/empty",
     *     name = "emptyOrders")
     */
    public function emptyOrdersAction(): Response
    {
        $this->isAllowedUser();

        $this->emptyOrders();

        return $this->redirectToRoute('product_listOrders');
    }

    private function emptyOrders($isBuyed = false)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);

        $orders = $orderRepository->findBy(array('client' => $userRepository->find($this->getParameter('id-user'))));

        foreach ($orders as $order) {
            if (!$isBuyed) {
                $storedProduct = $productRepository->find($order->getProduct()->getId());
                $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
            }
            $em->remove($order);
        }

        $em->flush();
    }

    /**
     * @Route("orders/buy", name = "buyOrders")
     */
    public function buyOrdersAction(): Response
    {
        $this->isAllowedUser();

        $this->emptyOrders(true);

        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction(): Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
