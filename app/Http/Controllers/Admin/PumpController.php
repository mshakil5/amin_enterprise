<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelBill;
use App\Models\MotherVassel;
use Illuminate\Http\Request;
use App\Models\PetrolPump;
use Illuminate\Support\Facades\Auth;
use App\Models\ProgramDetail;
use App\Models\Transaction;
use App\Models\Vendor;

class PumpController extends Controller
{
    public function index()
    {
        if (!(in_array('7', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $pumps = PetrolPump::orderby('id', 'DESC')->get();
        $pumpIds = $pumps->pluck('id');

        // Get all fuel bills for these pumps (eager load programDetails)
        $fuelBills = FuelBill::whereIn('petrol_pump_id', $pumpIds)
            ->with(['programDetails.advancePayment'])
            ->orderBy('id', 'DESC')
            ->get();

        // Build summary stats
        $totalPumps       = $pumps->count();
        $totalFuelBills   = $fuelBills->count();
        $totalInvoiceQty  = $fuelBills->sum('qty');
        $totalMarkQty     = $fuelBills->sum('markqty');
        $totalNotMarkQty  = $fuelBills->sum('notmarkqty');

        // Total due calculation across all pumps
        $totalCarrying = 0;
        $totalScale    = 0;
        $totalCash     = 0;
        $totalFuelAmt  = 0;

        foreach ($fuelBills as $bill) {
            foreach ($bill->programDetails as $pd) {
                $totalCarrying += $pd->carrying_bill ?? 0;
                $totalScale    += $pd->scale_fee ?? 0;
                $totalCash     += $pd->advancePayment->cashamount ?? 0;
                $totalFuelAmt  += $pd->advancePayment->fuelamount ?? 0;
            }
        }

        $grandDue = $totalCarrying + $totalScale - $totalCash - $totalFuelAmt;

        // Per-pump stats map (used in table)
        $pumpStats = [];
        foreach ($pumps as $p) {
            $bills = $fuelBills->where('petrol_pump_id', $p->id);

            $carrying = 0; $scale = 0; $cash = 0; $fuelAmt = 0;
            foreach ($bills as $bill) {
                foreach ($bill->programDetails as $pd) {
                    $carrying += $pd->carrying_bill ?? 0;
                    $scale    += $pd->scale_fee ?? 0;
                    $cash     += $pd->advancePayment->cashamount ?? 0;
                    $fuelAmt  += $pd->advancePayment->fuelamount ?? 0;
                }
            }
            $pumpDue = $carrying + $scale - $cash - $fuelAmt;

            $pumpStats[$p->id] = [
                'bill_count'  => $bills->count(),
                'invoice_qty' => $bills->sum('qty'),
                'mark_qty'    => $bills->sum('markqty'),
                'notmark_qty' => $bills->sum('notmarkqty'),
                'due'         => $pumpDue,
                'last_bill'   => $bills->first()?->date,
            ];
        }
        $data = $pumps; // keep using $data in blade
        return view('admin.pump.index', compact(
            'data',
            'totalPumps',
            'totalFuelBills',
            'totalInvoiceQty',
            'totalMarkQty',
            'totalNotMarkQty',
            'grandDue'
        ))->with('pumpStats', $pumpStats);
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $chkname = PetrolPump::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $data = new PetrolPump;
        $data->name = $request->name;
        $data->location = $request->location;
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
        $info = PetrolPump::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = PetrolPump::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $data = PetrolPump::find($request->codeid);
        $data->name = $request->name;
        $data->location = $request->location;
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

        if(PetrolPump::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function generateUniqueCode($petpumpName)
    {
        $words = explode(' ', $petpumpName);
        $firstLetters = array_map(fn($word) => strtoupper($word[0]), $words);
        $code = implode('', $firstLetters);
        $uniqueCode = $code;

        return $uniqueCode;
    }

    public function addFuelBillNumber(Request $request)
    {
        $request->validate([
            'pumpId' => 'required',
            'bill_number' => 'required',
            'invqty' => 'required',
            'vehicle_count' => 'required',
        ]);

        $petpump = PetrolPump::where('id', $request->pumpId)->first();

        $chkBillNumber = FuelBill::where('petrol_pump_id', $request->pumpId)->where('bill_number',$request->bill_number)->count();

        if ($chkBillNumber > 0) {
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This bill number has already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
        }


        $petpumpName = $petpump->name;
        $uniqueCode = $this->generateUniqueCode($petpumpName);

        $data = new FuelBill();
        $data->petrol_pump_id = $request->pumpId;
        $data->qty = $request->invqty;
        $data->notmarkqty = $request->invqty;
        $data->bill_number = $request->bill_number;
        $data->vehicle_count = $request->vehicle_count;

        $lastSequence = FuelBill::where('petrol_pump_id', $request->pumpId)->where('created_at', 'like', date('Y'.'%'))->max('sequence');
        $data->sequence = $lastSequence ? $lastSequence + 1 : 1;
        $data->unique_id = $uniqueCode."_".$data->sequence."_".date('Y');

        $data->date = $request->date;
        $data->created_by = Auth::user()->id;
        $data->save();

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data store Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function getFuelBillNumber2(Request $request)
    {
        $pump = PetrolPump::where('id', $request->pumpId)->first();
        $data = FuelBill::where('petrol_pump_id', $request->pumpId)->orderby('id', 'DESC')->get();
        
        $prop = '';
        
        foreach ($data as $tran) {
            $pdtls = ProgramDetail::where('fuel_bill_id', $tran->id)->get();
            
            $totalCarryingBill = $pdtls->sum('carrying_bill');
            $totalScaleFee = $pdtls->sum('scale_fee');
            $totalCashAmount = $pdtls->sum(function($item) {
                return $item->advancePayment->cashamount ?? 0;
            });
            $totalFuelAmount = $pdtls->sum(function($item) {
                return $item->advancePayment->fuelamount ?? 0;
            });
            
            $balance = $totalCarryingBill + $totalScaleFee - $totalCashAmount - $totalFuelAmount;

            $formattedBalance = number_format($balance, 2);

            $tripCount = $pdtls->count();

            $prop .= '<tr>
                        <td>
                            ' . $tran->date . '
                        </td>
                        <td>
                            ' . $tran->bill_number . '
                        </td>
                        <td>
                            ' . $tran->qty . '
                        </td>
                        <td>
                            ' . $tripCount . '
                        </td>
                        <td>
                            ' . $formattedBalance . '
                        </td>
                        <td>
                            <a class="btn btn-success btn-xs" href="' . route('admin.pump.sequence.show', $tran->id) . '">' . $tran->unique_id . '</a>
                        </td>
                        <td>
                            <button class="btn btn-info btn-xs editFullBtn" 
                                    data-id="' . $tran->id . '" 
                                    data-date="' . $tran->date . '"
                                    data-bill_number="' . $tran->bill_number . '"
                                    data-qty="' . $tran->qty . '" 
                                    data-vehicle_count="' . $tran->vehicle_count . '" 
                                    data-toggle="modal" 
                                    data-target="#editFullModal">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>';
        }

        return response()->json(['status' => 300, 'data' => $prop, 'pump' => $pump]);
    }

    public function getFuelBillNumber(Request $request)
    {
        $pump = PetrolPump::where('id', $request->pumpId)->first();
        
        // Eager load all relationships to prevent N+1 queries
        $data = FuelBill::where('petrol_pump_id', $request->pumpId)
            ->orderby('id', 'DESC')
            ->with(['programDetails.advancePayment'])
            ->get();
        
        $prop = '';
        
        foreach ($data as $tran) {
            // Now using eager loaded data - no extra queries
            $pdtls = $tran->programDetails;
            
            $totalCarryingBill = $pdtls->sum('carrying_bill');
            $totalScaleFee = $pdtls->sum('scale_fee');
            $totalCashAmount = $pdtls->sum(function($item) {
                return $item->advancePayment ? $item->advancePayment->cashamount : 0;
            });
            $totalFuelAmount = $pdtls->sum(function($item) {
                return $item->advancePayment ? $item->advancePayment->fuelamount : 0;
            });
            
            $balance = $totalCarryingBill + $totalScaleFee - $totalCashAmount - $totalFuelAmount;
            $formattedBalance = number_format($balance, 2);
            $tripCount = $pdtls->count();

            $prop .= '<tr>
                        <td>' . $tran->date . '</td>
                        <td>' . $tran->bill_number . '</td>
                        <td>' . $tripCount . '</td>
                        <td>' . $tran->qty . '</td>
                        <td>' . $totalFuelAmount . '</td>
                        <td>
                            <a class="btn btn-success btn-xs" href="' . route('admin.pump.sequence.show', $tran->id) . '">' . $tran->unique_id . '</a>
                        </td>
                        <td>
                            <button class="btn btn-info btn-xs editFullBtn" 
                                    data-id="' . $tran->id . '" 
                                    data-date="' . $tran->date . '"
                                    data-bill_number="' . $tran->bill_number . '"
                                    data-qty="' . $tran->qty . '" 
                                    data-vehicle_count="' . $tran->vehicle_count . '" 
                                    data-toggle="modal" 
                                    data-target="#editFullModal">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>';
        }

        return response()->json(['status' => 300, 'data' => $prop, 'pump' => $pump]);
    }

    public function updateMarkQty(Request $request)
    {
        $validated = $request->validate([
            'petrol_pump_id' => 'required|integer',
            'total_qty' => 'required|numeric',
            'unique_id' => 'required',
            'program_detail_ids' => 'required|json',
        ]);

        $fuelBil = FuelBill::where('petrol_pump_id', $validated['petrol_pump_id'])
                          ->where('unique_id', $validated['unique_id'])
                          ->first();

          if ($fuelBil) {
            $oldMarkQty = $fuelBil->markqty;
    
            $qtyDifference = $validated['total_qty'] - $oldMarkQty;
    
            $fuelBil->markqty = $validated['total_qty'];
            $fuelBil->notmarkqty -= $qtyDifference;
    
            $fuelBil->save();

            $programDetailIds = json_decode($validated['program_detail_ids'], true);

            foreach ($programDetailIds as $id) {
                ProgramDetail::where('id', $id)->update([
                    'fuel_bill_id' => $fuelBil->id
                ]);
            }
    
            return redirect()->back()->with('success', 'Fuel bill updated successfully!');
        }

        return redirect()->back()->with('error', 'No record found for the given petrol pump and unique ID.');
    }

    // getVendorWiseProgramList
    public function getPumpWiseProgramList($id)
    {
        $pumpSequenceNumber = FuelBill::where('id', $id)->first();
        $pump = PetrolPump::where('id', $pumpSequenceNumber->petrol_pump_id)->first();
        $pdtls = ProgramDetail::where('fuel_bill_id', $id)->get();

        $motherVasselIds = $pdtls->pluck('mother_vassel_id')->unique()->filter()->toArray();
        
        $motherVassels = MotherVassel::whereIn('id', $motherVasselIds)->get();

        // Group ProgramDetails by mother_vassel_id
        $data = ProgramDetail::where('fuel_bill_id', $id)
            ->get()
            ->groupBy(function ($item) use ($motherVassels) {
            $motherVassel = $motherVassels->where('id', $item->mother_vassel_id)->first();
            return $motherVassel ? $motherVassel->name : 'Unknown';
            });

        $allTrips = $pdtls;

        
        // Calculate totals in controller
        $totals = [
            'total_carrying_bill' => $allTrips->sum('carrying_bill'),
            'total_scale_fee' => $allTrips->sum('scale_fee'),
            'total_cash_amount' => $allTrips->sum(function($item) {
                return $item->advancePayment->cashamount ?? 0;
            }),
            'total_fuel_amount' => $allTrips->sum(function($item) {
                return $item->advancePayment->fuelamount ?? 0;
            }),
        ];
        
        // Calculate total due
        $totals['total_due'] = $totals['total_carrying_bill'] + $totals['total_scale_fee'] 
            - $totals['total_cash_amount'] - $totals['total_fuel_amount'];
        
        $totals['total_due'] = round($totals['total_due'], 2);
        if ($totals['total_due'] === -0.0) {
            $totals['total_due'] = 0.0;
        }
        
        $totals['label'] = $totals['total_due'] >= 0 ? 'Petrol Pump Payable' : 'Petrol Pump Receivable';

        return view('admin.pump.fuelbill_wise_program_list', compact(
            'data',
            'pump',
            'pumpSequenceNumber',
            'allTrips',
            'totals'
        ));
    }

    public function fuelBillUpdate(Request $request)
    {
        $request->validate([
            'tran_id' => 'required|exists:fuel_bills,id',
            'date' => 'required|date',
            'bill_number' => 'required|string',
            'qty' => 'required|numeric',
            'vehicle_count' => 'required|numeric',
        ]);

        $bill = FuelBill::find($request->tran_id);
        $bill->date = $request->date;
        $bill->bill_number = $request->bill_number;
        $bill->qty = $request->qty;
        $bill->vehicle_count = $request->vehicle_count;
        $bill->save();

        return response()->json(['status' => 200, 'message' => 'Bill updated successfully']);
    }


    public function getFuelBills($pump_id)
    {
        $fuelBills = FuelBill::where('petrol_pump_id', $pump_id)->get();
        
        return response()->json($fuelBills);
    }

    

    public function showLedger(Request $request, $id)
    {
        $pump = PetrolPump::findOrFail($id);

        // Date range filter
        $startDate = $request->filled('start_date') ? $request->start_date : '2025-07-20';
        $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();

        // 1. Fetch Fuel Bills (Debit side)
        $fuelBills = FuelBill::where('petrol_pump_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['programDetails.advancePayment'])
            ->get();

        // 2. Fetch Transactions (Credit side)
        $transactions = Transaction::where('petrol_pump_id', $id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // 3. Merge into a single unified collection
        $ledgerData = collect();

        foreach ($fuelBills as $bill) {
            $ledgerData->push([
                'date' => $bill->date,
                'description' => "Fuel Bill #" . $bill->bill_number,
                'ref' => $bill->unique_id,
                'qty' => $bill->total_fuel_qty,
                'debit' => $bill->total_fuel_amount,
                'credit' => 0,
            ]);
        }

        foreach ($transactions as $tran) {
            $ledgerData->push([
                'date' => $tran->date,
                'description' => $tran->description ?? 'Payment',
                'ref' => $tran->tran_id,
                'qty' => 0,
                'debit' => 0,
                'credit' => $tran->at_amount,
            ]);
        }

        // Sort the unified collection by date DESC
        $ledgerData = $ledgerData->sortByDesc('date')->values();

        // Calculate totals
        $totalDebit = $ledgerData->sum('debit');
        $totalCredit = $ledgerData->sum('credit');
        $totalQty = $ledgerData->sum('qty');
        $balance = $totalDebit - $totalCredit;

        return view('admin.pump.ledger', compact(
            'pump', 
            'ledgerData', 
            'totalDebit', 
            'totalCredit', 
            'totalQty', 
            'balance', 
            'startDate', 
            'endDate'
        ));
    }

}
