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
    public function productListAction(Request $request) : Response
    {
        $this->isAllowedUser();

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
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
