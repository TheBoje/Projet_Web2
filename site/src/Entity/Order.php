<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`im2021_order`",
 *     uniqueConstraints = {
 *      @ORM\UniqueConstraint(name = "prod_user_idx", columns = {"client", "product"})
 *     })
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name = "id_client", nullable = false)
     */
    private $client;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(name = "id_product", nullable = false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClient(): User
    {
        return $this->client;
    }

    public function setIdClient(int $idClient): self
    {
        $this->client = $idClient;

        return $this;
    }

    public function getIdProduct(): Product
    {
        return $this->product;
    }

    public function setIdProduct(int $idProduct): self
    {
        $this->product = $idProduct;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
