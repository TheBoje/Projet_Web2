<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/product", name="product_")
 */
class ProductController extends AbstractController
{
    // TODO revenir sur ce code dégueulasse
    public function isAllowedUser(int $id = null)
    {
        if($id !== null)
        {
            if($this->getParameter('id-user') !== $id && $this->getParameter('is-auth') || $this->getParameter('is-admin'))
                throw $this->createNotFoundException('You\'re not allowed here');
        }
        else
        {
            if(!$this->getParameter('is-auth') || $this->getParameter('is-admin'))
                throw $this->createNotFoundException('You\'re not allowed here');
        }
    }

    /**
     * @Route("", name = "productList")
     */
    public function productListAction() : Response
    {
        $this->isAllowedUser();

        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render("vues/product/productList.html.twig", ['products'=>$products]);
    }


    /**
     * @Route("/orders/list/{id}",
     *     name = "listOrders",
     *     requirements = {"id" = "[1-9]\d*"})
     */
    public function listOrdersAction(int $id) : Response
    {
        $this->isAllowedUser($id);

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $userRepository = $em->getRepository(User::class);
        $productRepository = $em->getRepository(Product::class);

        $user = $userRepository->find($id);
        $orders = $orderRepository->findBy(array('client' => $user));

        return $this->render("vues/product/listOrders.html.twig", ['orders' => $orders]);
    }

    /**
     * @param int $userId
     * @param int $orderId
     * @return Response
     *
     * @Route("orders/delete/{userId}/{orderId}",
     *     name = "deleteOrder",
     *     requirements = { "userId" = "[1-9]\d*", "orderId" = "[1-9]\d*"})
     */
    public function deleteOrderAction(int $userId, int $orderId) : Response
    {
        $this->isAllowedUser($userId);

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

        return $this->redirectToRoute('product_listOrders', ['id' => $userId]);
    }

    /**
     * Vide le panier d'un utilisateur 
     *
     * @param int $id
     *
     * @Route("orders/empty/{id}",
     *     name = "emptyOrders",
     *     requirements = {"id" = "[1-9]\d*"})
     */
    public function emptyOrdersAction(int $id)
    {
        $this->isAllowedUser($id);

        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);

        $orders = $orderRepository->findBy(array('client' => $userRepository->find($id)));

        foreach($orders as $order)
        {
            $storedProduct = $productRepository->find($order->getProduct()->getId());
            $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
            $em->remove($order);
        }

        $em->flush();
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
