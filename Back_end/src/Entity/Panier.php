<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Panier
 *
 * @ORM\Table(name="panier")
 * @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
 */
class Panier
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Userid", type="integer", nullable=true)
     */
    private $userid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Produitid", type="integer", nullable=true)
     */
    private $produitid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var float|null
     *
     * @ORM\Column(name="Price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(?int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getProduitid(): ?int
    {
        return $this->produitid;
    }

    public function setProduitid(?int $produitid): self
    {
        $this->produitid = $produitid;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

}
