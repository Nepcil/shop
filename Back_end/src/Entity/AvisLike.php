<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AvisLike
 *
 * @ORM\Table(name="AvisLike")
 * @ORM\Entity(repositoryClass="App\Repository\AvisLikeRepository")
 */
class AvisLike
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
     * @ORM\Column(name="UserID", type="integer", nullable=true)
     */
    private $userid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ProduitID", type="integer", nullable=true)
     */
    private $produitid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Handup", type="integer", nullable=true)
     */
    private $handup = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Handless", type="integer", nullable=true)
     */
    private $handless = 0;

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

    public function getHandup(): ?int
    {
        return $this->handup;
    }

    public function setHandup(?int $handup): self
    {
        $this->handup = $handup;

        return $this;
    }

    public function getHandless(): ?int
    {
        return $this->handless;
    }

    public function setHandless(?int $handless): self
    {
        $this->handless = $handless;

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
