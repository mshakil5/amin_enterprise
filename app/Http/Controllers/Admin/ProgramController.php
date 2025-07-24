<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\ChallanRate;
use App\Models\ChallanRateLog;
use App\Models\Client;
use App\Models\ClientRate;
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
use App\Models\VendorSequenceNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;

class ProgramController extends Controller
{

    public function allPrograms()
    {
      if (!(in_array('14', json_decode(auth()->user()->role->permission)))) {
        return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
      }
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
            },
            'programDetail as deleted_count' => function ($query) {
                $query->onlyTrashed();
            },
            'programDetail as pump_count' => function ($query) {
                $query->whereHas('advancePayment', function ($q) {
                    $q->whereNotNull('petrol_pump_id');
                });
            },
            'programDetail as after_challan_posting_count' => function ($query) {
                $query->whereNotNull('headerid');
            },
            'programDetail as before_challan_count' => function ($query) {
                $query->whereNull('headerid');
            },
            
        ])->where('status', 1)->orderby('id','DESC')->get();

        // dd( $data );

        return view('admin.program.index', compact('data'));
    }

    public function programDetail($id)
    {
        $data = Program::with('programDetail','programDetail.programDestination','programDetail.advancePayment','programDetail.advancePayment.petrolPump','programDetail.programDestination.destinationSlabRate')->where('id', $id)->first();
        // dd($data);
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $motherVesselName = $data->motherVassel->name;

        $vlist = AdvancePayment::select('vendor_id',
                    DB::raw('SUM(fuelqty) as total_fuelqty'),
                    DB::raw('SUM(fuelamount) as total_fuelamount'),
                    DB::raw('SUM(cashamount) as total_cashamount'),
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('COUNT(*) as vendor_count')
            )->where([
                ['program_id','=', $id]
            ])->groupBy('vendor_id')->get();



        $dates = ProgramDetail::select(DB::raw('DATE(date) as date'))
                    ->where('program_id','=', $id)
                    ->groupBy('date')
                    ->orderBy('date', 'DESC')
                    ->get();


                    

        return view('admin.program.details', compact('data','pumps','vendors','vlist','dates','motherVesselName'));
    }

    public function vendorWiseProgramDetails(Request $request)
    {
        $vid = $request->vendor_id;
        $mid = $request->mvassel_id;

        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('vendor_id', $vid)->where('mother_vassel_id', $mid)->get();
        
        $vendor = Vendor::select('id','name')->where('id',$vid)->first();
        $motherVesselName = MotherVassel::where('id', $mid)->first()->name;

        $duePaymentTransaction = Transaction::where('vendor_id', $vid)
                                  ->where('mother_vassel_id', $mid)
                                  ->where('description', 'Carrying Bill')
                                  ->where('tran_type', 'Due Payment')
                                  ->select('amount')
                                  ->sum('amount');
        $missingHeaderIds = collect([]);
        return view('admin.report.challanPostingReport', compact('data','vendor','motherVesselName','mid', 'vid', 'duePaymentTransaction', 'missingHeaderIds'));
    }

    
    // getVendorAdvanceByDate
    public function getVendorAdvanceByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }

        $programId = $request->input('program_id');
        $date = $request->input('date');

        // program details
        $program = Program::with('motherVassel:id,name')->where('id', $programId)->first();

        $vendorAdvances = AdvancePayment::select(
                'vendor_id',
                DB::raw('SUM(fuelqty) as total_fuelqty'),
                DB::raw('SUM(fuelamount) as total_fuelamount'),
                DB::raw('SUM(cashamount) as total_cashamount'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as vendor_count')
            )
            ->with('vendor:id,name')
            ->where('program_id', $programId)
            ->whereHas('programDetail', function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->groupBy('vendor_id')
            ->get();

        return response()->json(['status' => 200, 'data' => $vendorAdvances, 'date' => $date, 'program' => $program]);
    }

    // changeQuantity
    public function changeQuantity2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer',
            'newQty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }

        $programId = $request->input('program_id');
        $newQty = $request->input('newQty');

        $programDetails = ProgramDetail::where('program_id', $request->program_id)->get();

        foreach ($programDetails as $detail) {
            $detail->old_qty = $detail->dest_qty;
            $detail->dest_qty = $newQty;
            $detail->save(); 
        }
        
        return response()->json(['status' => 200,  'program' => $programId]);
    }

    public function changeQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer',
            'newQty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }

        $programId = $request->program_id;
        $newQty = $request->newQty; // Expecting an array of new data

        $program_details = ProgramDetail::where('program_id', $programId)->get();

        DB::beginTransaction();

        try {
            // Get all program_details IDs related to the program
            $programDetailIds = ProgramDetail::where('program_id', $programId)->pluck('id');
            

            // CHALLAN RATE backup
            foreach ($program_details as $key => $pdtls) {

                if($pdtls->ghat_id == Null || $pdtls->destination_id == Null ){
                    continue;
                }

                $chkrate = DestinationSlabRate::where('ghat_id', $pdtls->ghat_id)->where('destination_id', $pdtls->destination_id)->first();

                $oldQty = ChallanRate::where('program_detail_id', $pdtls->id)->where('challan_no', $pdtls->challan_no)->first();

                if (!$chkrate) {
                    DB::rollBack();
                    return response()->json(['status' => 400,'error' => 'Rate not found for ghat and destination'], 500);
                }
                $oldData[] = [
                    'program_id' => $programId, 
                    // 'ghat_id' => $pdtls->ghat_id, 
                    // 'destination_id' => $pdtls->destination_id, 
                    'program_detail_id' => $pdtls->id, 
                    'challan_no' => $pdtls->challan_no, 
                    'rate_per_unit' => $chkrate->below_rate_per_qty,
                    'qty' => $oldQty->qty,
                    'total' => $oldQty->qty * $chkrate->below_rate_per_qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
            }

            // Bulk insert new records
            if (!empty($oldData)) {
                ChallanRateLog::insert($oldData);
            }

            // Delete related challan_rates
            ChallanRate::whereIn('program_detail_id', $programDetailIds)->delete();

            // Insert new challan_rates
            $newData = [];
            foreach ($program_details as $program_detail) {
                // check rate

                if($program_detail->ghat_id == Null || $program_detail->destination_id == Null ){
                    continue;
                }

                $chkrate = DestinationSlabRate::where('ghat_id', $program_detail->ghat_id)->where('destination_id', $program_detail->destination_id)->first();
                if (!$chkrate) {
                    DB::rollBack();
                    return response()->json(['status' => 400,'error' => 'Rate not found for ghat and destination'], 500);
                }
                $newData[] = [
                    'program_detail_id' => $program_detail->id, 
                    'challan_no' => $program_detail->challan_no, 
                    'rate_per_unit' => $chkrate->below_rate_per_qty,
                    'qty' => $newQty,
                    'total' => $newQty * $chkrate->below_rate_per_qty,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $updatepdtls = ProgramDetail::find($program_detail->id);
                $updatepdtls->old_qty = $updatepdtls->dest_qty;
                $updatepdtls->old_carrying_bill = $updatepdtls->carrying_bill;
                $updatepdtls->dest_qty = $newQty;
                $updatepdtls->carrying_bill = $newQty * $chkrate->below_rate_per_qty;
                $updatepdtls->save();
                    
            }

            // Bulk insert new records
            if (!empty($newData)) {
                ChallanRate::insert($newData);
            }

            DB::commit();

            return response()->json(['status' => 200,'message' => 'Challan rates updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 400,'error' => 'Something went wrong', 'message' => $e->getMessage()], 500);
        }
    }

    public function undoChangeQuantity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }

        $programId = $request->input('program_id');
        $programDetails = ProgramDetail::where('program_id', $request->program_id)->get();

        foreach ($programDetails as $detail) {
            $detail->dest_qty = $detail->old_qty;
            $detail->old_qty = null;
            $detail->save(); 
        }
        
        return response()->json(['status' => 200,  'program' => $programId]);
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


        $billnotgenerate = ProgramDetail::select('vendor_id',
                    DB::raw('SUM(dest_qty) as total_dest_qty'),
                    DB::raw('SUM(line_charge) as total_line_charge'),
                    DB::raw('SUM(carrying_bill) as total_carrying_bill'),
                    DB::raw('SUM(scale_fee) as total_scale_fee'),
                    DB::raw('SUM(advance) as total_advance'),
                    DB::raw('SUM(other_cost) as total_other_cost'),
                    DB::raw('SUM(due) as total_due'),
                    DB::raw('COUNT(*) as vendor_count')
            )->where([
                ['generate_bill','=', '0'],
                ['program_id','=', $pid]
            ])->groupBy('vendor_id')->get();


        return view('admin.program.vendor_report', compact('data','pid','billnotgenerate'));
    }


    public function createProgram()
    {
        if (!(in_array('14', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
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
        if (!(in_array('14', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
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
            'mother_vassel_id' => 'required|unique:programs,mother_vassel_id,NULL,id,ghat_id,' . $request->ghat_id,
            'ghat_id' => 'required|unique:programs,ghat_id,NULL,id,mother_vassel_id,' . $request->mother_vassel_id,
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

                $invdtl->advance = $data->amount;
                $invdtl->save();

                if ($cashamounts[$key] > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $request->input('client_id');
                    $transaction->mother_vassel_id = $request->input('mother_vassel_id');
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->advance_payment_id = $data->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $cashamounts[$key];
                    $transaction->tran_type = "Advance";
                    $transaction->description = "Cash Advance to Vendor";
                    $transaction->payment_type = "Cash";
                    $transaction->table_type = "AdvancePayment";
                    $transaction->date = date('Y-m-d');
                    $transaction->save();
                    $transaction->tran_id = 'CA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                if ($fuelAmnt > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $request->input('client_id');
                    $transaction->mother_vassel_id = $request->input('mother_vassel_id');
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->advance_payment_id = $data->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $fuelAmnt;
                    $transaction->tran_type = "Advance";
                    $transaction->description = "Fuel Advance to Vendor";
                    $transaction->payment_type = "Fuel";
                    $transaction->table_type = "AdvancePayment";
                    $transaction->date = date('Y-m-d');
                    $transaction->save();
                    $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                
            }
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message,'program'=>$program]);
    }


    public function addMoreChallan(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'vendor_id.*' => 'required',
            'truck_number.*' => 'required',
            'challan_no.*' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $vendorIds = $request->input('vendor_id');
        $truckNumbers = $request->input('truck_number');
        $challanNos = $request->input('challan_no');
        $fuelqtys = $request->input('fuelqty');
        $cashamounts = $request->input('cashamount');
        $fuel_rates = $request->input('fuel_rate');
        $fueltokens = $request->input('fueltoken');
        $petrol_pump_ids = $request->input('petrol_pump_id');

        $program = Program::where('id', $request->program_id)->first();

        

        foreach($vendorIds as $key => $value)
            {
                $invdtl = new ProgramDetail();
                $invdtl->date = $request->input('newDate');
                $invdtl->program_id = $program->id;
                $invdtl->programid = $program->programid;
                $invdtl->consignmentno = $program->consignmentno;
                $invdtl->mother_vassel_id = $program->mother_vassel_id;
                $invdtl->lighter_vassel_id = $program->lighter_vassel_id;
                $invdtl->client_id = $program->client_id;
                $invdtl->ghat_id = $program->ghat_id;
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
                $data->date = $request->input('newDate');
                $data->save();

                if ($cashamounts[$key] > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $program->client_id;
                    $transaction->mother_vassel_id = $program->mother_vassel_id;
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $cashamounts[$key];
                    $transaction->tran_type = "Advance";
                    $transaction->payment_type = "Cash";
                    $transaction->description = "Cash Advance to Vendor";
                    $transaction->date = $request->input('newDate');
                    $transaction->save();
                    $transaction->tran_id = 'CA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                if ($fuelAmnt > 0) {
                    $transaction = new Transaction();
                    $transaction->client_id = $program->client_id;
                    $transaction->mother_vassel_id = $program->mother_vassel_id;
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $invdtl->id;
                    $transaction->vendor_id = $vendorIds[$key];
                    $transaction->challan_no = $challanNos[$key]; 
                    $transaction->amount = $fuelAmnt;
                    $transaction->tran_type = "Advance";
                    $transaction->description = "Fuel Advance to Vendor";
                    $transaction->payment_type = "Fuel";
                    $transaction->date = $request->input('newDate');
                    $transaction->save();
                    $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }

                
            }
        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Created Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message,'program'=>$program]);
    }


    public function programEdit($id)
    {
        $program = Program::with('programDetail.advancePayment','programDetail.transaction')->where('id', $id)->first();

        // dd($program );
        

        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $lvassels = LighterVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $ghats = Ghat::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        return view('admin.program.edit', compact('clients','mvassels','lvassels','vendors','program','ghats','pumps'));
    }


    public function programDetailsEdit($id)
    {
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $lvassels = LighterVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $ghats = Ghat::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $pumps = PetrolPump::select('id', 'name')->where('status', 1)->get();
        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate','advancePayment','challanRate')->where('id', $id)->first();

        // dd($data);
        return view('admin.program.programDetails', compact('data','clients','mvassels','lvassels','vendors','ghats','pumps'));
    }

    public function programUpdate(Request $request)
    {

        $alldata = $request->all();
        
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


        $vendorIds = $request->input('vendor_id');
        $truckNumbers = $request->input('truck_number');
        $challanNos = $request->input('challan_no');
        $fuelqtys = $request->input('fuelqty');
        $cashamounts = $request->input('cashamount');
        $fuel_rates = $request->input('fuel_rate');
        $fueltokens = $request->input('fueltoken');
        $petrol_pump_ids = $request->input('petrol_pump_id');
        $programDtlIds = $request->input('program_detail_id');
        $advancePaymentIds = $request->input('advancePaymentId');


        $program = Program::find($request->pid);
        $program->date = $request->input('date');
        $program->client_id = $request->input('client_id');
        $program->mother_vassel_id = $request->input('mother_vassel_id');
        $program->lighter_vassel_id = $request->input('lighter_vassel_id');
        $program->ghat_id = $request->input('ghat_id');
        $program->consignmentno = $request->input('consignmentno');
        $program->note = $request->input('note', null);
        $program->updated_by = auth()->user()->id;
        $program->save();


        // if program details delete option add then this part will needed
        // $currentpDtlIds = $program->programDetail->pluck('id')->toArray();
        // $updatedpDtlIds = collect($request->program_detail_id)->filter()->toArray();
        // $pIdsToDelete = array_diff($currentpDtlIds, $updatedpDtlIds);
        // AdvancePayment::whereIn('program_detail_id', $pIdsToDelete)->delete();
        // $program->programDetail()->whereIn('id', $pIdsToDelete)->delete();
        

        foreach($vendorIds as $key => $value)
            {
                if (isset($programDtlIds[$key])) {
                    $invdtl = ProgramDetail::find($programDtlIds[$key]);
                    $invdtl->date = $request->input('date');
                    $invdtl->program_id = $program->id;
                    $invdtl->consignmentno = $request->input('consignmentno');
                    $invdtl->mother_vassel_id = $request->input('mother_vassel_id');
                    $invdtl->lighter_vassel_id = $request->input('lighter_vassel_id');
                    $invdtl->client_id = $request->input('client_id');
                    $invdtl->ghat_id = $request->input('ghat_id');
                    $invdtl->vendor_id = $vendorIds[$key]; 
                    $invdtl->truck_number = $truckNumbers[$key]; 
                    $invdtl->challan_no = $challanNos[$key]; 
                    $invdtl->updated_by = Auth::user()->id;
                    $invdtl->save();

                    $fuelAmnt = $fuel_rates[$key] * $fuelqtys[$key];
                    $data = AdvancePayment::find($advancePaymentIds[$key]);
                    $data->date = $request->input('date');
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
                    $data->save();

                    if ($cashamounts[$key] > 0) {
                        $chkCashTran = Transaction::where('program_detail_id', $invdtl->id)->where('payment_type', 'Cash')->first();
                        $transaction = Transaction::find($chkCashTran->id);
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
                        $chkFuelTran = Transaction::where('program_detail_id', $invdtl->id)->where('payment_type', 'Fuel')->first();
                        $transaction = Transaction::find($chkFuelTran->id);
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

                } else {

                    $invdtl = new ProgramDetail();
                    $invdtl->date = $request->input('date');
                    $invdtl->program_id = $program->id;
                    $invdtl->consignmentno = $request->input('consignmentno');
                    $invdtl->mother_vassel_id = $request->input('mother_vassel_id');
                    $invdtl->lighter_vassel_id = $request->input('lighter_vassel_id');
                    $invdtl->client_id = $request->input('client_id');
                    $invdtl->ghat_id = $request->input('ghat_id');
                    $invdtl->vendor_id = $vendorIds[$key]; 
                    $invdtl->truck_number = $truckNumbers[$key]; 
                    $invdtl->challan_no = $challanNos[$key]; 
                    $invdtl->updated_by = Auth::user()->id;
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
                    $data->date = $request->input('date');
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
                        $transaction->description = "Cash Advance to Vendor";
                        $transaction->date = $request->input('date');
                        $transaction->save();
                        $transaction->tran_id = 'CA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
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
                        $transaction->description = "Fuel Advance to Vendor";
                        $transaction->date = $request->input('date');
                        $transaction->save();
                        $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                        $transaction->save();
                    }
                }
            }

            

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data Updated Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message,'all'=>$alldata]);
    }

    public function updateSingleRow(Request $request)
    {
        $request->validate([
            'program_detail_id' => 'required|exists:program_details,id',
            'advance_payment_id' => 'required|exists:advance_payments,id',
            'vendor_id' => 'required|exists:vendors,id',
            'truck_number' => 'required|string',
            'challan_no' => 'required|numeric',
            'cashamount' => 'nullable|numeric',
            'fuelqty' => 'nullable|numeric',
            'fuel_rate' => 'nullable|numeric',
            'petrol_pump_id' => 'nullable|exists:petrol_pumps,id',
            'fueltoken' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $programDetail = ProgramDetail::findOrFail($request->program_detail_id);
            $programDetail->date = $request->pdtldate;
            $programDetail->vendor_id = $request->vendor_id;
            $programDetail->truck_number = $request->truck_number;
            $programDetail->challan_no = $request->challan_no;
            $programDetail->updated_by = auth()->id();
            $programDetail->save();

            $program = Program::where('id', $programDetail->program_id)->first();

            $fuelAmount = ($request->fuel_rate ?? 0) * ($request->fuelqty ?? 0);
            $advancePayment = AdvancePayment::findOrFail($request->advance_payment_id);
            $advancePayment->date = $request->pdtldate;
            $advancePayment->vendor_id = $request->vendor_id;
            $advancePayment->cashamount = $request->cashamount ?? 0;
            $advancePayment->petrol_pump_id = $request->petrol_pump_id;
            $advancePayment->fuel_rate = $request->fuel_rate ?? 0;
            $advancePayment->fuelqty = $request->fuelqty ?? 0;
            $advancePayment->fueltoken = $request->fueltoken;
            $advancePayment->fuelamount = $fuelAmount;
            $advancePayment->amount = ($request->cashamount ?? 0) + $fuelAmount;
            $advancePayment->save();

            if ($request->cashamount) {
                $cashTran = Transaction::where('program_detail_id', $request->program_detail_id)
                            ->where('payment_type', 'Cash')
                            ->first();

                if ($cashTran) {
                    $cashTran->vendor_id = $request->vendor_id;
                    $cashTran->challan_no = $request->challan_no;
                    $cashTran->amount = $request->cashamount ?? 0;
                    $cashTran->save();
                }else {
                    $transaction = new Transaction();
                    $transaction->client_id = $program->client_id;
                    $transaction->mother_vassel_id = $program->mother_vassel_id;
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $request->program_detail_id;
                    $transaction->vendor_id = $request->vendor_id;
                    $transaction->challan_no = $request->challan_no;
                    $transaction->amount = $request->cashamount ?? 0;
                    $transaction->tran_type = "Advance";
                    $transaction->payment_type = "Cash";
                    $transaction->description = "Cash Advance to Vendor";
                    $transaction->date = $request->pdtldate;
                    $transaction->save();
                    $transaction->tran_id = 'CA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }
            }

            

            if ($request->fuelqty) {
                $fuelTran = Transaction::where('program_detail_id', $request->program_detail_id)
                            ->where('payment_type', 'Fuel')
                            ->first();

                if ($fuelTran) {
                    $fuelTran->vendor_id = $request->vendor_id;
                    $fuelTran->challan_no = $request->challan_no;
                    $fuelTran->amount = $fuelAmount;
                    $fuelTran->save();
                }else {
                    
                    $transaction = new Transaction();
                    $transaction->client_id = $program->client_id;
                    $transaction->mother_vassel_id = $program->mother_vassel_id;
                    $transaction->program_id = $program->id;
                    $transaction->program_detail_id = $request->program_detail_id;
                    $transaction->vendor_id = $request->vendor_id;
                    $transaction->challan_no = $request->challan_no; 
                    $transaction->amount = $fuelAmount;
                    $transaction->tran_type = "Advance";
                    $transaction->payment_type = "Fuel";
                    $transaction->description = "Fuel Advance to Vendor";
                    $transaction->date = $request->pdtldate;
                    $transaction->save();
                    $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                    $transaction->save();
                }
            }
            

            DB::commit();

            return redirect()->back()->with('success', 'Updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function programUpdate_new(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'mother_vassel_id' => 'required',
                'ghat_id' => 'required',
                'vendor_id.*' => 'required',
                'truck_number.*' => 'required',
                'challan_no.*' => 'required',
            ]);

            if ($validator->fails()) {
                $errorMessage = "<div class='alert alert-warning'><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
                return response()->json(['status' => 400, 'message' => $errorMessage]);
            }

            $program = Program::find($request->pid);
            if (!$program) {
                return response()->json(['status' => 404, 'message' => "<div class='alert alert-danger'>Program not found.</div>"]);
            }

            $program->update([
                'date' => $request->input('date'),
                'client_id' => $request->input('client_id'),
                'mother_vassel_id' => $request->input('mother_vassel_id'),
                'lighter_vassel_id' => $request->input('lighter_vassel_id'),
                'ghat_id' => $request->input('ghat_id'),
                'consignmentno' => $request->input('consignmentno'),
                'note' => $request->input('note', null),
                'created_by' => auth()->id(),
            ]);

            $fields = [
                'vendor_id', 'truck_number', 'challan_no', 'fuelqty',
                'cashamount', 'fuel_rate', 'fueltoken', 'petrol_pump_id',
                'program_detail_id', 'advancePaymentId'
            ];

            foreach ($request->input('vendor_id') as $key => $vendorId) {
                $data = [];
                foreach ($fields as $field) {
                    $data[$field] = $request->input($field)[$key] ?? null;
                }

                $programDetail = isset($data['program_detail_id']) ? ProgramDetail::find($data['program_detail_id']) : new ProgramDetail();
                if (!$programDetail) {
                    continue;
                }

                $programDetail->fill([
                    'date' => $request->input('date'),
                    'program_id' => $program->id,
                    'consignmentno' => $request->input('consignmentno'),
                    'mother_vassel_id' => $request->input('mother_vassel_id'),
                    'lighter_vassel_id' => $request->input('lighter_vassel_id'),
                    'client_id' => $request->input('client_id'),
                    'ghat_id' => $request->input('ghat_id'),
                    'vendor_id' => $vendorId,
                    'truck_number' => $data['truck_number'],
                    'challan_no' => $data['challan_no'],
                    'updated_by' => auth()->id(),
                ])->save();

                // Advance Payment
                $fuelAmnt = ($data['fuel_rate'] ?? 0) * ($data['fuelqty'] ?? 0);
                $adv = isset($data['advancePaymentId']) ? AdvancePayment::find($data['advancePaymentId']) : new AdvancePayment();
                if ($adv) {
                    $adv->fill([
                        'program_id' => $program->id,
                        'program_detail_id' => $programDetail->id,
                        'vendor_id' => $vendorId,
                        'cashamount' => $data['cashamount'],
                        'petrol_pump_id' => $data['petrol_pump_id'],
                        'fuel_rate' => $data['fuel_rate'],
                        'fuelqty' => $data['fuelqty'],
                        'fueltoken' => $data['fueltoken'],
                        'fuelamount' => $fuelAmnt,
                        'amount' => $fuelAmnt + ($data['cashamount'] ?? 0),
                        'date' => date('Y-m-d'),
                    ])->save();
                }

                // Transactions
                if ($data['cashamount'] > 0) {
                    $trans = Transaction::where('program_detail_id', $programDetail->id)
                        ->where('payment_type', 'Cash')->first() ?? new Transaction();
                    $trans->fill([
                        'client_id' => $request->input('client_id'),
                        'mother_vassel_id' => $request->input('mother_vassel_id'),
                        'program_id' => $program->id,
                        'program_detail_id' => $programDetail->id,
                        'vendor_id' => $vendorId,
                        'challan_no' => $data['challan_no'],
                        'amount' => $data['cashamount'],
                        'tran_type' => 'Advance',
                        'payment_type' => 'Cash',
                        'date' => date('Y-m-d'),
                    ])->save();
                    $trans->tran_id = 'RT' . date('ymd') . str_pad($trans->id, 4, '0', STR_PAD_LEFT);
                    $trans->save();
                }

                if ($fuelAmnt > 0) {
                    $trans = Transaction::where('program_detail_id', $programDetail->id)
                        ->where('payment_type', 'Fuel')->first() ?? new Transaction();
                    $trans->fill([
                        'client_id' => $request->input('client_id'),
                        'mother_vassel_id' => $request->input('mother_vassel_id'),
                        'program_id' => $program->id,
                        'program_detail_id' => $programDetail->id,
                        'vendor_id' => $vendorId,
                        'challan_no' => $data['challan_no'],
                        'amount' => $fuelAmnt,
                        'tran_type' => 'Advance',
                        'payment_type' => 'Fuel',
                        'date' => date('Y-m-d'),
                    ])->save();
                    $trans->tran_id = 'FT' . date('ymd') . str_pad($trans->id, 4, '0', STR_PAD_LEFT);
                    $trans->save();
                }
            }

            return response()->json([
                'status' => 300,
                'message' => "<div class='alert alert-success'><b>Data Updated Successfully.</b></div>",
            ]);

        } catch (\Exception $e) {
            Log::error('Program Update Failed: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => "<div class='alert alert-danger'><b>Error:</b> {$e->getMessage()}</div>"
            ]);
        }
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
        
        session()->put('mv_id', $request->mv_id);
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
            $ghatID = $chkprgmid->ghat_id;
            
        
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
                                <span class="btn btn-sm btn-success w-100 addrateThis" id="prgmDtlArrowKey_'.$prgmdtl->id.'" tabindex="2" data-pdtlid="'.$prgmdtl->id.'" data-adv="'.$prgmdtl->advancePayment->amount.'" data-headerid="'.$prgmdtl->headerid.'" data-destqty="'.$prgmdtl->dest_qty.'" data-linecharge="'.$prgmdtl->line_charge.'" data-scale_fee="'.$prgmdtl->scale_fee.'" data-other_cost="'.$prgmdtl->other_cost.'" data-destination_id="'.$prgmdtl->destination_id.'" data-advid="'.$prgmdtl->advancePayment->id.'" data-due="'.$prgmdtl->due.'" data-additional_cost="'.$prgmdtl->additional_cost.'" data-carrying_bill="'.$prgmdtl->carrying_bill.'" data-vendor_sequence_number_id="'.$prgmdtl->vendor_sequence_number_id.'"><i class="fas fa-arrow-right"></i></span>
                            </td>
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
                                <input type="number" class="form-control pcashamount" id="cashamount'.$prgmdtl->id.'"  value="'.$prgmdtl->advancePayment->cashamount.'" name="cashamount[]" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control pfuelqty" id="fuelqty'.$prgmdtl->id.'" name="fuelqty[]"  value="'.$prgmdtl->advancePayment->fuelqty.'">
                            </td>
                            <td>
                                <input type="number" class="form-control pfuel_rate" id="fuel_rate'.$prgmdtl->id.'" name="fuel_rate[]"  value="'.$prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td> 
                                <input type="number" class="form-control pfuel_amount" id="fuel_amount'.$prgmdtl->id.'" readonly  name="fuel_amount[]" value="'.$prgmdtl->advancePayment->fuelqty * $prgmdtl->advancePayment->fuel_rate.'">
                            </td>
                            <td>
                                <input type="number" class="form-control" id="fueltoken'.$prgmdtl->id.'" value="'.$prgmdtl->advancePayment->fueltoken.'"  name="fueltoken[]">
                            </td>
                            <td>
                                <input type="number" class="form-control pamount" id="amount'.$prgmdtl->id.'" readonly  value="'.$prgmdtl->advancePayment->amount.'" name="pamount[]">
                            </td>
                        </tr>';
            }

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan found.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$prop, 'program'=>$program, 'prgmdtls'=>$prgmdtls, 'prate'=>$prate, 'program_detail_id'=>$chkprgmid->id, 'chkprgmid' => $chkprgmid, 'ghatID' => $ghatID]);


        } else {


            $program = 'empty';
            $data = 'empty';
            $chkprgmid = 'empty';

            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Challan No not  found.</b></div>";

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data, 'program'=>$program, 'program_detail_id'=> $chkprgmid]);
        }
        
    }



    public function checkSlabRate(Request $request)
    {
        $vsno = VendorSequenceNumber::where('status', 1)->where('vendor_id', $request->vendor)->get();
        $challanqty = $request->challanqty;
        $chkrate = DestinationSlabRate::where('destination_id', $request->destid)->where('ghat_id', $request->ghat)->first();

        if ($vsno) {
            $vdata = '<option value="">Select</option>';
            foreach ($vsno as $key => $vsvalue) {

                $programCount = ProgramDetail::where('vendor_sequence_number_id', $vsvalue->id)->count();

                // if ($programCount < $vsvalue->qty) {
                    $vdata.= '<option value="'.$vsvalue->id.'">'.$vsvalue->unique_id.' ('.$vsvalue->date.') '.$vsvalue->qty.'/'.$programCount.' </option>';
                // }
                
            }
        } else {
            $vdata = '<option value="">Select</option>';
        }

        $alldata = $request->all();
        
        
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

            return response()->json(['status'=> 300,'message'=>$message, 'data'=>$chkrate, 'rate'=>$prop, 'totalAmount' => $totalAmount, 'vdata' => $vdata]);


        }else {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Slab rate not found</b></div>";
            $totalAmount = 0;
            return response()->json(['status'=> 200,'message'=>$message, 'data'=>'', 'alldata' => $alldata]);
        }
        
    }


    public function afterPostProgramStore_old(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'prgmdtlid' => 'required',
            'destid' => 'required',
            'headerid' => 'required',
            'sequence_id' => 'required',
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

        if ($request->fuelqty) {
            $tran = Transaction::where('advance_payment_id',$request->advPmtid)->where('payment_type','=','Fuel')->first();
            if (isset($tran)) {
                $tran->vendor_id = $request->vendor_id;
                $tran->amount = $request->amount;
                $tran->save();
            }else{
                $transaction = new Transaction();
                $transaction->client_id = $prgm->client_id;
                $transaction->mother_vassel_id = $prgm->mother_vassel_id;
                $transaction->lighter_vassel_id = $prgm->lighter_vassel_id;
                $transaction->advance_payment_id = $request->advPmtid;
                $transaction->program_id = $prgm->id;
                $transaction->program_detail_id = $prgmdtl->id;
                $transaction->vendor_id = $request->vendor_id;
                $transaction->challan_no = $prgmdtl->challan_no;
                $transaction->amount = $request->fuelqty * $request->fuel_rate;
                $transaction->tran_type = "Advance";
                $transaction->payment_type = "Fuel";
                $transaction->date = date('Y-m-d');
                $transaction->save();
                $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();
            }
        }

        if ($request->sequence_id) {
            $vsequence = VendorSequenceNumber::find($request->sequence_id);
            $vsequence->markqty = $vsequence->markqty + 1;
            $vsequence->notmarkqty = $vsequence->notmarkqty - 1;
            $vsequence->save();
        }
        
        $progrm = ProgramDetail::find($request->prgmdtlid);

        //importent
        // Delete old challan rates if destination or ghat has changed
        if (
            $progrm->destination_id != $request->destid ||
            $progrm->ghat_id != $request->ghat_id
        ) {
            ChallanRate::where('challan_no', $progrm->challan_no)
                        ->where('program_detail_id', $progrm->id)
                        ->delete();
        }

        if (
            $progrm->dest_qty != $request->totalqtyasperchallan
        ) {
            ChallanRate::where('challan_no', $progrm->challan_no)
                        ->where('program_detail_id', $progrm->id)
                        ->delete();
        }
        //importent

        $progrm->after_date = date('Y-m-d');
        $progrm->vendor_sequence_number_id = $request->sequence_id;
        $progrm->destination_id = $request->destid;
        $progrm->ghat_id = $request->ghat_id;
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


    public function afterPostProgramStore(Request $request)
    {
      
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'prgmdtlid' => 'required|integer',
            'destid' => 'required|integer',
            'headerid' => 'required|integer',
            'sequence_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'>
                                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                <b>" . implode("<br>", $validator->errors()->all()) . "</b>
                            </div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        // Step 2: Fetch related program data
        $programDetail = ProgramDetail::findOrFail($request->prgmdtlid);
        $program = Program::findOrFail($programDetail->program_id);

        // Step 3: Update Advance Payment
        $advancePayment = AdvancePayment::findOrFail($request->advPmtid);
        $advancePayment->fill([
            'vendor_id'   => $request->vendor_id,
            'fuelqty'     => $request->fuelqty,
            'fuel_rate'   => $request->fuel_rate,
            'fueltoken'   => $request->fueltoken,
            'fuelamount'  => $request->fuelqty * $request->fuel_rate,
        ]);
        $advancePayment->amount = $advancePayment->fuelamount + $advancePayment->cashamount;
        $advancePayment->save();

        // Step 4: Create or update Fuel Transaction
        if ($request->fuelqty) {
            $transaction = Transaction::firstOrNew([
                'advance_payment_id' => $request->advPmtid,
                'payment_type'       => 'Fuel'
            ]);

            $transaction->fill([
                'client_id'          => $program->client_id,
                'mother_vassel_id'   => $program->mother_vassel_id,
                'lighter_vassel_id'  => $program->lighter_vassel_id,
                'program_id'         => $program->id,
                'program_detail_id'  => $programDetail->id,
                'vendor_id'          => $request->vendor_id,
                'challan_no'         => $programDetail->challan_no,
                'amount'             => $request->fuelqty * $request->fuel_rate,
                'tran_type'          => 'Advance',
                'date'               => now()->format('Y-m-d'),
            ]);

            $transaction->save();

            if (!$transaction->tran_id) {
                $transaction->tran_id = 'FA' . now()->format('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();
            }
        }

        // Step 5: Update Vendor Sequence
        if ($request->sequence_id) {
            $sequence = VendorSequenceNumber::find($request->sequence_id);
            if ($sequence) {
                $sequence->increment('markqty');
                $sequence->decrement('notmarkqty');
            }
        }

        // Step 6: Update ProgramDetail with new values
        $programDetail->fill([
            'after_date'                => now()->format('Y-m-d'),
            'vendor_sequence_number_id'=> $request->sequence_id,
            'destination_id'           => $request->destid,
            'ghat_id'                  => $request->ghat_id,
            'vendor_id'                => $request->vendor_id,
            'truck_number'             => $request->truck_number,
            'headerid'                 => $request->headerid,
            'dest_qty'                 => $request->totalqtyasperchallan,
            'line_charge'              => $request->line_charge,
            'scale_fee'                => $request->scale_fee,
            'other_cost'               => $request->other_cost,
            'transportcost'            => $request->totalamount,
            'carrying_bill'            => $request->totalamount,
            'additional_cost'          => $request->additionalCost,
            'advance'                  => $advancePayment->amount,
            'due'                      => ($request->totalamount + $request->additionalCost) - $advancePayment->amount,
            'rate_status'              => 0,
            'updated_by'             => Auth::user()->id,
        ]);

        // Check for destination or quantity change
        if (
            $programDetail->destination_id != $request->destid ||
            $programDetail->ghat_id != $request->ghat_id ||
            $programDetail->dest_qty != $request->totalqtyasperchallan
        ) {}

        $dstnID = $request->destid;
        $ghatID = $request->ghat_id;
        $cQty = $request->totalqtyasperchallan;

        ChallanRate::where('challan_no', $programDetail->challan_no)
                    ->where('program_detail_id', $programDetail->id)
                    ->delete();

        $programDetail->save();

        // Step 7: Create or update ChallanRates
        
        $this->challanRateUpdate($cQty, $dstnID, $ghatID, $programDetail);

        // Step 8: Return success response
        $message = "<div class='alert alert-success'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Challan completed.</b>
                    </div>";

        return response()->json([
            'status'  => 300,
            'message' => $message,
            'programDetail' => $programDetail,
            'data'    => $request->all()
        ]);
    }

    public function challanRateUpdate($cQty, $dstnID, $ghatID, $programDetail)
    {

        $chkrate = DestinationSlabRate::where('destination_id', $dstnID)
            ->where('ghat_id', $ghatID)
            ->first();

            $totalAmount = 0;
            if ($cQty > $chkrate->maxqty) {
                
                $aboveqty = $cQty - $chkrate->maxqty;
                $totalAmount = $totalAmount + $chkrate->above_rate_per_qty * $aboveqty + $chkrate->below_rate_per_qty * $chkrate->maxqty;

                $challanBelowRate = new ChallanRate();
                $challanBelowRate->fill([
                    'program_detail_id' => $programDetail->id,
                    'challan_no'        => $programDetail->challan_no,
                    'qty'               => $chkrate->maxqty,
                    'rate_per_unit'     => $chkrate->below_rate_per_qty,
                    'total'             => $chkrate->below_rate_per_qty * $chkrate->maxqty,
                    'created_by'        => Auth::id(),
                ])->save();

                $challanAboveRate = new ChallanRate();
                $challanAboveRate->fill([
                    'program_detail_id' => $programDetail->id,
                    'challan_no'        => $programDetail->challan_no,
                    'qty'               => $aboveqty,
                    'rate_per_unit'     => $chkrate->above_rate_per_qty,
                    'total'             => $chkrate->above_rate_per_qty * $aboveqty,
                    'created_by'        => Auth::id(),
                ])->save();

            } else {

                $challanBelowRate = new ChallanRate();
                $challanBelowRate->fill([
                    'program_detail_id' => $programDetail->id,
                    'challan_no'        => $programDetail->challan_no,
                    'qty'               => $cQty,
                    'rate_per_unit'     => $chkrate->below_rate_per_qty,
                    'total'             => $chkrate->below_rate_per_qty * $cQty,
                    'created_by'        => Auth::id(),
                ])->save();
            }

    }



    public function changeProgramFuelRate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'fuel_rate' => 'required',
            'fuel_bill_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $prgmdtls = ProgramDetail::where('fuel_bill_id', $request->fuel_bill_id)->get();
        $data = $request->all();

        foreach ($prgmdtls as $key => $prgmdtl) {


            $fadv = AdvancePayment::find($prgmdtl->advancePayment->id);
            $fadv->fuel_rate = $request->fuel_rate;
            $fadv->fuelamount = $fadv->fuelqty * $request->fuel_rate;
            $fadv->amount = $fadv->fuelamount + $fadv->cashamount;
            $fadv->save();

            $progrm = ProgramDetail::find($prgmdtl->id);
            $progrm->advance = $fadv->amount; 
            $progrm->due = $progrm->carrying_bill + $progrm->additional_cost - $fadv->amount; 
            $progrm->save();

            if ($request->fuel_rate) {
                $tran = Transaction::where('advance_payment_id', $fadv->id)->where('payment_type','=','Fuel')->first();
                if (isset($tran)) {
                    $tran->amount = $fadv->fuelamount; 
                    $tran->save();
                }
            }


        }

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Fuel rate change for this sequence id.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data]);
        
        
    }


    public function singleProgramdetailUpdate(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'program_detail_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $prgmdtl = ProgramDetail::where('id', $request->prgmdtlid)->first();
        $prgm = Program::where('id', $prgmdtl->program_id)->first();

        
        $data = $request->all();

        $fadv = AdvancePayment::find($request->advPmtid);
        $fadv->vendor_id = $request->vendor_id;
        $fadv->fuelqty = $request->fuelqty;
        $fadv->fuel_rate = $request->fuel_rate;
        $fadv->fueltoken = $request->fueltoken;
        $fadv->fuelamount = $request->fuelqty * $request->fuel_rate;
        $fadv->amount = $fadv->fuelamount + $fadv->cashamount;
        $fadv->save();

        if ($request->fuelqty) {
            $tran = Transaction::where('advance_payment_id',$request->advPmtid)->where('payment_type','=','Fuel')->first();
            if (isset($tran)) {
                $tran->vendor_id = $request->vendor_id;
                $tran->amount = $request->amount;
                $tran->save();
            }else{
                $transaction = new Transaction();
                $transaction->client_id = $prgm->client_id;
                $transaction->mother_vassel_id = $prgm->mother_vassel_id;
                $transaction->lighter_vassel_id = $prgm->lighter_vassel_id;
                $transaction->advance_payment_id = $request->advPmtid;
                $transaction->program_id = $prgm->id;
                $transaction->program_detail_id = $prgmdtl->id;
                $transaction->vendor_id = $request->vendor_id;
                $transaction->challan_no = $prgmdtl->challan_no;
                $transaction->amount = $request->fuelqty * $request->fuel_rate;
                $transaction->tran_type = "Advance";
                $transaction->payment_type = "Fuel";
                $transaction->description = "Fuel Advance to Vendor";
                $transaction->date = date('Y-m-d');
                $transaction->save();
                $transaction->tran_id = 'FA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
                $transaction->save();
            }
        }

        $progrm = ProgramDetail::find($request->prgmdtlid);
        $progrm->ghat_id = $prgm->ghat_id;
        $progrm->truck_number = $request->truck_number; 
        $progrm->challan_no = $prgmdtl->challan_no;
        $progrm->additional_cost = $request->additionalCost;
        $progrm->advance = $fadv->amount; 
        $progrm->rate_status = 0;
        $progrm->save();

            

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data updated.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data]);
        
        
    }


    public function singleProgramdetailUpdate_old(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'program_detail_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = "<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>" . implode("<br>", $validator->errors()->all()) . "</b></div>";
            return response()->json(['status' => 400, 'message' => $errorMessage]);
        }

        $progrm = ProgramDetail::find($request->program_detail_id);
        $progrm->lighter_vassel_id = $request->lighter_vassel_id;
        $progrm->ghat_id = $request->ghat_id;
        $progrm->save();




        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data updated</b></div>";

        return response()->json(['status'=> 300,'message'=>$message, 'data'=>$data]);
        
        
    }

    public function programDetailLogs()
    {
        $todayCarbon = Carbon::today();
        $yesterdayCarbon = Carbon::yesterday();

        $getLogsGrouped = function ($date, $filterDeleted = false) {
            $query = Activity::where('log_name', 'program_detail')
                ->whereDate('created_at', $date)
                ->with([
                    'causer:id,name',
                    'subject:id,headerid,dest_qty,challan_no'
                ]);

            if ($filterDeleted) {
                $query->where('event', 'deleted');
            }

            return $query->get()
                ->groupBy('causer_id')
                ->map(function ($group) {
                    return $group->map(function ($log) {
                        $headerid = $log->subject->headerid ?? 
                                  ($log->properties['attributes']['headerid'] ?? '-');
                        $dest_qty = $log->subject->dest_qty ?? 
                                  ($log->properties['attributes']['dest_qty'] ?? '-');
                        $challan_no = $log->subject->challan_no ?? 
                                    ($log->properties['attributes']['challan_no'] ?? '-');

                        return [
                            'headerid' => $headerid,
                            'dest_qty' => $dest_qty,
                            'challan_no' => $challan_no,
                            'causer_name' => $log->causer?->name ?? 'Unknown',
                        ];
                    });
                });
        };

        $todayLogs = $getLogsGrouped($todayCarbon);
        $yesterdayLogs = $getLogsGrouped($yesterdayCarbon);

        $todayDeletedLogs = $getLogsGrouped($todayCarbon, true);
        $yesterdayDeletedLogs = $getLogsGrouped($yesterdayCarbon, true);

        $today = $todayCarbon->format('Y-m-d');
        $yesterday = $yesterdayCarbon->format('Y-m-d');

        $vsnumbersToday = VendorSequenceNumber::with(['programDetail'])
            ->whereDate('date', $today)->orderBy('id', 'DESC')->get();

        $vsnumbersYesterday = VendorSequenceNumber::with(['programDetail'])
            ->whereDate('date', $yesterdayCarbon)->orderBy('id', 'DESC')->get();

        // dd($vsnumbersYesterday);

        return view('admin.programs.program_detail_logs', compact('todayLogs', 'yesterdayLogs', 'todayDeletedLogs', 'yesterdayDeletedLogs', 'vsnumbersToday', 'vsnumbersYesterday'));
    }

    public function deletedProgramDetail($id)
    {
        $program = Program::with(['programDetail' => function ($query) {
            $query->onlyTrashed()->with('deleteLogs');
        }])->findOrFail($id);

        $program->programDetail->each(function ($detail) {
            $detail->deleteLogs->load('causer');
        });

        $deletedDetails = $program->programDetail;

        return view('admin.program_details.deleted', compact('deletedDetails'));
    }

    public function undoFuelBill($id)
    {
        $programDetail = ProgramDetail::findOrFail($id);
        $programDetail->fuel_bill_id = null;
        $programDetail->save();

        return back()->with('success', 'Fuel bill unassigned successfully.');
    }


}
