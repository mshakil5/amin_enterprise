<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\PettyCash;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class PettyCashController extends Controller
{
    public function index()
    {
        if (!(in_array('3', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $balance = PettyCash::where('id','1')->first();
        $transactions = Transaction::where('table_type', 'Asset')->where('tran_type', 'Petty Cash In')->orderby('id', 'DESC')->get();
        return view('admin.pettycash.index', compact('balance','transactions'));
    }

    public function store(Request $request)
    {
        if(empty($request->amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" amount \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $account = Account::find(1);
        if (!$account || $account->amount < $request->amount) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Insufficient Balance in Office..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }

        $account->amount -= $request->amount;
        $account->save();

        $data = new Transaction();
        $data->table_type = "Asset";
        $data->tran_type = "Petty Cash In";
        $data->date = $request->date;
        $data->amount = $request->amount;
        $data->note = $request->description;
        $data->payment_type = "Cash";
        $data->description = "Cash transfer to Petty Cash";
        $data->created_by = Auth::user()->id;
        if ($data->save()) {

            $data->tran_id = 'PT' . date('ymd') . str_pad($data->id, 4, '0', STR_PAD_LEFT);
            $data->save();


            $pcash = PettyCash::where('id','1')->first();
            $pcash->amount = $pcash->amount + $request->amount;
            $pcash->save();


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
        $info = Transaction::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        
        if(empty($request->amount)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" amount \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }




        $data = Transaction::find($request->codeid);

        $pcash = PettyCash::where('id','1')->first();
        $pcash->amount = $pcash->amount - $data->amount;
        $pcash->save();

        $data->table_type = "Asset";
        $data->tran_type = "Petty Cash In";
        $data->date = $request->date;
        $data->amount = $request->amount;
        $data->note = $request->description;
        $data->payment_type = "Cash";
        $data->description = "Cash transfer to Petty Cash";
        $data->updated_by = Auth::user()->id;
        if ($data->save()) {
            $pcash = PettyCash::where('id','1')->first();
            $pcash->amount = $pcash->amount + $request->amount;
            $pcash->save();

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }


    
}
