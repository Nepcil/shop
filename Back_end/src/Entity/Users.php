<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users implements UserInterface, PasswordAuthenticatedUserInterface
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
     * @ORM\Column(name="Nom", type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Prenom", type="string", length=100, nullable=true)
     */
    private $prenom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MotDePasse", type="string", length=255, nullable=true)
     */
    private $motdepasse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="LanguePreferee", type="string", length=50, nullable=true)
     */
    private $languepreferee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Adresse", type="string", length=200, nullable=true)
     */
    private $adresse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Tel", type="string", length=20, nullable=true)
     */
    private $tel;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DateDeNaissance", type="datetime", nullable=true)
     */
    private $datedenaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false)
     */
    private $email;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private $roles = ['ROLE_USER'];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(?string $motdepasse): self
    {
        $this->motdepasse = $motdepasse;
        return $this;
    }

    public function getLanguepreferee(): ?string
    {
        return $this->languepreferee;
    }

    public function setLanguepreferee(?string $languepreferee): self
    {
        $this->languepreferee = $languepreferee;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getDatedenaissance(): ?\DateTimeInterface
    {
        return $this->datedenaissance;
    }

    public function setDatedenaissance(?\DateTimeInterface $datedenaissance): self
    {
        $this->datedenaissance = $datedenaissance;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->motdepasse;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Ici, vous pouvez effacer les données sensibles de l'utilisateur
        // Par exemple, vous pouvez mettre $this->motdepasse à null
        $this->motdepasse = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getIsAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        if ($isAdmin) {
            if (!in_array('ROLE_ADMIN', $this->roles)) {
                $this->roles[] = 'ROLE_ADMIN';
            }
        } else {
            $this->roles = array_diff($this->roles, ['ROLE_ADMIN']);
        }
    }
}
