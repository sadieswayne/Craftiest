<?php

namespace App\Services;

use App\Domains\Entity\EntityStats;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UsersExportService
{
    public function exportAsPdf($users)
    {

        $html = view('panel.admin.users.components.users-table', compact('users'))->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->download('users.pdf');
    }

    public function exportAsExcel($users): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NAME');
        $sheet->setCellValue('B1', 'TYPE');
        $sheet->setCellValue('C1', 'REMAINING WORDS');
        $sheet->setCellValue('D1', 'REMAINING IMAGES');
        $sheet->setCellValue('E1', 'COUNTRY');
        $sheet->setCellValue('F1', 'STATUS');
        $sheet->setCellValue('G1', 'CREATED AT');

        $row = 2;
        foreach ($users as $user) {
            $status = $user->status === 1 ? 'Active' : 'Inactive';

            $words = EntityStats::word()->forUser($user)->totalCredits();
            $images = EntityStats::image()->forUser($user)->totalCredits();

            $sheet->setCellValue('A' . $row, $user?->fullName());
            $sheet->setCellValue('B' . $row, $user->type->value);
            $sheet->setCellValue('C' . $row, $words);
            $sheet->setCellValue('D' . $row, $images);
            $sheet->setCellValue('E' . $row, $user->country);
            $sheet->setCellValue('F' . $row, $status);
            $sheet->setCellValue('G' . $row, $user->created_at);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'users.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function exportAsCsv($users)
    {
        $csv = $this->generateCsvContent($users);
        $fileName = 'users.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return Response::make($csv, 200, $headers);
    }

    private function generateCsvContent($users): string
    {
        $csv = "Name,Type,Remaining Words,Remaining Images,Country,Status,Created At\n";

        foreach ($users as $user) {
            $status = $user->status === 1 ? 'Active' : 'Inactive';

            $words = EntityStats::word()->forUser($user)->totalCredits();
            $images = EntityStats::image()->forUser($user)->totalCredits();
            $csv .= "{$user?->fullName()},{$user->type->value},{$words},{$images},{$user->country},{$status},{$user->created_at}\n";
        }

        return $csv;
    }
}
