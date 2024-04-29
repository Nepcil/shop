<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Promos
 *
 * @ORM\Table(name="promos")
 * @ORM\Entity
 */
class Promos
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
     * @ORM\Column(name="produitid", type="integer", nullable=true) 
     */
    private $produitid;

    /**
     * @ORM\Column(type="promo", type="float", nullable=true) 
     */
    private $promo;

    /**
     * @ORM\Column(type="pourcent", type="integer", nullable=true) 
     */
    private $pourcent;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $dateIn;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $dateOut;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $date;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getPourcent(): ?int
    {
        return $this->pourcent;
    }

    public function setPourcent(?int $pourcent): self
    {
        $this->pourcent = $pourcent;

        return $this;
    }

    public function getPromo(): ?float
    {
        return $this->promo;
    }

    public function setPromo(?float $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getDateIn(): ?DateTime
    {
        return $this->dateIn;
    }

    public function setDateIn(DateTime $dateIn): self
    {
        $this->dateIn = $dateIn;

        return $this;
    }

    public function getDateOut(): ?DateTime
    {
        return $this->dateOut;
    }

    public function setDateOut(DateTime $dateOut): self
    {
        $this->date = $dateOut;

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
