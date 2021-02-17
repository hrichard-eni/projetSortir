<?php

namespace App\Controller;

use App\Repository\ArchiveRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchiveController extends AbstractController
{
    /**
     * @Route("/admin/archive", name="archive_dl")
     */
    public function recupererArchives(EntityManagerInterface $entityManager, ArchiveRepository $archiveRepository): Response
    {
        $archives = $archiveRepository->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Date Limite d\'Inscription');
        $sheet->setCellValue('D1', 'Début');
        $sheet->setCellValue('E1', 'Fin');
        $sheet->setCellValue('F1', 'Infos');
        $sheet->setCellValue('G1', 'Organisateur');
        $sheet->setCellValue('H1', 'Campus Organisateur');
        $sheet->setCellValue('I1', 'Participants');
        $sheet->setCellValue('J1', 'Max de Participants');
        $sheet->setCellValue('K1', 'Lieu');

        $sheet->getStyle('A1:K1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('d7d7d7');

        $i = 2;
        foreach ($archives as $archive) {

            //Récupération uniquement des pseudos des participants à une sortie
            $participants = $archive->getSortie()->getParticipants();
            $pseudos = [];
            foreach ($participants as $participant) {
                $pseudos[] = $participant->getPseudo();
            }

            $sheet->setCellValue('A'.$i, $archive->getSortie()->getId()); //Id
            $sheet->setCellValue('B'.$i, $archive->getSortie()->getNom()); //Nom
            $sheet->setCellValue('C'.$i, $archive->getSortie()->getDateLimiteInscription()); //Limite Inscription
            $sheet->setCellValue('D'.$i, $archive->getSortie()->getDateHeureDebut()); //Début
            $sheet->setCellValue('E'.$i, $archive->getSortie()->getDuree()); //Fin
            $sheet->setCellValue('F'.$i, $archive->getSortie()->getInfosSortie()); //Infos
            $sheet->setCellValue('G'.$i, $archive->getSortie()->getOrganisateur()->getPseudo()); //Organisateur
            $sheet->setCellValue('H'.$i, $archive->getSortie()->getCampusOrganisateur()->getNom()); //Campus Organisateur
            $sheet->setCellValue('I'.$i, implode(' | ', $pseudos)); //Participants
            $sheet->setCellValue('J'.$i, $archive->getSortie()->getNbInscriptionsMax()); //Max de Participants
            $sheet->setCellValue('K'.$i, $archive->getSortie()->getLieu()->getRue() //Lieu rue
                . ' ' . $archive->getSortie()->getLieu()->getVille()->getNom() //Lieu ville
                . ' (' . $archive->getSortie()->getLieu()->getVille()->getCodePostal() . ')'); //Lieu cp
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('ArchivesSorties.xlsx');

        $this->addFlash('success', 'Archive générée avec succès : <a href="/projetSortir/public/ArchivesSorties.xlsx">ArchivesSorties.xlsx</a>');
        return $this->redirectToRoute('main_home', [
        ]);
    }
}
