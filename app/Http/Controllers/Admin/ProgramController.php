<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\ChallanRate;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{

    public function allPrograms()
    {
        $data = Program::
        withCount([
            'programDetail as unique_challan_count' => function ($query) {
                $query->select(DB::raw('COUNT(DISTINCT challan_no)'));
            },
            'programDetail as generate_bill_count' => function ($query) {
                $query->where('generate_bill', 1);
            },
            'programDetail as not_generate_bill_count' => function ($query) {
                $query->where('generate_bill', 0);
            }
        ])->orderby('id','DESC')->get();

        // dd( $data );

        return view('admin.program.index', compact('data'));
    }

    public function programDetail($id)
    {
        $data = Program::with('programDetail','programDetail.programDestination','programDetail.programDestination.destinationSlabRate')->where('id', $id)->first();
        // dd($data);
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        return view('admin.program.details', compact('data','pumps'));
    }

    public function programVendor($id)
    {
        $pid = $id;



        $data = ProgramDetail::select('vendor_id',
                    DB::raw('SUM(dest_qty) as total_dest_qty'),
                    DB::raw('SUM(line_charge) as total_line_charge'),
                    DB::raw('SUM(carrying_bill) as total_carrying_bill'),
                    DB::raw('SUM(scale_fee) as total_scale_fee'),
                    DB::raw('SUM(advance) as total_advance'),
                    DB::raw('SUM(other_cost) as total_other_cost'),
                    DB::raw('SUM(due) as total_due')
            )->where([
                ['generate_bill','=', '1'],
                ['program_id','=', $pid]
            ])->groupBy('vendor_id')->get();





        return view('admin.program.vendor_report', compact('data','pid'));
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
            'client_id' => 'required',
            'mother_vassel_id' => 'required',
            'ghat_id' => 'required',
            'vendor_id.*' => 'required',
            'truck_number.*' => 'required',
            'challan_no.*' => 'required',
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
                $data->fuelamount = $fuel_rates[$key] * $fuelqtys[$key];
                $data->amount = $fuelAmnt + $cashamounts[$key];
                $data->date = date('Y-m-d');
                $data->save();

                if ($cashamounts[$key] > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $request->input('client_id');
                    $transaction->mother_vassel_id = $request->input('mother_vassel_id');
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $cashamounts[$key];
                    $transaction->tran_type = "Advance";
                    $transaction->payment_type = "Cash";
                    $transaction->date = date('Y-m-d');
                    $transaction->save();
                    $transaction->tran_id = 'RT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                if ($fuelAmnt > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $request->input('client_id');
                    $transaction->mother_vassel_id = $request->input('mother_vassel_id');
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $fuelAmnt;
                    $transaction->tran_type = "Advance";
                    $transaction->payment_type = "Fuel";
                    $transaction->date = date('Y-m-d');
                    $transaction->save();
                    $transaction->tran_id = 'RT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                
            }
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message,'program'=>$program]);
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
        
        $chkprgmid = ProgramDetail::with('advancePayment')->where('status',1)->where('rate_status',0)->where('challan_no', $request->challan_no)->where('mother_vassel_id', $request->mv_id)->first();
        
        if ($chkprgmid) {

            $prgmdtls = ProgramDetail::with('advancePayment')
                                    ->where('status',1)
                                    ->where('mother_vassel_id', $request->mv_id)
                                    ->where('challan_no', $request->challan_no)
                                    ->get();

            $program = Program::where('id', $chkprgmid->program_id)->first();
            $vendors = Vendor::select('id', 'name')->orderby('id', 'DESC')->get();
            $prop = '';
        
            foreach ($prgmdtls as $prgmdtl){
                // <!-- Single Property Start -->

                if (isset($prgmdtl->destination_id)) {
                    $challanRate = ChallanRate::where('program_detail_id', $prgmdtl->id)->where('challan_no', $request->challan_no)->get();

                    $prate = '';

                    foreach ($challanRate as $key => $rate) {
                        $id = $key+1;
                        $prate.= '<tr>
                            <td><input type="number" class="form-control qty" id="slabqty'.$id.'" name="qty[]" value="'.$rate->qty.'" ><input type="hidden" class="form-control" id="challanrateid'.$id.'" name="challanrateid[]" value="'.$rate->id.'" ></td>
                            <td><input type="number" class="form-control rate" id="slabrate'.$id.'" name="rate[]" value="'.$rate->rate_per_unit.'" ></td>
                            <td><input type="number" class="form-control rateunittotal" id="slabamnt'.$id.'" name="amnt[]" value="'.$rate->total.'" readonly></td>
                        </tr>';
                    }



                } else {
                    $prate = '';
                }
                

                $prop.= '<tr>
                            <td>
                                <select class="form-control" id="vendor_id'.$prgmdtl->id.'" name="vendor_id">
                                <option value="'.$prgmdtl->advancePayment->vendor_id.'" selected>'.$prgmdtl->advancePayment->vendor->name.'</option>';
                                foreach ($vendors as $vendor){
                                    $prop.= '<option value="'.$vendor->id.'">'.$vendor->name.'</option>';
                                }
                        $prop.= '</select>
                            </td>
                            <td>
                                <input type="text" class="form-control" id="truck_number'.$prgmdtl->id.'" value="'.$prgmdtl->truck_number.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="cashamount"  value="'.$prgmdtl->advancePayment->cashamount.'" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fuelqty'.$prgmdtl->id.'" name="fuelqty"  value="'.$prgmdtl->advancePayment->fuelqty.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fuel_rate'.$prgmdtl->id.'" name="fuel_rate"  value="'.$prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td> 
                                <input type="number" class="form-control" id="fuel_amount'.$prgmdtl->id.'" readonly  value="'.$prgmdtl->advancePayment->fuelqty * $prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fueltoken'.$prgmdtl->id.'" value="'.$prgmdtl->advancePayment->fueltoken.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="amount'.$prgmdtl->id.'" readonly  value="'.$prgmdtl->advancePayment->amount.'">
                            </td>
                            <td>
                                <span class="btn btn-sm btn-success addrateThis" data-pdtlid="'.$prgmdtl->id.'" data-adv="'.$prgmdtl->advancePayment->amount.'" data-headerid="'.$prgmdtl->headerid.'" data-destqty="'.$prgmdtl->dest_qty.'" data-linecharge="'.$prgmdtl->line_charge.'" data-scale_fee="'.$prgmdtl->scale_fee.'" data-other_cost="'.$prgmdtl->other_cost.'" data-destination_id="'.$prgmdtl->destination_id.'" data-advid="'.$prgmdtl->advancePayment->id.'" data-due="'.$prgmdtl->due.'" data-additional_cost="'.$prgmdtl->additional_cost.'" data-carrying_bill="'.$prgmdtl->carrying_bill.'"><i class="fas fa-arrow-right"></i></span>
                            </td>
                        </tr>';
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan found.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$prop, 'program'=>$program, 'prgmdtls'=>$prgmdtls, 'prate'=>$prate]);


        } else {


            $program = 'empty';
            $data = 'empty';

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
            $totalAmount = 0;
            if ($challanqty > $chkrate->maxqty) {
                $aboveqty = $challanqty - $chkrate->maxqty;
                $totalAmount = $totalAmount + $chkrate->above_rate_per_qty * $aboveqty + $chkrate->below_rate_per_qty * $chkrate->maxqty;
                $prop.= '<tr>
                            <td><input type="number" class="form-control qty" id="slabqty1" name="qty[]" value="'.$chkrate->maxqty.'" ></td>
                            <td><input type="number" class="form-control rate" id="slabrate1" name="rate[]" value="'.$chkrate->below_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control rateunittotal" id="slabamnt1" name="amnt[]" value="'.$chkrate->below_rate_per_qty * $chkrate->maxqty.'" readonly></td>
                        </tr>
                        <tr>
                            <td><input type="number" class="form-control qty" id="slabqty2" name="qty[]" value="'.$aboveqty.'" ></td>
                            <td><input type="number" class="form-control rate" id="slabrate2" name="rate[]" value="'.$chkrate->above_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control rateunittotal" id="slabamnt2" name="amnt[]" value="'.$chkrate->above_rate_per_qty * $aboveqty.'" readonly ></td>
                        </tr>';

            } else {

                $totalAmount = $totalAmount + $chkrate->below_rate_per_qty * $chkrate->maxqty;
                $prop.= '<tr>
                            <td><input type="number" class="form-control qty" id="slabqty1" name="qty[]" value="'.$challanqty.'" ></td>
                            <td><input type="number" class="form-control rate" id="slabrate1" name="rate[]" value="'.$chkrate->below_rate_per_qty.'" ></td>
                            <td><input type="number" class="form-control rateunittotal" id="slabamnt1" name="amnt[]" value="'.$chkrate->below_rate_per_qty * $challanqty.'" readonly></td>
                        </tr>';
            }
            
            
        
                



            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Slab rate found</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$chkrate, 'rate'=>$prop, 'totalAmount' => $totalAmount]);


        }else {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Slab rate not found</b></div>";
            $totalAmount = 0;
            return response()->json(['status'=> 300,'message'=>$message, 'data'=>'', 'totalAmount' => $totalAmount]);
        }
        
    }


    public function afterPostProgramStore(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'prgmdtlid' => 'required',
            'destid' => 'required',
            'headerid' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $prgmdtl = ProgramDetail::where('id', $request->prgmdtlid)->first();
        $prgm = Program::where('id', $prgmdtl->program_id)->first();

        
        $data = $request->all();
        $qtys = $request->input('qty');
        $rates = $request->input('rate');
        $oldchallanrateids = $request->input('challanrateid');

        $fadv = AdvancePayment::find($request->advPmtid);
        $fadv->vendor_id = $request->vendor_id;
        $fadv->fuelqty = $request->fuelqty;
        $fadv->fuel_rate = $request->fuel_rate;
        $fadv->fueltoken = $request->fueltoken;
        $fadv->fuelamount = $request->fuelqty * $request->fuel_rate;
        $fadv->amount = $fadv->fuelamount + $fadv->cashamount;
        $fadv->save();

        $progrm = ProgramDetail::find($request->prgmdtlid);

        //importent
        if ($progrm->destination_id != $request->destid) {
            $dltoldchallanrate = ChallanRate::where('challan_no', $progrm->challan_no)->where('program_detail_id', $progrm->id)->delete();
        }
        //importent


        $progrm->after_date = date('Y-m-d');
        $progrm->destination_id = $request->destid;
        $progrm->ghat_id = $prgm->ghat_id;
        $progrm->program_id = $prgm->id;
        $progrm->vendor_id = $request->vendor_id; 
        $progrm->truck_number = $request->truck_number; 
        $progrm->headerid = $request->headerid; 
        $progrm->dest_qty = $request->totalqtyasperchallan;
        $progrm->challan_no = $prgmdtl->challan_no;
        $progrm->line_charge = $request->line_charge; 
        $progrm->scale_fee = $request->scale_fee; 
        $progrm->other_cost = $request->other_cost; 
        $progrm->transportcost = $request->totalamount; 
        $progrm->carrying_bill = $request->totalamount; 
        $progrm->additional_cost = $request->additionalCost;
        $progrm->advance = $fadv->amount; 
        $progrm->due = $request->totalamount + $request->additionalCost - $fadv->amount; 
        $progrm->rate_status = 0;
        $progrm->save();


        

            foreach($rates as $key => $value)
            {


                if (isset($oldchallanrateids[$key])) {

                    $chalanRate = ChallanRate::find($oldchallanrateids[$key]);
                    $chalanRate->program_detail_id = $progrm->id;
                    $chalanRate->challan_no = $progrm->challan_no;
                    $chalanRate->qty = $qtys[$key]; 
                    $chalanRate->rate_per_unit = $rates[$key]; 
                    $chalanRate->total = $rates[$key] * $qtys[$key]; 
                    $chalanRate->created_by = Auth::user()->id;
                    $chalanRate->save();

                } else {

                    $chalanRate = new ChallanRate();
                    $chalanRate->program_detail_id = $progrm->id;
                    $chalanRate->challan_no = $progrm->challan_no;
                    $chalanRate->qty = $qtys[$key]; 
                    $chalanRate->rate_per_unit = $rates[$key]; 
                    $chalanRate->total = $rates[$key] * $qtys[$key]; 
                    $chalanRate->created_by = Auth::user()->id;
                    $chalanRate->save();
                }
                
                
                
            }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan completed.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data]);
        
        
    }



}
