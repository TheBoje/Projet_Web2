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
     * @Route("/orders/{id}",
     *     name = "orders",
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
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
