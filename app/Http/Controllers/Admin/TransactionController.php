<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\BillReceive;
use App\Models\Client;
use App\Models\ClientRate;
use App\Models\MotherVassel;
use App\Models\Program;
use App\Models\ProgramDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function vendorAdvancePay(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
            'paymentAmount' => 'required',
            'payment_type' => 'required',
            'paymentNote' => 'nullable',
        ]);

        $program = Program::where('id', $request->programId)->first();

        
            $transaction = new Transaction();
            $transaction->program_id = $request->programId;
            $transaction->mother_vassel_id = $program->mother_vassel_id;
            $transaction->lighter_vassel_id = $program->lighter_vassel_id;
            $transaction->client_id = $program->client_id;
            $transaction->vendor_id = $request->vendorId;
            $transaction->amount = $request->paymentAmount;
            $transaction->payment_type = $request->payment_type;
            $transaction->note = $request->paymentNote;
            $transaction->tran_type = "Payment";
            $transaction->date = date('Y-m-d');
            $transaction->save();
            $transaction->tran_id = 'AE' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment processed successfully!',
        ]);
    }

    public function vendorTran(Request $request)
    {
        $data = Transaction::where('program_id',$request->programId)->where('vendor_id',$request->vendorId)->get();

        $prop = '';
        
            foreach ($data as $tran){
                // <!-- Single Property Start -->
                $prop.= '<tr>
                            <td>
                                '.$tran->date.'
                            </td>
                            <td>
                                '.$tran->tran_id.'
                            </td>
                            <td>
                                '.$tran->payment_type.'
                            </td>
                            <td>
                                '.$tran->amount.'
                            </td>
                        </tr>';
            }

        return response()->json(['status'=> 300,'data'=>$prop]);
    }

    public function getBill()
    {
        if (!(in_array('13', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        
        $data = ProgramDetail::where('generate_bill', 1)->where('bill_status', 0)->limit(10)->get();
        return view('admin.bill.index', compact('clients','mvassels','data'));
    }

    public function checkBill(Request $request)
    {
        // Eager load relationships
        $chkprgms = ProgramDetail::with(['vendor', 'ghat', 'destination'])
            ->where('bill_no', $request->bill_number)
            ->get();

        if ($chkprgms->isEmpty()) {
            return response()->json(['status' => 404, 'message' => 'No bill records found.']);
        }

        $totalAmount = 0;
        $totalprevAmount = 0;
        $totalPrevQty = 0;
        $totalQty = 0;
        $totalscale_fee = 0;
        $html = ''; // Initialize the HTML string

        foreach ($chkprgms as $key => $prgmDtl) {
            // Calculate Rate
            $rate = ClientRate::where('destination_id', $prgmDtl->destination_id)
                ->where('ghat_id', $prgmDtl->ghat_id)
                ->first();

                // 2. LOG RECORD: Capture search attempts and results
                Log::info("Bill Check Log - Bill No: {$request->bill_number}", [
                    'row_index'      => $key + 1,
                    'challan_no'     => $prgmDtl->challan_no,
                    'search_params'  => [
                        'client_id'      => $request->client_id,
                        'destination_id' => $prgmDtl->destination_id,
                        'ghat_id'        => $prgmDtl->ghat_id,
                    ],
                    'rate_found'     => $rate ? 'Yes' : 'No',
                    'rate_details'   => $rate ? [
                        'max_qty'      => $rate->maxqty,
                        'below_rate'   => $rate->below_rate_per_qty,
                        'above_rate'   => $rate->above_rate_per_qty
                    ] : 'NULL'
                ]);

            $qty = (float) $prgmDtl->dest_qty;
            $old_qty = (float) $prgmDtl->old_qty;
            $rowAmount = 0;
            $rowOldAmount = 0;

            if ($rate) {
                if ($qty > $rate->maxqty) {
                    $belowAmount = $rate->maxqty * $rate->below_rate_per_qty;
                    $aboveQty = $qty - $rate->maxqty;
                    $aboveAmount = $aboveQty * $rate->above_rate_per_qty;
                    $rowAmount = $belowAmount + $aboveAmount;
                } else {
                    $rowAmount = $qty * $rate->below_rate_per_qty;
                }

                if ($old_qty > $rate->maxqty) {
                    $OldbelowAmount = $rate->maxqty * $rate->below_rate_per_qty;
                    $OldaboveQty = $old_qty - $rate->maxqty;
                    $OldaboveAmount = $OldaboveQty * $rate->above_rate_per_qty;
                    $rowOldAmount = $OldbelowAmount + $OldaboveAmount;
                } else {
                    $rowOldAmount = $rate->maxqty * $rate->below_rate_per_qty;
                }
            }

            $formattedDate = \Carbon\Carbon::parse($prgmDtl->date)->format('d/m/Y');
            $vendorName = $prgmDtl->vendor->name ?? 'N/A';
            $ghatName = $prgmDtl->ghat->name ?? '';
            $destName = $prgmDtl->destination->name ?? '';

            // Build the HTML row string
            $html .= '<tr class="text-center">
                        <td>' . ($key + 1) . '</td>
                        <td><span class="badge badge-success">Generated</span></td>
                        <td>' . $formattedDate . '</td>
                        <td>' . $vendorName . '</td>
                        <td>' . $prgmDtl->challan_no . '</td>
                        <td>' . $prgmDtl->headerid . '</td>
                        <td>
                            <small>' . $ghatName . '</small> 
                            <i class="fas fa-arrow-right mx-1 text-muted"></i> 
                            <small>' . $destName . '</small>
                        </td>
                        <td><b>' . number_format($prgmDtl->scale_fee, 2) . '</b></td>
                        <td><b>' . number_format($old_qty, 2) . '</b></td>
                        <td class="text-primary"><b>' . number_format($rowOldAmount, 2) . '</b></td>
                        <td><b>' . number_format($qty, 2) . '</b></td>
                        <td class="text-primary"><b>' . number_format($rowAmount, 2) . '</b></td>
                    </tr>';

            $totalprevAmount += $rowOldAmount;
            $totalAmount += $rowAmount;
            $totalQty += $qty;
            $totalPrevQty += $old_qty;
            $totalscale_fee += $prgmDtl->scale_fee;
        }

        return response()->json([
            'status' => 200,
            'html' => $html, // Pass the pre-rendered HTML back
            'totalscalefee' => number_format($totalscale_fee, 2, '.', ''),
            'totalprevAmount' => number_format($totalprevAmount, 2, '.', ''),
            'totalAmount' => number_format($totalAmount, 2, '.', ''),
            'totalQty' => number_format($totalQty, 2, '.', ''),
            'totalPrevQty' => number_format($totalPrevQty, 2, '.', ''),
        ]);
    }

    public function billStore(Request $request)
    {
        $data = $request->all();
        
        $chkprgms = ProgramDetail::where('client_id', $request->client_id)->where('bill_no', $request->bill_number)->where('mother_vassel_id', $request->mv_id)->get();


        $bill = new BillReceive();
        $bill->date = $request->date;
        $bill->client_id = $request->client_id;
        $bill->mother_vassel_id = $request->mv_id;
        $bill->bill_number = $request->bill_number;
        $bill->rcv_type = $request->rcvType;
        $bill->qty = $request->totalqty;
        $bill->total_amount = $request->totalAmount;
        $bill->maintainance = $request->maintainance;
        $bill->scale_charge = $request->scaleCharge;
        $bill->other_exp = $request->otherexp;
        $bill->other_rcv = $request->otherRcv;
        $bill->net_amount = $request->netAmount;
        $bill->save();
       
        $tran = new Transaction();
        $tran->date =  $request->date;
        $tran->bill_number =  $request->bill_number;
        $tran->client_id =  $request->client_id;
        $tran->bill_receive_id =  $bill->id;
        $tran->mother_vassel_id = $request->mv_id;
        $tran->payment_type =  $request->rcvType;
        $tran->tran_type =  "Received";
        $tran->amount =  $request->netAmount;
        $tran->save();
        $tran->tran_id = 'RT' . date('ymd') . str_pad($tran->id, 4, '0', STR_PAD_LEFT);
        $tran->save();

        $pdtls = ProgramDetail::where('bill_no', $request->bill_number)->update(['bill_status' => 1]);

        

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Bill Stored successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message,'data'=>$data]);
    }
}
