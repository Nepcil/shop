<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Avis
 *
 * @ORM\Table(name="avis")
 * @ORM\Entity(repositoryClass="App\Repository\AvisRepository")
 */
class Avis
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
    private $handup;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Handless", type="integer", nullable=true)
     */
    private $handless;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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
