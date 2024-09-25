<?php

namespace App\Service;

use App\Repository\PureAnnonceRepository;

class AnnonceService
{
    private $pureAnnonceRepository;

    public function __construct(PureAnnonceRepository $pureAnnonceRepository)
    {
        $this->pureAnnonceRepository = $pureAnnonceRepository;
    }

    public function getAnnonceCounts(): array
    {
        $annonces = $this->pureAnnonceRepository->findBy(['approuvee' => null]);
        $nbAnnonces = count($annonces);
        $nbAnnoncesPubliees = $this->pureAnnonceRepository->count(['approuvee' => true]);

        return [
            'nbAnnonces' => $nbAnnonces,
            'nbAnnoncesPubliees' => $nbAnnoncesPubliees,
        ];
    }
}