<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneratingBill;
use App\Models\Program;
use App\Models\ProgramDetail;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GeneratingBillController extends Controller
{
    public function billGenerating($id)
    {
        $programId = $id;

        $data = Program::with('programDetail','programDetail.programDestination','programDetail.advancePayment','programDetail.advancePayment.petrolPump','programDetail.programDestination.destinationSlabRate')->where('id', $id)->first();

        return view('admin.bill.generator', compact('programId','data'));
    }

    public function billGenerated($id)
    {
        $programId = $id;
        $data = Program::with([
            'programDetail' => function ($q) {
                $q->where('generate_bill', 1);
            },
            'programDetail.programDestination',
            'programDetail.advancePayment',
            'programDetail.advancePayment.petrolPump',
            'programDetail.programDestination.destinationSlabRate',
        ])->where('id', $id)->first();

        return view('admin.bill.generator', compact('programId', 'data'));
    }

    public function billNotGenerated($id)
    {
        $programId = $id;
        $data = Program::with([
            'programDetail' => function ($q) {
                $q->whereNot('generate_bill', 1);
            },
            'programDetail.programDestination',
            'programDetail.advancePayment',
            'programDetail.advancePayment.petrolPump',
            'programDetail.programDestination.destinationSlabRate',
        ])->where('id', $id)->first();

        return view('admin.bill.generator', compact('programId', 'data'));
    }

    public function billGeneratingShow($id)
    {
        $programId = Program::where('id', $id)->first();
        $data = GeneratingBill::where('program_id', $id)->get();

        return view('admin.bill.billShow', compact('programId','data'));
    }

    public function billGeneratingStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $programId = $request->programId;
        if (isset($programId)) {
            $program = Program::find($programId);
            $program->bill_status = 1;
            $program->save();
        }
        // Move the file to a temporary location
        $file = $request->file('file')->getRealPath();

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Iterate through the rows and save to the database
        foreach ($rows as $index => $row) {
            // Skip the first row if it is the header
            if ($index === 0) {
                continue;
            }

            if (empty($row[1])) {
                continue;
            }else {

                $chkPrgmDetail = ProgramDetail::where('headerid', $row[1])->where('dest_qty',$row[8])->orWhere('old_qty',$row[8])->first();
                if (isset($chkPrgmDetail)) {
                    $chkPrgmDetail->bill_no = $row[26];
                    $chkPrgmDetail->generate_bill = 1;
                    $chkPrgmDetail->save();
                    $billingSts = 1;
                } else {
                    $billingSts = 0;
                }

                GeneratingBill::create([
                    'program_id' => $programId,
                    'header_id' => $row[1],
                    'date' => $row[2],
                    'truck_number' => $row[3],
                    'destination' => $row[4],
                    'from_location' => $row[5],
                    'to_location' => $row[6],
                    'shipping_method' => $row[7],
                    'challan_qty' => $row[8],
                    'trip_number' => $row[9],
                    'trip_qty' => $row[10],
                    'before_freight_amount' => $row[11],
                    'after_freight_amount' => $row[12],
                    'additional_claim' => $row[13],
                    'final_trip_amount' => $row[14],
                    'remark_by_transporter' => $row[15],
                    'rental_mode' => $row[16],
                    'mode_of_trip' => $row[17],
                    'rate_type' => $row[18],
                    'sales_region' => $row[19],
                    'wings' => $row[20],
                    'lc_no' => $row[21],
                    'vessel_name' => $row[22],
                    'batch_no' => $row[23],
                    'billing_ou' => $row[24],
                    'billing_legal_entity' => $row[25],
                    'bill_no' => $row[26],
                    'transaction_status' => $row[27],
                    'billing_status' => $billingSts,
                    // Map other columns as necessary
                ]);
            }

            
        }

        return back()->with('success', 'Data imported successfully.');
    }


    public function exportTemplate()
    {
        // Define the header row for the CSV
        $headers = [
            'header_id',
            'date',
            'truck_number',
            'destination',
            'from_location',
            'to_location',
            'shipping_method',
            'challan_qty',
            'trip_number',
            'trip_qty',
            'before_freight_amount',
            'after_freight_amount',
            'additional_claim',
            'final_trip_amount',
            'remark_by_transporter',
            'rental_mode',
            'mode_of_trip',
            'rate_type',
            'sales_region',
            'wings',
            'lc_no',
            'vessel_name',
            'batch_no',
            'billing_ou',
            'billing_legal_entity',
            'bill_no',
            'transaction_status', // Example columns
        ];

        // Create the CSV content
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            
            // Add the header row to the CSV
            fputcsv($file, $headers);

            // Optionally, add example rows
            fputcsv($file, ['header_id',
                            'date',
                            'truck_number',
                            'destination',
                            'from_location',
                            'to_location',
                            'shipping_method',
                            'challan_qty',
                            'trip_number',
                            'trip_qty',
                            'before_freight_amount',
                            'after_freight_amount',
                            'additional_claim',
                            'final_trip_amount',
                            'remark_by_transporter',
                            'rental_mode',
                            'mode_of_trip',
                            'rate_type',
                            'sales_region',
                            'wings',
                            'lc_no',
                            'vessel_name',
                            'batch_no',
                            'billing_ou',
                            'billing_legal_entity',
                            'bill_no',
                            'transaction_status',]);
            
                            fclose($file);
                        };

        // Return response to download as CSV
        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=import_template.csv",
        ]);
    }
    
    public function generateBill(Request $request)
    {
        $request->validate([
            'bill_no' => 'required|string',
            'selected_ids' => 'required|string',
        ]);

        $ids = explode(',', str_replace(['[', ']', '"'], '', $request->selected_ids));

        foreach ($ids as $id) {
            $programDetail = ProgramDetail::find($id);
            if ($programDetail) {
                $programDetail->bill_no = $request->bill_no;
                $programDetail->generate_bill = 1;
                $programDetail->save();
            }
        }

        return back()->with('success', 'Bill generated successfully.');
    }

    public function undoGenerateBill($id)
    {
        $programDetail = ProgramDetail::findOrFail($id);
        $programDetail->generate_bill = 0;
        $programDetail->bill_no = null;
        $programDetail->save();

        return back()->with('success', 'Unchecked successfully.');
    }

    // export program details
    public function exportProgramDetails($id)
    {
        $programDetails = ProgramDetail::with([
            'programDestination',
            'advancePayment',
            'advancePayment.petrolPump',
            'programDestination.destinationSlabRate'
        ])->where('program_id', $id)->get();


        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'ID', 'Header ID', 'Destination', 'Advance Payment', 'Quantity', 'Old Quantity'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Fill data
        $row = 2;
        foreach ($programDetails as $detail) {
            $sheet->setCellValue('A' . $row, $detail->id);
            $sheet->setCellValue('B' . $row, $detail->headerid);
            $sheet->setCellValue('C' . $row, optional($detail->programDestination)->name);
            $sheet->setCellValue('D' . $row, optional($detail->advancePayment)->amount);
            $sheet->setCellValue('E' . $row, $detail->dest_qty);
            $sheet->setCellValue('F' . $row, $detail->old_qty);
            $row++;
        }

        // Save the file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Program_Details_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        ob_end_clean(); // Clear output buffer
        $writer->save('php://output');
        
        exit;
    }

    // update old quantity
    public function updateOldQty(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $programId = $request->programId;

        
        // Move the file to a temporary location
        $file = $request->file('file')->getRealPath();

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();


        // Iterate through the rows and save to the database
        foreach ($rows as $index => $row) {
            // Skip the first row if it is the header
            if ($index === 0) {
                continue;
            }

            if (empty($row[4])) {
                continue;
            }else {

                $chkPrgmDetail = ProgramDetail::where('id', $row[0])->first();
                if (isset($chkPrgmDetail)) {
                    $chkPrgmDetail->old_qty = $row[4];
                    $chkPrgmDetail->save();
                }

            }

            
        }

        return back()->with('success', 'Data imported successfully.');
    }
        

}
