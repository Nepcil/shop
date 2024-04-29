<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * BestProducts
 *
 * @ORM\Table(name="bestproducts")
 * @ORM\Entity
 */
class BestProducts
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
