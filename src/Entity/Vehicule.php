<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(
        message: "Le titre ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 200,
        minMessage:"Le titre doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le titre doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(
        message: "La marque ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 50,
        minMessage:"La marque doit contenir au minimum {{ limit }} caractères",
        maxMessage:"La marque doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $marque = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(
        message: "Le modele ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 50,
        minMessage:"Le modele doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le modele doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $modele = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: "La description ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        minMessage:"La description doit contenir au minimum {{ limit }} caractères",
    )]
    private ?string $description = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(
        message: "La photo ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 200,
        minMessage:"Le chemin vers la photo doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le chemin vers la photo doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $photo = null;

    #[ORM\Column]
    #[Assert\NotBlank(
        message: "Le prix journalier ne peut pas etre vide",allowNull: false
    )]
    private ?int $prix_journalier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_enregistrement = null;

    #[ORM\OneToMany(mappedBy: 'vehicule', targetEntity: Commande::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->titre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrixJournalier(): ?int
    {
        return $this->prix_journalier;
    }

    public function setPrixJournalier(?int $prix_journalier): static
    {
        $this->prix_journalier = $prix_journalier;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->date_enregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $date_enregistrement): static
    {
        $this->date_enregistrement = $date_enregistrement;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setVehicule($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getVehicule() === $this) {
                $commande->setVehicule(null);
            }
        }

        return $this;
    }
}
