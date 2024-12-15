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
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        
        $data = ProgramDetail::where('generate_bill', 1)->where('bill_status', 0)->get();
        return view('admin.bill.index', compact('clients','mvassels','data'));
    }

    public function checkBill(Request $request)
    {
        $data = $request->all();
        
        $chkprgms = ProgramDetail::where('client_id', $request->client_id)->where('bill_no', $request->bill_number)->where('mother_vassel_id', $request->mv_id)->get();


        
        if ($chkprgms) {
            $totalAmount = 0;
            $totalQty = 0;
            foreach ($chkprgms as $key => $prgmDtl) {
                $rate = ClientRate::where('client_id', $request->client_id)->where('destination_id', $prgmDtl->destination_id)->where('ghat_id', $prgmDtl->ghat_id)->first();
                      $qty = $prgmDtl->dest_qty;

                      if ($rate) {
                        if ( $qty > $rate->maxqty) {
                            $belowAmount = $rate->maxqty * $rate->below_rate_per_qty;
                            $aboveQty = $qty - $rate->maxqty;
                            $aboveAmount = $aboveQty * $rate->above_rate_per_qty;
                            $totalAmount += $belowAmount + $aboveAmount;
                        } else {
                            $totalAmount += $qty * $rate->below_rate_per_qty;
                        }
                        $totalQty = $totalQty + $qty;
                      }
            }
        }
        

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Bill Found.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message,'data'=>$chkprgms,'totalAmount'=>$totalAmount,'totalQty'=>$totalQty]);
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
