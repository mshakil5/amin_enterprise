<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramDetail;
use App\Models\VendorSequenceNumber;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ChallanRate;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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


    public function vendorRateIndex()
    {
        return view('admin.excel.vendor-slabrate-upload');
    }


    public function carryingBillUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        // 1. Load and Process Excel Data into a Rate Map
        $file = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        // The Rate Map will use a combined key: "ghat_id-destination_id"
        $rateMap = [];
        $headerSkipped = false;

        foreach ($rows as $row) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue; // Skip the header row
            }

            // Check if the row has enough columns and if IDs are present
            if (count($row) >= 6 && $row[1] && $row[3]) {
                $ghatId = trim($row[1]);
                $destId = trim($row[3]);
                $key = "{$ghatId}-{$destId}";

                $rateMap[$key] = [
                    'max_qty' => (float) $row[4],      // Qty column (index 4)
                    'below_rate' => (float) $row[5],   // Below rate column (index 5)
                    'above_rate' => (float) $row[6],   // Above rate column (index 6)
                ];
            }
        }

        if (empty($rateMap)) {
            return back()->with('error', 'Could not process valid rate data from the Excel file.');
        }
        
        // 2. Retrieve Program Details (Your existing logic)
        $date = "2025-12-03";
        $programDetails = ProgramDetail::where('created_at', '<', $date)
            ->where('updated_at', '>', $date)
            ->where('date', '<', $date)
            ->get(); // Use get() not paginate() for processing

        // 3. Iterate, Calculate & Update
        $updatedCount = 0;
        
        DB::beginTransaction(); // Start transaction for data integrity
        
        try {
            foreach ($programDetails as $programDetail) {
                $ghatId = $programDetail->ghat_id;
                $destId = $programDetail->destination_id;
                $cQty = (float) $programDetail->dest_qty;
                $key = "{$ghatId}-{$destId}";

                // Check if a rate exists for this combination
                if (!isset($rateMap[$key])) {
                    // You might want to log this or skip, depending on your business rules
                    continue; 
                }

                $rateData = $rateMap[$key];
                
                // Delete existing ChallanRate entries to avoid duplicates
                // Assuming ChallanRate has a foreign key to program_detail_id
                ChallanRate::where('program_detail_id', $programDetail->id)->delete();
                
                $totalAmount = 0;
                $maxQty = $rateData['max_qty'];
                $belowRate = $rateData['below_rate'];
                $aboveRate = $rateData['above_rate'];

                // Calculation Logic (Similar to your original function)
                if ($cQty > $maxQty && $maxQty > 0) {
                    // Case 1: Quantity exceeds the maximum slab limit
                    $aboveQty = $cQty - $maxQty;
                    
                    // Below Rate Portion
                    $belowTotal = $belowRate * $maxQty;
                    
                    // Above Rate Portion
                    $aboveTotal = $aboveRate * $aboveQty;
                    
                    $totalAmount = $belowTotal + $aboveTotal;

                    // Insert ChallanRate for Below Rate (max_qty)
                    ChallanRate::create([
                        'program_detail_id' => $programDetail->id,
                        'challan_no'        => $programDetail->challan_no,
                        'qty'               => $maxQty,
                        'rate_per_unit'     => $belowRate,
                        'total'             => $belowTotal,
                        'created_by'        => Auth::id(),
                    ]);

                    // Insert ChallanRate for Above Rate (remaining qty)
                    ChallanRate::create([
                        'program_detail_id' => $programDetail->id,
                        'challan_no'        => $programDetail->challan_no,
                        'qty'               => $aboveQty,
                        'rate_per_unit'     => $aboveRate,
                        'total'             => $aboveTotal,
                        'created_by'        => Auth::id(),
                    ]);

                } else {
                    // Case 2: Quantity is below or equal to the maximum slab limit (or maxQty is 0)
                    $totalAmount = $belowRate * $cQty;
                    
                    // Insert ChallanRate for the full quantity at below rate
                    ChallanRate::create([
                        'program_detail_id' => $programDetail->id,
                        'challan_no'        => $programDetail->challan_no,
                        'qty'               => $cQty,
                        'rate_per_unit'     => $belowRate,
                        'total'             => $totalAmount,
                        'created_by'        => Auth::id(),
                    ]);
                }
                
                // Update the carrying_bill and old_carrying_bill (assuming you want to keep track of the old one)
                $programDetail->old_carrying_bill = $programDetail->carrying_bill;
                $programDetail->carrying_bill = $totalAmount;
                $programDetail->save();
                
                $updatedCount++;
            }
            
            DB::commit(); // Commit transaction if all updates were successful
            
            return back()->with('success', "Successfully updated carrying bills for {$updatedCount} records.");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurred
            // Log the error for debugging
            \Log::error("Carrying Bill Update Failed: " . $e->getMessage());
            return back()->with('error', 'An error occurred during the update process. Changes were rolled back.');
        }
    }


    public function programDetailsQtyUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        DB::beginTransaction();

        $program = Program::find(137);
            $program->qty_change = 0;
            $program->save();

        try {
            foreach ($rows as $index => $row) {

                // Skip header row
                if ($index === 0) {
                    continue;
                }

                $id         = (int) $row[0];
                $program_id = (int) $row[1];
                $dest_qty   = (float) $row[3];
                $old_qty    = (float) $row[2];

                // Extra safety check
                if ($program_id !== 137) {
                    continue;
                }

                DB::table('program_details')
                    ->where('id', $id)
                    ->where('program_id', 137)
                    ->update([
                        'dest_qty' => $dest_qty,
                        'old_qty'  => $old_qty,
                    ]);
            }

            DB::commit();

            return back()->with('success', 'Program details quantities updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

}