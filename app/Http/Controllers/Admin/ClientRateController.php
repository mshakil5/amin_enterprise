<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\ClientRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientRateController extends Controller
{
    
    public function index()
    {
        if (!(in_array('12', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $data = ClientRate::orderby('id','DESC')->get();
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.clientrate.index', compact('data','clients'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
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

        $data = new ClientRate();
        $data->date = date('Y-m-d');
        $data->client_id = $request->client_id;
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

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = ClientRate::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
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


        $data = ClientRate::find($request->codeid);
        $data->client_id = $request->client_id;
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

    public function delete($id)
    {

        if(ClientRate::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }
}
