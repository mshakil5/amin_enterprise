<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\PettyCash;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class PettyCashController extends Controller
{
    public function index()
    {
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

        $data = new Transaction();
        $data->table_type = "Asset";
        $data->tran_type = "Petty Cash In";
        $data->date = $request->date;
        $data->amount = $request->amount;
        $data->payment_type = "Cash";
        $data->description = "Cash transfer to Petty Cash";
        $data->created_by = Auth::user()->id;
        if ($data->save()) {

            $data->tran_id = 'PT' . date('ymd') . str_pad($data->id, 4, '0', STR_PAD_LEFT);
            $data->save();


            $pcash = PettyCash::where('id','1')->first();
            $pcash->amount = $pcash->balance + $request->amount;
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
        $info = Country::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \" amount \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }




        $data = Country::find($request->codeid);
        $data->name = $request->name;
        $data->updated_by = Auth::user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }


    
}
