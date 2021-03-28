<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/product", name="product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("", name = "productList")
     */
    public function productListAction() : Response
    {
        return $this->render("vues/product/productList.html.twig");
    }

    /**
     * @Route("/orders", name = "orders")
     */
    public function ordersAction() : Response
    {
        return $this->render("vues/product/orders.html.twig");
    }

}
