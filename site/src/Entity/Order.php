<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\JoinColumn(name = "client", nullable = false)
     */
    private $client;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity=Product::class)
     * @ORM\JoinColumn(name = "product", nullable = false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\Positive
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): User
    {
        return $this->client;
    }

    public function setClient(User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

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

/* ============================================================================
 * ============= Fichier créé par Vincent Commin et Louis Leenart =============
 * ============================================================================
 * */
