<?php


namespace App\Entity;



class Filtre
{
    // Pour le filtre par campus qui est sur la home et permet d'afficher les sorties... par campus, donc
    public $campus;
    public $dateDebut;
    public $dateFin;
    public $isOrganisateur;
    public $isInscrit;

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return mixed
     */
    public function getIsOrganisateur()
    {
        return $this->isOrganisateur;
    }

    /**
     * @param mixed $isOrganisateur
     */
    public function setIsOrganisateur($isOrganisateur): void
    {
        $this->isOrganisateur = $isOrganisateur;
    }

    /**
     * @return mixed
     */
    public function getIsInscrit()
    {
        return $this->isInscrit;
    }

    /**
     * @param mixed $isInscrit
     */
    public function setIsInscrit($isInscrit): void
    {
        $this->isInscrit = $isInscrit;
    }

}