<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\DestinationSlabRate;
use App\Models\Ghat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    public function index()
    {
        if (!(in_array('10', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
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
        if (!(in_array('11', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        
        $data = DestinationSlabRate::orderby('client_id')->orderby('destination_id')->orderby('tier_min_qty')->get();
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $ghats = Ghat::where('status', 1)->get();
        $destinations = Destination::where('status', 1)->get();
        
        return view('admin.destination.slabrate', compact('data','clients', 'ghats', 'destinations'));
    }

    public function slabRatestore(Request $request)
    {
        // BSRM Logic (Client ID 3)
        if ($request->client_id == 3) {
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

            DestinationSlabRate::create([
                'client_id' => 3,
                'destination_id' => $request->destination_id,
                'ghat_id' => $request->ghat_id,
                'maxqty' => $request->qty,
                'below_rate_per_qty' => $request->below_rate_per_qty,
                'above_rate_per_qty' => $request->above_rate_per_qty,
                'title' => $request->title,
                'date' => date('Y-m-d'),
                'created_by' => Auth::user()->id,
            ]);

        } else {
            // New Multi-Tier Client Logic
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'destination_id' => 'required',
                'ghat_id' => 'required',
                'tiers' => 'required|array|min:1'
            ]);

            if ($validator->fails()) {
                $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
                return response()->json(['status' => 400, 'message' => $errorMessage]);
            }

            foreach ($request->tiers as $tier) {
                if (!isset($tier['rate']) || $tier['rate'] === null) continue;

                DestinationSlabRate::create([
                    'client_id' => $request->client_id,
                    'vendor_id' => $request->vendor_id ?? null,
                    'destination_id' => $request->destination_id,
                    'ghat_id' => $request->ghat_id,
                    'tier_min_qty' => $tier['min_qty'] ?? 0,
                    'tier_max_qty' => $tier['max_qty'] ?? null,
                    'tier_rate' => $tier['rate'],
                    'maxqty' => 0, 
                    'below_rate_per_qty' => 0,
                    'above_rate_per_qty' => 0,
                    'title' => $request->title,
                    'date' => date('Y-m-d'),
                    'created_by' => Auth::user()->id,
                ]);
            }
        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function slabRateedit($id)
    {
        $info = DestinationSlabRate::find($id);
        return response()->json($info);
    }

    public function slabRateupdate(Request $request)
    {
        $data = DestinationSlabRate::find($request->codeid);
        if (!$data) {
            return response()->json(['status'=> 303,'message'=>'Data not found!!']);
        }

        // BSRM Logic (Client ID 3)
        if ($request->client_id == 3) {
            $validator = Validator::make($request->all(), [
                'qty' => 'required',
                'below_rate_per_qty' => 'required',
                'above_rate_per_qty' => 'required'
            ]);
            if ($validator->fails()) {
                $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
                return response()->json(['status' => 400, 'message' => $errorMessage]);
            }

            $data->maxqty = $request->qty;
            $data->below_rate_per_qty = $request->below_rate_per_qty;
            $data->above_rate_per_qty = $request->above_rate_per_qty;

        } else {
            // New Multi-Tier Client Logic
            if (isset($request->tiers[0])) {
                $data->tier_min_qty = $request->tiers[0]['min_qty'] ?? 0;
                $data->tier_max_qty = $request->tiers[0]['max_qty'] ?? null;
                $data->tier_rate = $request->tiers[0]['rate'];
            }
        }

        $data->client_id = $request->client_id;
        $data->vendor_id = $request->vendor_id ?? null;
        $data->destination_id = $request->destination_id;
        $data->ghat_id = $request->ghat_id;
        $data->title = $request->title;
        $data->updated_by = Auth::user()->id;

        if ($data->save()) {
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        } else {
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
