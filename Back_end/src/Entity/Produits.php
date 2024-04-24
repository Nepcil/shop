<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(name="Description", type="text", nullable=true)
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
     * @ORM\Column(name="ImageUrl", type="text", length=255, nullable=true)
     */
    private $imageurl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image1", type="text", length=255, nullable=true)
     */
    private $image1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image2", type="text", length=255, nullable=true)
     */
    private $image2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image3", type="text", length=255, nullable=true)
     */
    private $image3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image4", type="text", length=255, nullable=true)
     */
    private $image4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Image5", type="text", length=255, nullable=true)
     */
    private $image5;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Video", type="text", length=255, nullable=true)
     */
    private $video;

    /**
     * @var int|null
     *
     * @ORM\Column(name="Quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avis", mappedBy="produit")
     */
    private $avis;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $date;


    public function __construct()
    {
        $this->avis = new ArrayCollection();
    }


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

    public function getImage1(): ?string
    {
        return $this->image1;
    }

    public function setImage1(?string $image1): self
    {
        $this->image1 = $image1;

        return $this;
    }

    public function getImage2(): ?string
    {
        return $this->image2;
    }

    public function setImage2(?string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->image3;
    }

    public function setImage3(?string $image3): self
    {
        $this->image3 = $image3;

        return $this;
    }

    public function getImage4(): ?string
    {
        return $this->image4;
    }

    public function setImage4(?string $image4): self
    {
        $this->image4 = $image4;

        return $this;
    }

    public function getImage5(): ?string
    {
        return $this->image5;
    }

    public function setImage5(?string $image5): self
    {
        $this->image5 = $image5;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

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

    //---------------Avis--------------

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvis(Avis $avis): self
    {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setProduitid($this->id);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self
    {
        if ($this->avis->removeElement($avis)) {
            
            if ($avis->getProduitid() === $this) {
                $avis->setProduitid(null);
            }
        }

        return $this;
    }
    

}
