<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Reglement
 *
 * @ORM\Table(name="reglement")
 * @ORM\Entity(repositoryClass="App\Repository\ReglementRepository")
 */
class Reglement
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
     * @ORM\Column(name="CommandeID", type="integer", nullable=true)
     */
    private $commandeid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Montant", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montant;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DateReglement", type="datetime", nullable=true)
     */
    private $datereglement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeid(): ?int
    {
        return $this->commandeid;
    }

    public function setCommandeid(?int $commandeid): self
    {
        $this->commandeid = $commandeid;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatereglement(): ?\DateTimeInterface
    {
        return $this->datereglement;
    }

    public function setDatereglement(?\DateTimeInterface $datereglement): self
    {
        $this->datereglement = $datereglement;

        return $this;
    }


}
