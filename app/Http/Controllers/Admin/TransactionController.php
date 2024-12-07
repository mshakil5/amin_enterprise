<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\Program;
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

    public function vendorAdvanceTran(Request $request)
    {
        $data = AdvancePayment::where('program_detail_id',$request->pdid)->get();
        return response()->json(['status'=> 300,'data'=>$data]);
    }
}
