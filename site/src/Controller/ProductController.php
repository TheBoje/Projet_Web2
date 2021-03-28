<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller
 *
 * @Route("/product", name="product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("", name = "product_productList")
     */
    public function productListAction() : Response
    {
        return $this->render("vues/product/productList.html.twig");
    }

    /**
     * @Route("/orders", name = "product_orders")
     */
    public function ordersAction() : Response
    {
        return $this->render("vues/product/orders.html.twig");
    }

}
