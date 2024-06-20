<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promos")
 * @ORM\Entity
 */
class Promos
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="ID", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var int
     * 
     * @ORM\Column(name="produitid", type="integer", nullable=false) 
     */
    private $produitid;

    /**
     * @var float
     * 
     * @ORM\Column(name="promo", type="float", nullable=false) 
     */
    private $promo;

    /**
     * @var int
     * 
     * @ORM\Column(name="pourcent", type="integer", nullable=false) 
     */
    private $pourcent;

    /**
     * @var string
     * 
     * @ORM\Column(name="promotitle", type="string", nullable=false) 
     */
    private $promotitle;

    /**
     * @var DateTime
     * 
     * @ORM\Column(name="datein", type="datetime", nullable=false) 
     */
    private $datein;

    /**
     * @var DateTime
     * 
     * @ORM\Column(name="dateout", type="datetime", nullable=false) 
     */
    private $dateout;

    /**
     * @var DateTime
     * 
     * @ORM\Column(name="date", type="datetime", nullable=false) 
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

    public function getPromotitle(): ?string
    {
        return $this->promotitle;
    }

    public function setPromotitle(?string $promotitle): self
    {
        $this->promotitle = $promotitle;

        return $this;
    }

    public function getDatein(): ?DateTime
    {
        return $this->datein;
    }

    public function setDatein(DateTime $datein): self
    {
        $this->datein = $datein;

        return $this;
    }

    public function getDateout(): ?DateTime
    {
        return $this->dateout;
    }

    public function setDateout(DateTime $dateout): self
    {
        $this->dateout = $dateout;

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
