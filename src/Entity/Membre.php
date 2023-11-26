<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a un déjà un compte crée avec cet email')]
class Membre implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(
        message: "Le pseudo ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 20,
        minMessage:"Le pseudo doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le pseudo doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 60)]
    #[Assert\NotBlank(
        message: "Le mot de passe ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 5,
        max : 60,
        minMessage:"Le mot de passe doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le mot de passe doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $mdp = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(
        message: "Le nom ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 20,
        minMessage:"Le nom doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le nom doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(
        message: "Le prenom ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 20,
        minMessage:"Le prenom doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le prenom doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(
        message: "L'email ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Length(
        min : 3,
        max : 50,
        minMessage:"Le mail doit contenir au minimum {{ limit }} caractères",
        maxMessage:"Le mail doit contenir au maximum {{ limit }} caractères",
    )]
    private ?string $email = null;

    #[ORM\Column(length: 1)]
    #[Assert\NotBlank(
        message: "La civilité ne peut pas etre vide",allowNull: false
    )]
    #[Assert\Choice(
        choices : ['m', 'f'],
        message : "choisir une civilité valide ( m ou f )"
    )]
    private ?string $civilite = null;

    #[ORM\Column]
    #[Assert\NotBlank(
        message: "Le statut ne peut pas etre vide",allowNull: false
    )]
    private ?int $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_enregistrement = null;

    #[ORM\OneToMany(mappedBy: 'membre', targetEntity: Commande::class)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private Collection $commandes;

    protected $roleValueAndLibel = [
        'ROLE_ADMIN' => 0,
        'ROLE_USER' => 1
    ];

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addGetterConstraint('statut', new Assert\Choice([
            'choices' => (new Membre)->getChoiceStatut(),
            'message' => "choisir un statut valide"
        ]));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(?string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): static
    {
        $this->civilite = $civilite;

        return $this;
    }

    public function getLibelStatusForForm()
    {
        return $this->roleValueAndLibel;
    }

    public function getLibelStatusForTableau()
    {
        foreach ($this->roleValueAndLibel as $key => $value){
            if($value == $this->statut){
                return $key;
            }
        }
        return '';
    }

    public function getChoiceStatut(){
        $res = [];
        foreach ($this->roleValueAndLibel as $key => $value){
            array_push($res, $value);
        }
        return $res;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(?int $statut): static
    {
        $this->statut = $statut;

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
            $commande->setMembre($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getMembre() === $this) {
                $commande->setMembre(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function getRoles(): array
    {
        return array($this->getLibelStatusForTableau());
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
