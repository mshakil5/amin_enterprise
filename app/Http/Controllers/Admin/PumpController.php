<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelBill;
use Illuminate\Http\Request;
use App\Models\PetrolPump;
use Illuminate\Support\Facades\Auth;

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

        $lastSequence = FuelBill::where('petrol_pump_id', $request->pumpId)->max('sequence');
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
                                '.$tran->unique_id.'
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
    
            return redirect()->back()->with('success', 'Fuel bill updated successfully!');
        }

        return redirect()->back()->with('error', 'No record found for the given petrol pump and unique ID.');
    }

}
