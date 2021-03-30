<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
    /**
     * @Route("", name = "productList")
     */
    public function productListAction(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();
        $order = new Order();

        $form = $this->createForm(OrderType::class, $products);
        $form->add('send', SubmitType::class, ['label'=>'Commander']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $order = $form->getData();
            $em->persist($order);
            $em->flush();
            $this->addFlash('info', 'Ajout au panier réussi');
            return $this->redirectToRoute('product_orders');
        }
        
        if ($form->isSubmitted())
        {
            $this->addFlash('info', 'Erreur lors de l\'ajout au panier');
        }

        return $this->render("vues/product/productList.html.twig", ['form_products'=>$form->createView()]);
    }


    /**
     * @Route("/orders/{id}",
     *     name = "orders",
     *     requirements = {"id" = "[1-9]\d*"})
     */
    public function ordersAction(int $id) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);

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
