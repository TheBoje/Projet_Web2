<?php

namespace App\Controller;

use App\Entity\Product;
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
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render("vues/product/productList.html.twig", ['products'=>$products]);
    }

    /**
     * @Route("/orders", name = "orders")
     */
    public function ordersAction() : Response
    {
        return $this->render("vues/product/orders.html.twig");
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
