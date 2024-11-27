<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
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
use App\Models\Ghat;
use App\Models\Transaction;
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
        $data = Program::with('programDetail','programDetail.programDestination','programDetail.programDestination.destinationSlabRate')->where('id', $id)->first();
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
        $ghats = Ghat::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        return view('admin.program.create', compact('clients','mvassels','lvassels','vendors','ghats','pumps'));
    }

    public function afterPostProgram()
    {
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $lvassels = LighterVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $ghats = Ghat::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        return view('admin.program.afterchallan', compact('clients','mvassels','lvassels','vendors','ghats','pumps'));
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
        $challanNos = $request->input('challan_no');
        $fuelqtys = $request->input('fuelqty');
        $cashamounts = $request->input('cashamount');
        $fuel_rates = $request->input('fuel_rate');
        $fueltokens = $request->input('fueltoken');
        $petrol_pump_ids = $request->input('petrol_pump_id');

        $program = new Program();
        $program->date = $request->input('date');
        $program->programid = $uprogramid;
        $program->client_id = $request->input('client_id');
        $program->mother_vassel_id = $request->input('mother_vassel_id');
        $program->lighter_vassel_id = $request->input('lighter_vassel_id');
        $program->ghat_id = $request->input('ghat_id');
        $program->consignmentno = $request->input('consignmentno');
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
                $invdtl->ghat_id = $request->input('ghat_id');
                $invdtl->vendor_id = $vendorIds[$key]; 
                $invdtl->truck_number = $truckNumbers[$key]; 
                $invdtl->challan_no = $challanNos[$key]; 
                $invdtl->created_by = Auth::user()->id;
                $invdtl->save();


                $fuelAmnt = $fuel_rates[$key] * $fuelqtys[$key];
                $data = new AdvancePayment();
                $data->program_id = $program->id;
                $data->program_detail_id  = $invdtl->id;
                $data->vendor_id = $vendorIds[$key];
                $data->cashamount = $cashamounts[$key];
                $data->petrol_pump_id = $petrol_pump_ids[$key];
                $data->fuel_rate = $fuel_rates[$key];
                $data->fuelqty = $fuelqtys[$key];
                $data->fueltoken = $fueltokens[$key];
                $data->amount = $fuelAmnt + $cashamounts[$key];
                $data->date = date('Y-m-d');
                $data->save();

                $transaction = new Transaction();
                $transaction->program_id = $program->id;
                $transaction->vendor_id = $vendorIds[$key];
                $transaction->amount = $data->amount;
                $transaction->tran_type = "Advance";
                $transaction->date = date('Y-m-d');
                $transaction->save();
                $transaction->tran_id = 'AD' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();



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


        $currentpDtlIds = $program->programDetail->pluck('id')->toArray();
        $updatedpDtlIds = collect($request->program_detail_id)->filter()->toArray();
        $pIdsToDelete = array_diff($currentpDtlIds, $updatedpDtlIds);
        $program->programDetail()->whereIn('id', $pIdsToDelete)->delete();
        

        foreach($vendorIds as $key => $value)
            {
                if (isset($programDtlIds[$key])) {
                    $invdtl = ProgramDetail::find($programDtlIds[$key]);
                    $invdtl->date = $request->date;
                    $invdtl->program_id = $program->id;
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
                    $invdtl->updated_by = Auth::user()->id;
                    $invdtl->save();
                } else {
                    $invdtl = new ProgramDetail();
                    $invdtl->date = $request->date;
                    $invdtl->program_id = $program->id;
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
            'ghat_id' => 'required',
            'qty' => 'required',
            'below_rate_per_qty' => 'required',
            'above_rate_per_qty' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }


        $prgmDtl = DestinationSlabRate::where('ghat_id', $request->ghat_id)->where('destination_id', $request->destination_id)->first();

        $invdtl = new DestinationSlabRate();
        $invdtl->date = date('Y-m-d');
        $invdtl->destination_id = $request->destination_id;
        $invdtl->ghat_id = $request->ghat_id;
        $invdtl->maxqty = $request->qty;
        $invdtl->below_rate_per_qty = $request->below_rate_per_qty;
        $invdtl->above_rate_per_qty = $request->above_rate_per_qty;
        $invdtl->title = $request->title;
        $invdtl->created_by = Auth::user()->id;
        $invdtl->save();
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
    }



    public function getDestinationSlabRate(Request $request)
    {
        
        // $data = $request->all();
        $data2 = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('status',1)->where('id', $request->prgmdtlid)->get();
        $data = ProgramDestination::where('id', $request->pdid)->first();
        $slabRates = DestinationSlabRate::where('program_destination_id', $data->id)->get();


        $prop = '';
        
            foreach ($slabRates as $rate){
                // <!-- Single Property Start -->
                $prop.= '<div class="form-row dynamic-row">
                            <div class="form-group col-md-3">
                                <input type="number" class="form-control" name="maxqty[]" value="'.$rate->maxqty.'"><input type="hidden" class="form-control" name="rateid[]" value="'.$rate->id.'">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="number" class="form-control" name="rate_per_qty[]" value="'.$rate->rate_per_qty.'">
                            </div>
                            <div class="form-group col-md-1">
                                <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>';
            }
        return response()->json(['status'=> 300, 'data'=>$data, 'rates'=>$prop]);
    }


    public function updateDestinationSlabRate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'updestid' => 'required',
            'amount.*' => 'required',
        ]);
        $allrqt = $request->all();
        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }


        $prgmDtl = ProgramDetail::where('id', $request->prgmdtlid)->first();
        $oldids = $request->input('rateid');
        $rates = $request->input('rate_per_qty');
        $minqtys = $request->input('minqty');
        $maxqtys = $request->input('maxqty');

        $program = ProgramDestination::find($request->pdid);
        $program->destination_id = $request->input('updestid');
        $program->updated_by = auth()->user()->id;
        $program->save();

        // delete
        $currentpDtlIds = $program->destinationSlabRate->pluck('id')->toArray();
        $updatedpDtlIds = collect($request->rateid)->filter()->toArray();
        $pIdsToDelete = array_diff($currentpDtlIds, $updatedpDtlIds);
        $program->destinationSlabRate()->whereIn('id', $pIdsToDelete)->delete();
        // delete

        foreach($rates as $key => $value)
            {

                if (isset($oldids[$key])) {

                    $invdtl = DestinationSlabRate::find($oldids[$key]);
                    $invdtl->program_destination_id = $program->id;
                    $invdtl->vendor_id = $request->input('vendorId');
                    if ($key == 0) {
                        $invdtl->minqty = 1;
                    } else {
                        $invdtl->minqty = $maxqtys[$key-1] + 1;
                    }
                    $invdtl->maxqty = $maxqtys[$key]; 
                    $invdtl->rate_per_qty = $rates[$key]; 
                    $invdtl->updated_by = Auth::user()->id;
                    $invdtl->save();
                } else {

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
                

                
            }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message,'allrqt'=>$allrqt ]);
    }



    public function checkChallan(Request $request)
    {
        
        $chkprgmid = ProgramDetail::with('advancePayment')->where('status',1)->where('challan_no', $request->challan_no)->where('date', $request->date)->first();
        
        if ($chkprgmid) {

            $prgmdtls = ProgramDetail::with('advancePayment')
                                    ->where('status',1)
                                    ->where('challan_no', $request->challan_no)
                                    ->where('date', $request->date)
                                    ->get();

            $program = Program::where('id', $chkprgmid->program_id)->first();
            $prop = '';
        
            foreach ($prgmdtls as $prgmdtl){
                // <!-- Single Property Start -->
                $prop.= '<tr>
                            <td>
                                '.$prgmdtl->advancePayment->vendor->name.'
                            </td>
                            <td>
                                <input type="text" class="form-control" id="truck_number" value="'.$prgmdtl->truck_number.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="cashamount"  value="'.$prgmdtl->advancePayment->cashamount.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fuelqty"  value="'.$prgmdtl->advancePayment->fuelqty.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fuel_rate"  value="'.$prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td> 
                                <input type="number" class="form-control" id="fuel_amount" readonly  value="'.$prgmdtl->advancePayment->fuelqty * $prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fueltoken" value="'.$prgmdtl->advancePayment->fueltoken.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="amount" readonly  value="'.$prgmdtl->advancePayment->amount.'">
                            </td>
                            <td>
                                <a class="btn btn-sm btn-success"><i class="fas fa-arrow-right"></i></a>
                            </td>
                        </tr>';
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan found.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$prop, 'program'=>$program]);


        } else {


            $program = ' ';
            $data = ' ';

            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan No not  found.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data, 'program'=>$program]);
        }
        
    }



    public function checkSlabRate(Request $request)
    {
        $challanqty = $request->challanqty;
        $chkrate = DestinationSlabRate::where('destination_id', $request->destid)->where('ghat_id', $request->ghat)->first();
        
        if ($chkrate) {
            $prop = '';
            if ($challanqty > $chkrate->maxqty) {
                $aboveqty = $challanqty - $chkrate->maxqty;

                $prop.= '<tr>
                            <td><input type="number" class="form-control" id="qty" value="'.$chkrate->maxqty.'" ></td>
                            <td><input type="number" class="form-control" id="rate" value="'.$chkrate->below_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control" id="amnt" value="'.$chkrate->below_rate_per_qty * $chkrate->maxqty.'" ></td>
                        </tr>
                        <tr>
                            <td><input type="number" class="form-control" id="qty" value="'.$aboveqty.'" ></td>
                            <td><input type="number" class="form-control" id="rate" value="'.$chkrate->above_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control" id="amnt" value="'.$chkrate->above_rate_per_qty * $aboveqty.'" ></td>
                        </tr>';

            } else {

                $prop.= '<tr>
                            <td><input type="number" class="form-control" id="qty" value="'.$challanqty.'" ></td>
                            <td><input type="number" class="form-control" id="rate" value="'.$chkrate->below_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control" id="amnt" value="'.$chkrate->below_rate_per_qty * $challanqty.'" ></td>
                        </tr>';
            }
            
            
        
                



            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Slab rate found</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$chkrate, 'rate'=>$prop]);


        }else {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Slab rate not found</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>'']);
        }
        
    }



}
