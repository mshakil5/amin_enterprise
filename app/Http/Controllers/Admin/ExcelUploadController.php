<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelUploadController extends Controller
{
    // ==================== ORIGINAL BILL FUNCTIONS ====================
    public function index()
    {
        return view('admin.excel.bill-upload');
    }

    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['date', 'mother_vessel_name', 'bill_number'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample row
        $sampleRow = [date('Y-m-d'), 'Example Vessel', '123456'];
        $sheet->fromArray($sampleRow, null, 'A2');

        // Formatting
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'bill_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        $row = $rows[1] ?? [];

        dd([
            'type' => 'bill',
            'date' => $row[0] ?? null,
            'mother_vessel_name' => $row[1] ?? null,
            'bill_number' => $row[2] ?? null,
        ]);
    }

    // ==================== FUEL BILL FUNCTIONS ====================
    public function fuelIndex()
    {
        return view('admin.excel.fuel-bill-upload');
    }

    public function fuelExportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for fuel bill
        $headers = ['date', 'mother_vessel_name', 'fuel_bill_number'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample row
        $sampleRow = [date('Y-m-d'), 'Example Vessel', 'FUEL-123'];
        $sheet->fromArray($sampleRow, null, 'A2');

        // Formatting
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'fuel_bill_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function fuelStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        $row = $rows[1] ?? [];

        dd([
            'type' => 'fuel_bill',
            'date' => $row[0] ?? null,
            'mother_vessel_name' => $row[1] ?? null,
            'fuel_bill_number' => $row[2] ?? null,
        ]);
    }

    // ==================== CLIENT BILL FUNCTIONS ====================
    public function clientIndex()
    {
        return view('admin.excel.client-bill-upload');
    }

    public function clientExportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for client bill
        $headers = ['date', 'mother_vessel_name', 'client_bill_number'];
        $sheet->fromArray($headers, null, 'A1');

        // Add sample row
        $sampleRow = [date('Y-m-d'), 'Example Vessel', 'CLIENT-789'];
        $sheet->fromArray($sampleRow, null, 'A2');

        // Formatting
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'client_bill_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        ob_end_clean();
        $writer->save('php://output');
        exit;
    }

    public function clientStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        $row = $rows[1] ?? [];

        dd([
            'type' => 'client_bill',
            'date' => $row[0] ?? null,
            'mother_vessel_name' => $row[1] ?? null,
            'client_bill_number' => $row[2] ?? null,
        ]);
    }
}