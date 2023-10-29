<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat")
 * @ORM\Entity(repositoryClass="App\Repository\ChatRepository")
 */
class Chat
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
     * @var string|null
     *
     * @ORM\Column(name="Message", type="text", length=65535, nullable=true)
     */
    private $message;
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DateMessage", type="datetime", nullable=true)
     */
    private $datemessage;

    /**
     * @ORM\Column(type="datetime") 
     */
    private $date;

    

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
    public function getMessage(): ?string
    {
        return $this->message;
    }
    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }
    public function getDatemessage(): ?\DateTimeInterface
    {
        return $this->datemessage;
    }
    public function setDatemessage(?\DateTimeInterface $datemessage): self
    {
        $this->datemessage = $datemessage;
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
