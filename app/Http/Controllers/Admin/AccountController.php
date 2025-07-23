<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class AccountController extends Controller
{
    public function index()
    {
        $data = Account::latest()->get();
        $allAccounts = Account::latest()->get();
        return view('admin.account.index', compact('data', 'allAccounts'));
    }

    public function store(Request $request)
    {
        if(empty($request->type)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $exists = Account::where('type', $request->type)->exists();

        if($exists){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Type already exists!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

        $data = new Account();
        $data->type = $request->type;
        $data->amount = $request->amount;
        $data->created_by = Auth::user()->id;
        if ($data->save()) {

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Create Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        }
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Account::where($where)->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        if(empty($request->type)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" type \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $exists = Account::where('type', $request->type)
        ->where('id', '!=', $request->codeid)
        ->exists();

          if($exists){
            $message = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Type already exists!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

        $data = Account::find($request->codeid);
        $data->type = $request->type;
        $data->amount = $request->amount;
        $data->updated_by = Auth::user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $fromAccount = Account::findOrFail($request->from_account_id);
            $toAccount = Account::findOrFail($request->to_account_id);
            
            if ($fromAccount->amount < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance for transfer'
                ]);
            }

            $fromAccount->amount -= $request->amount;
            $fromAccount->updated_by = Auth::id();
            $fromAccount->save();

            $toAccount->amount += $request->amount;
            $toAccount->updated_by = Auth::id();
            $toAccount->save();

            $debitTransaction = new Transaction();
            $debitTransaction->date = now()->format('Y-m-d');
            $debitTransaction->description = "Transfer to {$toAccount->type}";
            $debitTransaction->amount = $request->amount;
            $debitTransaction->at_amount = $request->amount;
            $debitTransaction->tran_type = 'TransferIn';
            $debitTransaction->payment_type = 'Cash';
            $debitTransaction->account_id = $fromAccount->id;
            $debitTransaction->created_by = Auth::id();
            $debitTransaction->save();
            $debitTransaction->tran_id = 'TR' . date('ymd') . 'D' . str_pad($debitTransaction->id, 4, '0', STR_PAD_LEFT);
            $debitTransaction->save();

            $creditTransaction = new Transaction();
            $creditTransaction->date = now()->format('Y-m-d');
            $creditTransaction->description = "Transfer from {$fromAccount->type}";
            $creditTransaction->amount = $request->amount;
            $creditTransaction->at_amount = $request->amount;
            $creditTransaction->tran_type = 'TransferOut';
            $creditTransaction->payment_type = 'Cash';
            $creditTransaction->account_id = $toAccount->id;
            $creditTransaction->created_by = Auth::id();
            $creditTransaction->save();
            $creditTransaction->tran_id = 'TR' . date('ymd') . 'C' . str_pad($creditTransaction->id, 4, '0', STR_PAD_LEFT);
            $creditTransaction->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ]);
        }
    }
    
}
