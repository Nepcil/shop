<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Produits
 *
 * @ORM\Table(name="produits")
 * @ORM\Entity(repositoryClass="App\Repository\ProduitsRepository")
 */
class Produits
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
     * @var string|null
     *
     * @ORM\Column(name="NomDuProduit", type="string", length=100, nullable=true)
     */
    private $nomduproduit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Prix", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $prix;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CategorieID", type="integer", nullable=true)
     */
    private $categorieid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ImageUrl", type="text", length=65535, nullable=true)
     */
    private $imageurl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomduproduit(): ?string
    {
        return $this->nomduproduit;
    }

    public function setNomduproduit(?string $nomduproduit): self
    {
        $this->nomduproduit = $nomduproduit;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(?string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategorieid(): ?int
    {
        return $this->categorieid;
    }

    public function setCategorieid(?int $categorieid): self
    {
        $this->categorieid = $categorieid;

        return $this;
    }

    public function getImageurl(): ?string
    {
        return $this->imageurl;
    }

    public function setImageurl(?string $imageurl): self
    {
        $this->imageurl = $imageurl;

        return $this;
    }


}
