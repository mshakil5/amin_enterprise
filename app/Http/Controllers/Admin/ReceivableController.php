<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillReceive;
use App\Models\ChequeDetail;
use Illuminate\Http\Request;
use App\Models\GeneratingBill;
use App\Models\Program;
use App\Models\ProgramDetail;
use App\Models\ClientRate;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ReceivableController extends Controller
{
    public function checkReceivables2(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);
        // Move the file to a temporary location
        $file = $request->file('document')->getRealPath();
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        dd($rows);
    }

    
    public function checkReceivables3(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('document')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        // 1. Extract Bill Numbers (Skip header row 0)
        $billNumbers = [];
        foreach ($rows as $index => $row) {
            if ($index < 2 || empty($row[1])) continue; 
            $billNumbers[] = trim($row[1]);
        }

        // 2. Fetch all program details with eager loading
        $chkprgms = ProgramDetail::with(['vendor', 'ghat', 'destination'])
            ->whereIn('bill_no', $billNumbers)
            ->orderBy('bill_no', 'ASC')
            ->orderBy('headerid', 'ASC')
            ->get();

        if ($chkprgms->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'No matching bill records found in system.']);
        }

        // 3. Variables for Totals
        $totalAmount = 0; $totalprevAmount = 0;
        $totalPrevQty = 0; $totalQty = 0;
        $totalscale_fee = 0;
        $html = '';

        // 4. Process Logic
        foreach ($chkprgms as $key => $prgmDtl) {
            // Find Rate
            $rate = ClientRate::where('destination_id', $prgmDtl->destination_id)
                ->where('ghat_id', $prgmDtl->ghat_id)
                ->first();

            $qty = (float) $prgmDtl->dest_qty;
            $old_qty = (float) $prgmDtl->old_qty;
            $rowAmount = 0;
            $rowOldAmount = 0;

            if ($rate) {
                // Current Bill Calculation
                if ($qty > $rate->maxqty) {
                    $rowAmount = ($rate->maxqty * $rate->below_rate_per_qty) + (($qty - $rate->maxqty) * $rate->above_rate_per_qty);
                } else {
                    $rowAmount = $qty * $rate->below_rate_per_qty;
                }

                // Old Bill Calculation
                if ($old_qty > $rate->maxqty) {
                    $rowOldAmount = ($rate->maxqty * $rate->below_rate_per_qty) + (($old_qty - $rate->maxqty) * $rate->above_rate_per_qty);
                } else {
                    $rowOldAmount = $old_qty * $rate->below_rate_per_qty;
                }
            }

            // Build HTML Row
            $html .= '<tr class="text-center">
                        <td>' . ($key + 1) . '</td>
                        <td><b>' . $prgmDtl->bill_no . '</b></td>
                        <td>' . Carbon::parse($prgmDtl->date)->format('d/m/Y') . '</td>
                        <td>' . ($prgmDtl->vendor->name ?? 'N/A') . '</td>
                        <td>' . $prgmDtl->challan_no . '</td>
                        <td>
                            <small>' . ($prgmDtl->ghat->name ?? '') . '</small> 
                            <i class="fas fa-arrow-right mx-1 text-muted"></i> 
                            <small>' . ($prgmDtl->destination->name ?? '') . '</small>
                        </td>
                        <td>' . number_format($prgmDtl->scale_fee, 2) . '</td>
                        <td>' . number_format($old_qty, 2) . '</td>
                        <td class="text-primary"><b>' . number_format($rowOldAmount, 2) . '</b></td>
                        <td>' . number_format($qty, 2) . '</td>
                        <td class="text-success"><b>' . number_format($rowAmount, 2) . '</b></td>
                    </tr>';

            // Add to Totals
            $totalprevAmount += $rowOldAmount;
            $totalAmount += $rowAmount;
            $totalQty += $qty;
            $totalPrevQty += $old_qty;
            $totalscale_fee += $prgmDtl->scale_fee;
        }

        return response()->json([
            'status' => 200,
            'html' => $html,
            'totalscalefee' => number_format($totalscale_fee, 2, '.', ''),
            'totalprevAmount' => number_format($totalprevAmount, 2, '.', ''),
            'totalAmount' => number_format($totalAmount, 2, '.', ''),
            'totalQty' => number_format($totalQty, 2, '.', ''),
            'totalPrevQty' => number_format($totalPrevQty, 2, '.', ''),
        ]);
    }


    public function checkReceivables(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('document')->getRealPath();
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $billNumbers = [];
        foreach ($rows as $index => $row) {
            if ($index <= 2 || empty($row[4])) continue; 
            $billNumbers[] = trim($row[4]);
        }

        // Fetch details and group them by bill_no
        $chkprgmsGrouped = ProgramDetail::with(['vendor', 'ghat', 'destination'])
            ->whereIn('bill_no', $billNumbers)
            ->get()
            ->groupBy('bill_no'); 

        if ($chkprgmsGrouped->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'No matching bill records found.']);
        }

        $grandTotalAmount = 0; 
        $grandQty = 0; 
        $grandTotalPrevAmount = 0;
        $grandPrevQty = 0;
        $html = '';
        $sl = 1;

        foreach ($chkprgmsGrouped as $billNo => $details) {
            $billPrevAmount = 0;
            $billCurrentAmount = 0;
            $billPrevQty = 0;
            $billCurrentQty = 0;
            $billScaleFee = 0;

            foreach ($details as $prgmDtl) {
                // Find Rate for each item in the bill
                $rate = ClientRate::where('destination_id', $prgmDtl->destination_id)
                    ->where('ghat_id', $prgmDtl->ghat_id)
                    ->first();

                $qty = (float) $prgmDtl->dest_qty;
                $old_qty = (float) $prgmDtl->old_qty;
                $rowAmount = 0;
                $rowOldAmount = 0;

                if ($rate) {
                    // Calculation Logic
                    $rowAmount = ($qty > $rate->maxqty) 
                        ? ($rate->maxqty * $rate->below_rate_per_qty) + (($qty - $rate->maxqty) * $rate->above_rate_per_qty)
                        : $qty * $rate->below_rate_per_qty;

                    $rowOldAmount = ($old_qty > $rate->maxqty)
                        ? ($rate->maxqty * $rate->below_rate_per_qty) + (($old_qty - $rate->maxqty) * $rate->above_rate_per_qty)
                        : $old_qty * $rate->below_rate_per_qty;
                }

                // Accumulate bill-wise totals
                $billPrevAmount += $rowOldAmount;
                $billCurrentAmount += $rowAmount;
                $billPrevQty += $old_qty;
                $billCurrentQty += $qty;
                $billScaleFee += (float)$prgmDtl->scale_fee;
            }

            // Build one row per Bill Number
            $html .= '<tr class="text-center">
                        <td>' . $sl++ . '</td>
                        <td><b>' . $billNo . '</b></td>
                        <td>' . number_format($billScaleFee, 2) . '</td>
                        <td>' . number_format($billPrevQty, 2) . '</td>
                        <td class="text-primary">' . number_format($billPrevAmount, 2) . '</td>
                        <td>' . number_format($billCurrentQty, 2) . '</td>
                        <td class="text-success">' . number_format($billCurrentAmount, 2) . '</td>
                        <td class="text-primary"> <input type="number" class="form-control" name="updatedQty" value="" id="updatedQty"> </td>
                        <td class="text-primary"> <input type="number" class="form-control" name="updatedAmount" value="" id="updatedAmount"> </td>
                    </tr>';

            // Add to Grand Totals (if you still need them for the footer)
            $grandTotalPrevAmount += $billPrevAmount;
            $grandTotalAmount += $billCurrentAmount;
            $grandQty += $billCurrentQty;
            $grandPrevQty += $billPrevQty;
        }

        $button = '<button class="btn btn-sm btn-primary" id="addToListBtn" data-bill-numbers="' . implode(',', $chkprgmsGrouped->keys()->toArray()) . '" data-currentQty="'.$grandQty.'"  data-prevQty="'.$grandPrevQty.'"  data-grandTotalPrev="'.$grandTotalPrevAmount.'"  data-grandTotalCurrent="'.$grandTotalAmount.'" >Add to List</button>';

        return response()->json([
            'status' => 200,
            'html' => $html,
            'button' => $button,
            'grandTotalPrev' => number_format($grandTotalPrevAmount, 2),
            'grandTotalCurrent' => number_format($grandTotalAmount, 2),
            'grandQty' => number_format($grandQty, 2),
            'grandPrevQty' => number_format($grandPrevQty, 2),
        ]);


    }

    public function getReceivables()
    {
        
        $billReceive = BillReceive::with('transaction')->orderby('id','DESC')->get();

        
        return view('admin.bill.receivable', compact('billReceive'));
    }


    public function getReceivablesDetails2($id)
    {
        $billReceive = BillReceive::with(['transaction', 'coa'])->where('id', $id)->first();

        $billNumbers = array_map('trim', explode(',', $billReceive->bill_list));

        $programDetails = ProgramDetail::with(['motherVassel', 'destination', 'ghat'])
                            ->whereIn('bill_no', $billNumbers)
                            ->orderBy('bill_no')
                            ->get()
                            ->groupBy('bill_no');

        // Pre-calculate carrying_bill per bill group using ClientRate logic
        $billCalculations = [];

        foreach ($programDetails as $billNo => $rows) {
            $billCarryingBill = 0;
            $billDestQty      = 0;
            $billScaleFee     = 0;

            foreach ($rows as $detail) {
                $qty = (float) $detail->dest_qty;

                $rate = ClientRate::where('destination_id', $detail->destination_id)
                            ->where('ghat_id', $detail->ghat_id)
                            ->first();

                $rowAmount = 0;
                if ($rate) {
                    $rowAmount = ($qty > $rate->maxqty)
                        ? ($rate->maxqty * $rate->below_rate_per_qty) + (($qty - $rate->maxqty) * $rate->above_rate_per_qty)
                        : $qty * $rate->below_rate_per_qty;
                }

                $billCarryingBill += $rowAmount;
                $billDestQty      += $qty;
                $billScaleFee     += (float) $detail->scale_fee;
            }

            $billCalculations[$billNo] = [
                'carrying_bill' => $billCarryingBill,
                'dest_qty'      => $billDestQty,
                'scale_fee'     => $billScaleFee,
                'trip'          => $rows->count(),
            ];
        }

        return view('admin.bill.receivabledetails', compact('billReceive', 'programDetails', 'billCalculations'));
    }

    public function getReceivablesDetails($id)
    {
        $billReceive = BillReceive::with(['transaction', 'coa'])->where('id', $id)->first();

        $billNumbers = array_map('trim', explode(',', $billReceive->bill_list));

        $programDetails = ProgramDetail::with(['motherVassel', 'destination', 'ghat'])
                            ->whereIn('bill_no', $billNumbers)
                            ->orderBy('bill_no')
                            ->get()
                            ->groupBy('bill_no');

        // Pre-calculate carrying_bill per bill group
        $billCalculations = [];

        foreach ($programDetails as $billNo => $rows) {
            $billCarryingBill = 0;
            $billDestQty      = 0;
            $billScaleFee     = 0;

            foreach ($rows as $detail) {
                $qty = (float) $detail->dest_qty;

                $rate = ClientRate::where('destination_id', $detail->destination_id)
                            ->where('ghat_id', $detail->ghat_id)
                            ->first();

                $rowAmount = 0;
                if ($rate) {
                    $rowAmount = ($qty > $rate->maxqty)
                        ? ($rate->maxqty * $rate->below_rate_per_qty) + (($qty - $rate->maxqty) * $rate->above_rate_per_qty)
                        : $qty * $rate->below_rate_per_qty;
                }

                $billCarryingBill += $rowAmount;
                $billDestQty      += $qty;
                $billScaleFee     += (float) $detail->scale_fee;
            }

            $billCalculations[$billNo] = [
                'carrying_bill' => $billCarryingBill,
                'dest_qty'      => $billDestQty,
                'scale_fee'     => $billScaleFee,
                'trip'          => $rows->count(),
            ];
        }

        // ========== FETCH EXISTING CHEQUE DETAILS ==========
        $chequeDetails = ChequeDetail::where('bill_receive_id', $id)
                            ->orderBy('id')
                            ->get();

        // Build map: billNo => cheque object (for pre-checking rows)
        $billChequeMap = [];
        foreach ($chequeDetails as $cheque) {
            $chequeBillNos = json_decode($cheque->bill_nos, true) ?? [];
            foreach ($chequeBillNos as $cbn) {
                $billChequeMap[$cbn] = $cheque;
            }
        }

        return view('admin.bill.receivabledetails', compact(
            'billReceive', 'programDetails', 'billCalculations', 'chequeDetails', 'billChequeMap'
        ));
    }


    public function destroy(BillReceive $billReceive)
    {
        try {
            \DB::transaction(function () use ($billReceive) {
                
                if ($billReceive->transaction) {
                    if (auth()->check()) {
                        $billReceive->transaction->deleted_by = auth()->id();
                        $billReceive->transaction->save();
                    }
                    $billReceive->transaction->delete();
                }
                $billReceive->delete();

            });

            return redirect()
                ->back()
                ->with('success', 'Bill receive and transaction deleted successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function updateReceiveStatus(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:bill_receives,id',
            'receive_status' => 'required|in:0,1',
        ]);

        try {
            $bill = BillReceive::find($request->id);
            $bill->receive_status = $request->receive_status;
            $bill->updated_by = auth()->id();
            $bill->save();

            return response()->json([
                'status'  => $bill->receive_status,
                'message' => $bill->receive_status == 1 
                            ? 'Marked as Received' 
                            : 'Marked as Not Received',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update status.',
            ], 500);
        }
    }




}
