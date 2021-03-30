<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
    public function productListAction(Request $request) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();
        $orders = [];
        foreach ($products as $index=>$product)
        {
            $orders[$index] = new Order();
            $orders[$index]->setIdClient($this->getParameter('id-user'));
            $orders[$index]->setIdProduct($product->getId());
            $orders[$index]->setQuantity($product->getQuantity());
            // Ajout du nom du produit ?
        }
        dump($orders);
        // Ajout de tous les produits à 0 qqt
        // Ajout du client id au client en cours

        $form = $this->createForm(OrderType::class, $orders);
        $form->add('send', SubmitType::class, ['label'=>'Commander']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            dump($orders);
            foreach ($orders as $index=>$order)
            {
                if ($order->getQuantity() >= 0)
                {
                    $em->persist($order);
                }
            }

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
