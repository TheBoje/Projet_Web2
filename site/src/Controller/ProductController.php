<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Form\OrderType;
use http\Client;
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
        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);
        $client = $userRepository->find($this->getParameter('id-user'));
        $products = $productRepository->findAll();
        $orders = [];
        foreach ($products as $index=>$product)
        {
            $orders[$index] = new Order();
            $orders[$index]->setClient($client);
            $orders[$index]->setProduct($product);
            $orders[$index]->setQuantity($product->getQuantity());
        }

        if ($request->isMethod('POST'))
        {
            $count = 0;
            foreach ($products as $product)
            {
                $temp_order = null;
                $index = $product->getId();
                $quantity_form = $request->request->get($index);
                if ($quantity_form > 0){
                    $existing_order = $orderRepository->findOneBy(['client'=>$client, 'product'=>$product]);
                    if (isset($existing_order))
                    {
                        $existing_order->setQuantity($existing_order->getQuantity() + $quantity_form);
                    }else{
                        $temp_order = new Order();
                        $temp_order->setProduct($product);
                        $temp_order->setQuantity($quantity_form);
                        $temp_order->setClient($client);
                        $em->persist($temp_order);
                    }
                    $product->setQuantity($product->getQuantity() - $quantity_form);
                    $count++;
                }
            }
            if ($count > 0){
                $em->flush();
                $this->addFlash('info', 'Ajout au panier de '. $count .' article(s) réussi');
            }
            else
            {
                $this->addFlash('info', 'Erreur dans l\'ajout au panier');
            }
        }

        return $this->render("vues/product/productList.html.twig", ['orders'=>$orders]);

    }


    /**
     * @Route("/orders/list",
     *     name = "listOrders")
     */
    public function listOrdersAction() : Response
    {
        $id = $this->getParameter('id-user');
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
     * @Route("orders/delete/{orderId}",
     *     name = "deleteOrder",
     *     requirements = { "userId" = "[1-9]\d*", "orderId" = "[1-9]\d*"})
     */
    public function deleteOrderAction(int $orderId) : Response
    {
        $this->isAllowedUser($this->getParameter('id-user'));

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

    private function emptyOrders($isBuyed = false)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);

        $orders = $orderRepository->findBy(array('client' => $userRepository->find($this->getParameter('id-user'))));

        foreach($orders as $order)
        {
            if(!$isBuyed)
            {
                $storedProduct = $productRepository->find($order->getProduct()->getId());
                $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
            }
            $em->remove($order);
        }

        $em->flush();
    }

    /**
     * Vide le panier d'un utilisateur
     *
     * @Route("orders/empty",
     *     name = "emptyOrders")
     */
    public function emptyOrdersAction() : Response
    {
        $this->isAllowedUser($this->getParameter('id-user'));

        $this->emptyOrders();

        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * @Route("orders/buy", name = "buyOrders")
     */
    public function buyOrdersAction() : Response
    {
        $this->isAllowedUser($this->getParameter('id-user'));

        $this->emptyOrders(true);

        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction() : Response
    {
        return $this->redirectToRoute('product_productList');
    }

}
