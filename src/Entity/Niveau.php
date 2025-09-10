<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NiveauRepository::class)
 */
class Niveau
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
    private $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @ORM\OneToMany(targetEntity=Classe::class, mappedBy="niveau")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity=TarifScolaire::class, mappedBy="niveau")
     */
    private $tarifScolaires;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
        $this->tarifScolaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setNiveau($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getNiveau() === $this) {
                $class->setNiveau(null);
            }
        }

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
            $tarifScolaire->setNiveau($this);
        }

        return $this;
    }

    public function removeTarifScolaire(TarifScolaire $tarifScolaire): self
    {
        if ($this->tarifScolaires->removeElement($tarifScolaire)) {
            // set the owning side to null (unless already changed)
            if ($tarifScolaire->getNiveau() === $this) {
                $tarifScolaire->setNiveau(null);
            }
        }

        return $this;
    }
}
