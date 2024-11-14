<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Destination;
use App\Models\LighterVassel;
use App\Models\MotherVassel;
use App\Models\Program;
use App\Models\Vendor;
use App\Models\ProgramDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{

    public function allPrograms()
    {
        $data = Program::orderby('id','DESC')->get();
        return view('admin.program.index', compact('data'));
    }

    public function programDetail($id)
    {
        $data = Program::with('programDetail')->where('id', $id)->first();
        return view('admin.program.details', compact('data'));
    }


    public function createProgram()
    {
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $lvassels = LighterVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        return view('admin.program.create', compact('clients','mvassels','lvassels','vendors'));
    }


    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'mother_vassel_id' => 'required',
            'lighter_vassel_id' => 'required',
            'vendor_id.*' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        do {
            $uprogramid = random_int(100000, 999999);
        } while (Program::where('programid', $uprogramid)->exists()); 

        $vendorIds = $request->input('vendor_id');
        $truckNumbers = $request->input('truck_number');
        $qtys = $request->input('qty');
        $challanNos = $request->input('challan_no');
        $lineCharges = $request->input('line_charge');
        $tokenfees = $request->input('token_fee');
        $partyNames = $request->input('party_name');
        $amounts = $request->input('amount');

        $program = new Program();
        $program->date = $request->input('date');
        $program->programid = $uprogramid;
        $program->client_id = $request->input('client_id');
        $program->mother_vassel_id = $request->input('mother_vassel_id');
        $program->lighter_vassel_id = $request->input('lighter_vassel_id');
        $program->consignmentno = $request->input('consignmentno');
        $program->headerid = $request->input('headerid');
        $program->qty_per_challan = $request->input('qty_per_challan');
        $program->note = $request->input('note', null);
        $program->created_by = auth()->user()->id;
        $program->save();

        foreach($vendorIds as $key => $value)
            {
                $invdtl = new ProgramDetail();
                $invdtl->date = $request->input('date');
                $invdtl->program_id = $program->id;
                $invdtl->programid = $uprogramid;
                $invdtl->consignmentno = $request->input('consignmentno');
                $invdtl->mother_vassel_id = $request->input('mother_vassel_id');
                $invdtl->lighter_vassel_id = $request->input('lighter_vassel_id');
                $invdtl->client_id = $request->input('client_id');
                $invdtl->vendor_id = $vendorIds[$key]; 
                $invdtl->truck_number = $truckNumbers[$key]; 
                $invdtl->qty = $qtys[$key]; 
                $invdtl->challan_no = $challanNos[$key]; 
                $invdtl->line_charge = $lineCharges[$key]; 
                $invdtl->token_fee = $tokenfees[$key]; 
                $invdtl->party_name = $partyNames[$key]; 
                $invdtl->amount = $amounts[$key]; 
                $invdtl->created_by = Auth::user()->id;
                $invdtl->save();
            }
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }
}
