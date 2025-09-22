<?php

namespace App\Entity;

use App\Repository\AnneeScolaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnneeScolaireRepository::class)
 */
class AnneeScolaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="date")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=TarifScolaire::class, mappedBy="anneeScolaire")
     */
    private $tarifScolaires;

    public function __construct()
    {
        $this->tarifScolaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, TarifScolaire>
     */
    public function getTarifScolaires(): Collection
    {
        return $this->tarifScolaires;
    }

    public function addTarifScolaire(TarifScolaire $tarifScolaire): self
    {
        if (!$this->tarifScolaires->contains($tarifScolaire)) {
            $this->tarifScolaires[] = $tarifScolaire;
            $tarifScolaire->setAnneeScolaire($this);
        }

        return $this;
    }

    public function removeTarifScolaire(TarifScolaire $tarifScolaire): self
    {
        if ($this->tarifScolaires->removeElement($tarifScolaire)) {
            // set the owning side to null (unless already changed)
            if ($tarifScolaire->getAnneeScolaire() === $this) {
                $tarifScolaire->setAnneeScolaire(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->libelle ?? 'Année Scolaire ' . $this->id;
    }
}
