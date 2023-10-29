<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Commandes
 *
 * @ORM\Table(name="commandes")
 * @ORM\Entity(repositoryClass="App\Repository\CommandesRepository")
 */
class Commandes
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="DateCommande", type="datetime", nullable=true)
     */
    private $datecommande;
    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer", nullable=false)
     */
    private $iduser;
    /**
     * @var int
     *
     * @ORM\Column(name="idProduit", type="integer", nullable=false)
     */
    private $idproduit;
    /**
     * @var int
     *
     * @ORM\Column(name="Quantite", type="integer", nullable=false)
     */
    private $quantite;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $date;

    

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDatecommande(): ?\DateTimeInterface
    {
        return $this->datecommande;
    }
    public function setDatecommande(?\DateTimeInterface $datecommande): self
    {
        $this->datecommande = $datecommande;
        return $this;
    }
    public function getIduser(): ?int
    {
        return $this->iduser;
    }
    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;
        return $this;
    }
    public function getIdproduit(): ?int
    {
        return $this->idproduit;
    }
    public function setIdproduit(int $idproduit): self
    {
        $this->idproduit = $idproduit;
        return $this;
    }
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }
    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
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
