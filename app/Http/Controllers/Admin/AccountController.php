<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $data = Account::latest()->get();
        return view('admin.account.index', compact('data'));
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
    
}
