<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramDetail;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index()
    {
        if (!(in_array('8', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $data = Vendor::orderby('id','DESC')->get();
        return view('admin.vendor.index', compact('data'));
    }

    public function vendorlist()
    {
        $data = Vendor::orderby('id','DESC')->get();
        return response()->json(['status' => 200, 'vendors' => $data]);
    }

    public function store(Request $request)
    {
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Name \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $chkname = Vendor::where('name',$request->name)->first();
        if($chkname){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        $data = new Vendor;
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->company = $request->company;
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
        $info = Vendor::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {

        
        if(empty($request->name)){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill \"Username \" field..!</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $duplicatename = Vendor::where('name',$request->name)->where('id','!=', $request->codeid)->first();
        if($duplicatename){
            $message ="<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>This name already added.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }


        $data = Vendor::find($request->codeid);
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->address = $request->address;
        $data->company = $request->company;
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

        if(Vendor::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    public function generateUniqueCode($vendorName)
    {
        $words = explode(' ', $vendorName);
        $firstLetters = array_map(fn($word) => strtoupper($word[0]), $words);
        $code = implode('', $firstLetters);
        $uniqueCode = $code;

        return $uniqueCode;
    }

    public function addSequenceNumber(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
            'challanqty' => 'required',
        ]);

        $vendor = Vendor::where('id', $request->vendorId)->first();


        $vendorName = $vendor->name;
        $uniqueCode = $this->generateUniqueCode($vendorName);

        $data = new VendorSequenceNumber();
        $data->vendor_id = $request->vendorId;
        $data->qty = $request->challanqty;
        $data->notmarkqty = $request->challanqty;
        $lastSequence = VendorSequenceNumber::where('vendor_id', $request->vendorId)->where('created_at', 'like', date('Y'.'%'))->max('sequence');
        $data->sequence = $lastSequence ? $lastSequence + 1 : 1;
        $data->unique_id = $uniqueCode."_".$data->sequence."_".date('Y');
        $data->date = date('Y-m-d');
        $data->created_by = Auth::user()->id;
        $data->save();

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Data store Successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }


    public function getSequenceNumber(Request $request)
    {
        
        $vendor = Vendor::where('id', $request->vendorId)->first();

        $data = VendorSequenceNumber::where('vendor_id',$request->vendorId)->get();
        
        $prop = '';
        
            foreach ($data as $tran){


                // <!-- Single Property Start -->
                $prop.= '<tr>
                            <td>
                                '.$tran->date.'
                            </td>
                            <td>
                                '.$tran->qty.'
                            </td>
                            <td>
                                '.$tran->markqty.'
                            </td>
                            <td>
                                '.$tran->sequence.'
                            </td>
                            <td>
                            <a class="btn btn-success btn-xs" href="'.route('admin.vendor.sequence.show', $tran->id).'">'.$tran->unique_id.'</a>
                            </td>
                            <td>
                                <span id="seqDeleteBtn" rid="'.$tran->id.'" class="btn btn-warning btn-xs seqDeleteBtn" style="cursor:pointer">Delete</span>
                            </td>
                        </tr>';
                        
            }

        return response()->json(['status'=> 300,'data'=>$prop, 'vendor'=>$vendor]);
    }


    public function sequencedelete($id)
    {
        if(VendorSequenceNumber::destroy($id)){
            return response()->json(['success'=>true,'message'=>'Data has been deleted successfully']);
        }else{
            return response()->json(['success'=>false,'message'=>'Delete Failed']);
        }
    }

    
    
    public function getVendorListByClientId($id)
    {
        $vendors = Vendor::whereHas('programDetail', function($query) use ($id) {
            $query->where('mother_vassel_id', $id);
        })->get();

        return response()->json(['status' => 300, 'vendors' => $vendors]);
    }

    // getVendorWiseProgramList
    public function getVendorWiseProgramList($id)
    {
        $vendorSequenceNumber = VendorSequenceNumber::where('id', $id)->first();
        // dd($vendorSequenceNumber);
        $vendor = Vendor::where('id', $vendorSequenceNumber->vendor_id)->first();
        $data = ProgramDetail::where('vendor_sequence_number_id', $id)->get();
        
        return view('admin.vendor.vendor_wise_program_list', compact('data','vendor','vendorSequenceNumber'));
    }
}
