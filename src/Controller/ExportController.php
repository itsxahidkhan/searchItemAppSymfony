<?php
namespace App\Controller;

use App\Entity\SearchItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ExportController extends AbstractController
{
    #[Route('/export', name: 'export', methods: ['GET'])]
    public function export(EntityManagerInterface $em): Response
    {
        $items = $em->getRepository(SearchItem::class)->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Category');
        $sheet->setCellValue('D1', 'Created At');

        $row = 2;
        foreach ($items as $item) {
            $sheet->setCellValue('A' . $row, $item->getId());
            $sheet->setCellValue('B' . $row, $item->getName());
            $sheet->setCellValue('C' . $row, $item->getCategory());
            $sheet->setCellValue('D' . $row, $item->getCreatedAt()->format('Y-m-d H:i:s'));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'search_items.xlsx';

        // Write the file to the output stream
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        $response->headers->set('Cache-Control', 'max-age=0');
        $writer->save('php://output');

        return $response;
    }
}
