<?php


namespace App\Entity;


class Filtre
{
    // Pour le filtre par campus qui est sur la home et permet d'afficher les sorties... par campus, donc
    public $campus;

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




}