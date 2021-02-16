<?php

namespace App\Entity;

use App\Repository\ArchiveRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArchiveRepository::class)
 */
class Archive
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Sortie::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $Sortie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortie(): ?Sortie
    {
        return $this->Sortie;
    }

    public function setSortie(Sortie $Sortie): self
    {
        $this->Sortie = $Sortie;

        return $this;
    }
}
