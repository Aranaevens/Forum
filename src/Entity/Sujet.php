<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SujetRepository")
 */
class Sujet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $titre;

    /**
     * @ORM\Column(type="date")
     */
    private $datePosted;

    /**
     * @ORM\Column(type="boolean")
     */
    private $verrouiller;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbvues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sujet", orphanRemoval=true)
     * @ORM\OrderBy({"datePosted" = "DESC"})
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="sujets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $auteur;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->datePosted = new \DateTime;
    }

    // public function getMCount() :?int
    // {
    //     return $this->message_count;
    // }

    // public function incrementMCount(): self
    // {
    //     $this->message_count++;

    //     return $this;
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDatePosted(): ?\DateTimeInterface
    {
        return $this->datePosted;
    }

    public function setDatePosted(\DateTimeInterface $datePosted): self
    {
        $this->datePosted = $datePosted;

        return $this;
    }

    public function getVerrouiller(): ?bool
    {
        return $this->verrouiller;
    }

    public function setVerrouiller(bool $verrouiller): self
    {
        $this->verrouiller = $verrouiller;

        return $this;
    }

    public function getNbvues(): ?int
    {
        return $this->nbvues;
    }

    public function setNbvues(int $nbvues): self
    {
        $this->nbvues = $nbvues;

        return $this;
    }

    public function incrNbvues(): self
    {
        $this->nbvues++;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setSujet($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getSujet() === $this) {
                $message->setSujet(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
