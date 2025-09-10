<?php

namespace App\Entity;

use App\Repository\TarifScolaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TarifScolaireRepository::class)
 */
class TarifScolaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Niveau::class, inversedBy="tarifScolaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity=AnneeScolaire::class, inversedBy="tarifScolaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $anneeScolaire;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_ecolage;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_inscription;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $autres_frais;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $anneeScolaire): self
    {
        $this->anneeScolaire = $anneeScolaire;

        return $this;
    }

    public function getMontantEcolage(): ?float
    {
        return $this->montant_ecolage;
    }

    public function setMontantEcolage(float $montant_ecolage): self
    {
        $this->montant_ecolage = $montant_ecolage;

        return $this;
    }

    public function getMontantInscription(): ?float
    {
        return $this->montant_inscription;
    }

    public function setMontantInscription(float $montant_inscription): self
    {
        $this->montant_inscription = $montant_inscription;

        return $this;
    }

    public function getAutresFrais(): ?float
    {
        return $this->autres_frais;
    }

    public function setAutresFrais(?float $autres_frais): self
    {
        $this->autres_frais = $autres_frais;

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
}
