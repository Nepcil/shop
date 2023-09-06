<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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
     * @ORM\Column(name="UsersID", type="integer", nullable=true)
     */
    private $usersid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ProduitID", type="integer", nullable=true)
     */
    private $produitid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Note", type="integer", nullable=true)
     */
    private $note;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=true)
     */
    private $commentaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsersid(): ?int
    {
        return $this->usersid;
    }

    public function setUsersid(?int $usersid): self
    {
        $this->usersid = $usersid;

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

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

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


}
