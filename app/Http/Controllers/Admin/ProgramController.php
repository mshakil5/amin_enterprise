<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Destination;
use App\Models\LighterVassel;
use App\Models\MotherVassel;
use App\Models\PetrolPump;
use App\Models\Program;
use App\Models\Vendor;
use App\Models\ProgramDetail;
use App\Models\ProgramDestination;
use App\Models\DestinationSlabRate;
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
        $data = Program::with('programDetail','programDetail.programDestination')->where('id', $id)->first();
        // dd($data);
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        return view('admin.program.details', compact('data','pumps'));
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
        $program->amount = $request->input('camount');
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

    public function programEdit($id)
    {
        $program = Program::with('programDetail')->where('id', $id)->first();
        

        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $lvassels = LighterVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        return view('admin.program.edit', compact('clients','mvassels','lvassels','vendors','program'));
    }


    public function programUpdate(Request $request)
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
        $programDtlIds = $request->input('program_detail_id');

        $program = Program::find($request->pid);
        $program->date = $request->input('date');
        $program->programid = $uprogramid;
        $program->client_id = $request->input('client_id');
        $program->mother_vassel_id = $request->input('mother_vassel_id');
        $program->lighter_vassel_id = $request->input('lighter_vassel_id');
        $program->consignmentno = $request->input('consignmentno');
        $program->headerid = $request->input('headerid');
        $program->qty_per_challan = $request->input('qty_per_challan');
        $program->amount = $request->input('camount');
        $program->note = $request->input('note', null);
        $program->created_by = auth()->user()->id;
        $program->save();


        $currentColorIds = $program->programDetail->pluck('id')->toArray();
        $updatedColorIds = collect($request->program_detail_id)->filter()->toArray();
        $colorIdsToDelete = array_diff($currentColorIds, $updatedColorIds);
        $program->programDetail()->whereIn('id', $colorIdsToDelete)->delete();
        

        foreach($vendorIds as $key => $value)
            {
                if (isset($programDtlIds[$key])) {
                    $invdtl = ProgramDetail::find($programDtlIds[$key]);
                    $invdtl->date = $request->date;
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
                } else {
                    $invdtl = new ProgramDetail();
                    $invdtl->date = $request->date;
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
            }

            

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function prgmDelete($id)
    {
        $data = Program::find($id);
        

        if ($data->delete()) {
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to delete.'], 500);
        }
    }

    public function addDestinationSlabRate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'destination_id' => 'required',
            'amount.*' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }


        $prgmDtl = ProgramDetail::where('id', $request->prgmdtlid)->first();
        $rates = $request->input('rate_per_qty');
        $minqtys = $request->input('minqty');
        $maxqtys = $request->input('maxqty');

        $program = new ProgramDestination();
        $program->vendor_id = $request->input('vendorId');
        $program->destination_id = $request->input('destination_id');
        $program->program_id = $prgmDtl->program_id;
        $program->program_detail_id = $request->prgmdtlid;
        $program->created_by = auth()->user()->id;
        $program->save();

        foreach($rates as $key => $value)
            {
                $invdtl = new DestinationSlabRate();
                $invdtl->program_destination_id = $program->id;
                $invdtl->vendor_id = $request->input('vendorId');
                $invdtl->program_id = $prgmDtl->program_id;
                $invdtl->program_detail_id = $request->prgmdtlid;
                if ($key == 0) {
                    $invdtl->minqty = 1;
                } else {
                    $invdtl->minqty = $maxqtys[$key-1] + 1;
                }
                $invdtl->maxqty = $maxqtys[$key]; 
                $invdtl->rate_per_qty = $rates[$key]; 
                $invdtl->created_by = Auth::user()->id;
                $invdtl->save();
            }
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }













}
