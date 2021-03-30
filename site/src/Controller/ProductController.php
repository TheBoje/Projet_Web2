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
    public function isAllowedUser(int $id)
    {
        if($this->getParameter('id-user') !== $id && $this->getParameter('is-auth')) // TODO : rajouter le is-admin ?
            throw $this->createNotFoundException('You\'re not allowed here');
    }

    /**
     * @Route("", name = "productList")
     */
    public function productListAction() : Response
    {
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
        $orders = $orderRepository->findBy(array('user' => $user));

        return $this->render("vues/product/orders.html.twig", ['orders' => $orders]);
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
