<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelBill;
use App\Models\MotherVassel;
use Illuminate\Http\Request;
use App\Models\PetrolPump;
use Illuminate\Support\Facades\Auth;
use App\Models\ProgramDetail;

class PumpController extends Controller
{
    public function index()
    {
        if (!(in_array('7', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $data = PetrolPump::orderby('id','DESC')->get();
        return view('admin.pump.index', compact('data'));
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

    public function getFuelBillNumber(Request $request)
    {
        
        $pump = PetrolPump::where('id', $request->pumpId)->first();

        $data = FuelBill::where('petrol_pump_id',$request->pumpId)->orderby('id', 'DESC')->get();
        
        $prop = '';
        
            foreach ($data as $tran){


                // <!-- Single Property Start -->
                $prop.= '<tr>
                            <td>
                                '.$tran->date.'
                            </td>
                            <td>
                                '.$tran->bill_number.'
                            </td>
                            <td>
                                '.$tran->qty.'
                            </td>
                            <td>
                                '.$tran->vehicle_count.'
                            </td>
                            <td>
                            <a class="btn btn-success btn-xs" href="'.route('admin.pump.sequence.show', $tran->id).'">'.$tran->unique_id.'</a>
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

        return response()->json(['status'=> 300,'data'=>$prop, 'pump'=>$pump]);
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

    

        return view('admin.pump.fuelbill_wise_program_list', compact('data','pump','pumpSequenceNumber'));
    }

    public function pumpUpdate(Request $request)
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

}
