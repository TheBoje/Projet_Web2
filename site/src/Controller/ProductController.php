<?php

namespace App\Controller;


use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Services\EmptyOrders;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * Si l'utilisateur est authentifié mais pas Administrateur, alors la
     * page est accessible, sinon on renvoie vers une page erreur 404
     */
    public function isAllowedUser()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getParameter('id-user'));

        if (!$this->getParameter('is-auth') || $user->getIsAdmin()) {
            throw $this->createNotFoundException('You\'re not allowed here');
        }
    }

    /**
     * Liste les produits pour les ajouter au panier
     * Utilisation d'un formulaire classique
     *
     * @Route("", name = "productList")
     */
    public function productListAction(Request $request): Response
    {
        // Cette page n'est accessible que pour les utilisateur connecté non admin
        $this->isAllowedUser();
        // Récupération des repos
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository(Product::class);
        $userRepository = $em->getRepository(User::class);
        $orderRepository = $em->getRepository(Order::class);
        // Récupération des produits
        $client = $userRepository->find($this->getParameter('id-user'));
        $products = $productRepository->findAll();
        // Création d'une liste d'ordres à partir des produits dans la bdd
        $orders = [];
        foreach ($products as $index => $product) {
            $orders[$index] = new Order();
            $orders[$index]->setClient($client);
            $orders[$index]->setProduct($product);
            $orders[$index]->setQuantity($product->getQuantity());
        }

        // Récupération du formulaire via post
        if ($request->isMethod('POST')) {
            // Nombre d'articles à ajouter au panier
            // utilisé pour le retour flash
            $count = 0;

            // Parcours de tous les produits de la bdd
            foreach ($products as $product) {
                // Récupération de l'ID du produit
                $index = $product->getId();
                // La quantité du produit à ajouter au panier est dans la réponse du
                // formulaire avec l'ID du produit en tant qu'identifiant
                $quantity_form = $request->request->get($index);
                // Vérification que la quantité commandée est positive et qu'elle est aussi
                // inférieure ou égale à celle du produit (pour ne pas commander plus que le stock)
                if ($quantity_form > 0 && $product->getQuantity() >= $quantity_form) {
                    // On cherche si l'utilisateur a déjà une commande pour ce produit
                    $existing_order = $orderRepository->findOneBy(['client' => $client, 'product' => $product]);
                    // Si la commande existe, on y ajoute la quantité
                    if (isset($existing_order)) {
                        $existing_order->setQuantity($existing_order->getQuantity() + $quantity_form);
                    }
                    // Sinon, on créé un nouveau ordrer
                    else {
                        $new_order = new Order();
                        $new_order->setProduct($product);
                        $new_order->setQuantity($quantity_form);
                        $new_order->setClient($client);
                        $em->persist($new_order);
                    }
                    // On décrémente la quantité de la commande à la quantité du produit dans la bdd
                    $product->setQuantity($product->getQuantity() - $quantity_form);
                    // On compte l'order comme validé, et on l'ajoute au compteur
                    $count++;
                }
            }

            // Ajout des order dans la bdd
            if ($count > 0) {
                $em->flush();
                $this->addFlash('info', 'Ajout au panier de ' . $count . ' article(s) réussi');
            }
            else {
                $this->addFlash('info', 'Erreur dans l\'ajout au panier');
            }
        }

        return $this->render("vues/product/productList.html.twig", ['orders' => $orders]);

    }

    /**
     * Affichage du panier de l'utilisateur connecté
     *
     * @Route("/orders/list",
     *     name = "listOrders")
     */
    public function listOrdersAction(): Response
    {
        // N'est accessible que pour un utilisateur connecté non admin
        $this->isAllowedUser();
        // Récupération des repos
        $em = $this->getDoctrine()->getManager();
        $orderRepository = $em->getRepository(Order::class);
        $userRepository = $em->getRepository(User::class);
        $productRepository = $em->getRepository(Product::class);
        // Récupérations des commandes et passage au twig
        $user = $userRepository->find($this->getParameter('id-user'));
        $orders = $orderRepository->findBy(array('client' => $user));

        return $this->render("vues/product/listOrders.html.twig", ['orders' => $orders]);
    }

    /**
     * Supprime la commande $orderId du panier de l'utilisateur
     * @param int $orderId
     * @return Response
     *
     * @Route("orders/delete/{orderId}",
     *     name = "deleteOrder",
     *     requirements = {"orderId" = "[1-9]\d*"})
     */
    public function deleteOrderAction(int $orderId): Response
    {
        $this->isAllowedUser();
        // Récupération des repos
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
        //
        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * Vide le panier d'un utilisateur
     *
     * @Route("orders/empty",
     *     name = "emptyOrders")
     */
    public function emptyOrdersAction(EmptyOrders $emptyOrders): Response
    {
        $this->isAllowedUser();

        $emptyOrders->emptyOrders($this->getParameter('id-user'));

        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * @Route("orders/buy", name = "buyOrders")
     */
    public function buyOrdersAction(EmptyOrders $emptyOrders): Response
    {
        $this->isAllowedUser();

        $emptyOrders->emptyOrders($this->getParameter('id-user'),true);

        return $this->redirectToRoute('product_listOrders');
    }

    /**
     * @Route("/add/{id}", name = "add")
     */
    public function addProductAction(): Response
    {
        return $this->redirectToRoute('product_productList');
    }

    /**
     * @Route("/mail", name = "mail")
     * @return Response
     */
    public function mailProductAction(Request $request) : Response {
        if ($request->isMethod('POST')) {
            // Création du transport

            /*
                Le mot de passe n'est même pas caché, ce n'est pas très malin
                de notre part mais la boite mail n'a été créée que pour ça. On
                aurait pu utiliser un fichier local pour stocker les
                identifiants, ou alors les "clés secretes" de GitHub. Dans
                une optique de ne pas perdre trop de temps avec ce genre de
                détails, on a décidé de tout laisser là.
            */
            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                ->setUsername('dummy.projet.web@gmail.com')
                ->setPassword('7sC#soS!jRe3@');

            // Création du mailer à partir du transport
            $mailer = new Swift_Mailer($transport);

            // Récupération de l'utilisateur et du nombre de produits
            $em = $this->getDoctrine()->getManager();
            $userRepository = $em->getRepository(User::class);
            $productRepository = $em->getRepository(Product::class);
            $client = $userRepository->find($this->getParameter('id-user'));
            $nbProducts = count($productRepository->findAll());

            // Création du mail avec les différents paramètres
            $mail = (new Swift_Message('Pépouze la Binouze - Information Stock'))
                ->setFrom(['dummy.projet.web@gmail.com'=>'Pépouze la Binouze'])
                ->setTo([$request->request->get('mail') =>$client->getFirstname() . " " . $client->getName()])
                ->setBody("Bonjour,\nLa quantité de produit disponible dans le shop Pépouze la Binouze est de " . $nbProducts . " article(s).");

            // Envoie du mail
            $mailer->send($mail);
            $this->addFlash('info', 'Mail envoyé avec succès');
        }
        else {
            $this->addFlash('info', 'Erreur la reception du formulaire');
        }


        return $this->redirectToRoute("product_productList");
    }
}

/* ============================================================================
 * ============= Fichier créé par Vincent Commin et Louis Leenart =============
 * ============================================================================
 * */
