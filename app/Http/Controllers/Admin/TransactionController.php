<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
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

        $data = new AdvancePayment();
        $data->program_detail_id = $request->id;
        $data->program_id = $request->programId;
        $data->vendor_id = $request->vendorId;
        $data->amount = $request->paymentAmount;
        $data->payment_type = $request->payment_type;
        $data->receiver_name = $request->receiver_name;
        $data->petrol_pump_id = $request->petrol_pump_id;
        $data->fuel_rate = $request->fuel_rate;
        $data->fuelqty = $request->fuelqty;
        $data->date = date('Y-m-d');
        if ($data->save()) {
            $transaction = new Transaction();
            $transaction->advance_payment_id = $data->id;
            $transaction->program_detail_id = $request->id;
            $transaction->program_id = $request->programId;
            $transaction->vendor_id = $request->vendorId;
            $transaction->amount = $request->paymentAmount;
            $transaction->payment_type = $request->payment_type;
            $transaction->tran_type = "Advance";
            $transaction->date = date('Y-m-d');
            $transaction->save();
            $transaction->tran_id = 'AD' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();
        }

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
