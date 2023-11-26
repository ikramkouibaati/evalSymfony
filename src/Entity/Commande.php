<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use http\Message;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(
        message: "La date de début de réservation ne peut pas être vide",allowNull: false
    )]
    #[Assert\LessThanOrEqual(
        propertyPath: "date_heur_fin",
        message: "La date de départ doit etre inferieur a la date de fin"
    )]
    #[Assert\GreaterThan(
        value: 'now',
        message: "La date de debut doit etre superieur a la date et heure actuelle"
    )]
    private ?\DateTimeInterface $date_heur_depart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(
        message: "La date de fin de réservation ne peut pas être vide",allowNull: false
    )]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "date_heur_depart",
        message: "La date de fin doit etre superieur a la date de debut"
    )]
    #[Assert\GreaterThan(
        value: 'now',
        message: "La date de fin doit etre superieur a la date et heure actuelle"
    )]
    private ?\DateTimeInterface $date_heur_fin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_enregistrement = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    #[Assert\NotBlank(
        message: "Vous devez choisir un membre",allowNull: false
    )]
    private ?Membre $membre = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    #[Assert\NotBlank(
        message: "Vous devez choisir un vehicule",allowNull: false
    )]
    private ?Vehicule $vehicule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeurDepart(): ?\DateTimeInterface
    {
        return $this->date_heur_depart;
    }

    public function setDateHeurDepart(?\DateTimeInterface $date_heur_depart): static
    {
        $this->date_heur_depart = $date_heur_depart;

        return $this;
    }

    public function getDateHeurFin(): ?\DateTimeInterface
    {
        return $this->date_heur_fin;
    }

    public function setDateHeurFin(?\DateTimeInterface $date_heur_fin): static
    {
        $this->date_heur_fin = $date_heur_fin;

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

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): static
    {
        $this->membre = $membre;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }
}
