<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\DestinationSlabRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    public function index()
    {
        $data = Destination::orderby('id','DESC')->get();
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.destination.index', compact('data','clients'));
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->client_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Client \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $chkname = Destination::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $data = new Destination;
        $data->name = $request->name;
        $data->address = $request->address;
        $data->client_id = $request->client_id;
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
        $info = Destination::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        if(empty($request->client_id)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please select \"Client \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = Destination::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $data = Destination::find($request->codeid);
        $data->name = $request->name;
        $data->address = $request->address;
        $data->client_id = $request->client_id;
        $data->updated_by = Auth::user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function delete($id)
    {

        if(Destination::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }





    // slab rate crud

    public function slabRateIndex()
    {
        $data = Destination::orderby('id','DESC')->get();
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.destination.slabrate', compact('data','clients'));
    }

    public function slabRatestore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required',
            'ghat_id' => 'required',
            'qty' => 'required',
            'below_rate_per_qty' => 'required',
            'above_rate_per_qty' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $data = new DestinationSlabRate();
        $data->date = date('Y-m-d');
        $data->destination_id = $request->destination_id;
        $data->ghat_id = $request->ghat_id;
        $data->maxqty = $request->qty;
        $data->below_rate_per_qty = $request->below_rate_per_qty;
        $data->above_rate_per_qty = $request->above_rate_per_qty;
        $data->title = $request->title;
        $data->created_by = Auth::user()->id;
        $data->save();
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function slabRateedit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = DestinationSlabRate::where($where)->get()->first();
        return response()->json($info);
    }

    public function slabRateupdate(Request $request)
    {

        
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required',
            'ghat_id' => 'required',
            'qty' => 'required',
            'below_rate_per_qty' => 'required',
            'above_rate_per_qty' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }


        $data = DestinationSlabRate::find($request->codeid);
        $data->destination_id = $request->destination_id;
        $data->ghat_id = $request->ghat_id;
        $data->maxqty = $request->qty;
        $data->below_rate_per_qty = $request->below_rate_per_qty;
        $data->above_rate_per_qty = $request->above_rate_per_qty;
        $data->title = $request->title;
        $data->updated_by = Auth::user()->id;
        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
        else{
            return response()->json(['status'=> 303,'message'=>'Server Error!!']);
        } 
    }

    public function slabRatedelete($id)
    {

        if(DestinationSlabRate::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }
}
