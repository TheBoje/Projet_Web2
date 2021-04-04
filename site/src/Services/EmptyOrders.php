<?php


namespace App\Services;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class EmptyOrders
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function emptyOrders($id, $isBuyed = false)
    {
        // Récupération du panier de l'utilisateurs
        $orderRepository = $this->em->getRepository(Order::class);
        $productRepository = $this->em->getRepository(Product::class);
        $userRepository = $this->em->getRepository(User::class);

        $orders = $orderRepository->findBy(array('client' => $userRepository->find($id)));

        // On retire les produits du panier et on les rajoute aux produits disponibles
        foreach ($orders as $order)
        {
            if (!$isBuyed)
            {
                $storedProduct = $productRepository->find($order->getProduct()->getId());
                $storedProduct->setQuantity($storedProduct->getQuantity() + $order->getQuantity());
            }
            $this->em->remove($order);
        }

        $this->em->flush();
    }
}