<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotherVassel;
use App\Models\ProgramDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exports\VendorTripExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

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

                $programCount = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->count();
                $totalCarringCost = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('carrying_bill');

                $totalAdvance = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('advance');

                $programDetails = ProgramDetail::with('advancePayment')
                    ->where('vendor_sequence_number_id', $tran->id)
                    ->get();

                $totalCash = $programDetails->sum(function ($pd) {
                    return $pd->advancePayment->cashamount ?? 0;
                });

                $totalFuel = $programDetails->sum(function ($pd) {
                    return $pd->advancePayment->fuelamount ?? 0;
                });

                $totalDue = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('due');
                $totalScaleFee = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('scale_fee');
                $totalLineCharge = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('line_charge');
                $totalOtherCost = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('other_cost');
                $totalTransportCost = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('transportcost');
                $totalCarryingBill = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('carrying_bill');
                $totalAdditionalCost = ProgramDetail::where('vendor_sequence_number_id', $tran->id)->sum('additional_cost');

                $balance = number_format($totalCarringCost + $totalScaleFee - ($totalCash + $totalFuel), 2);





                // <!-- Single Property Start -->
                $prop.= '<tr>
                            <td>
                                '.$tran->date.'
                            </td>
                            <td>
                                '.$tran->qty.'
                            </td>
                            <td>
                                '.$programCount.'
                            </td>
                            <td>
                                '.$tran->sequence.'
                            </td>
                            <td>
                                '.$balance.'
                            </td>
                            <td>
                            <a class="btn btn-success btn-xs" href="'.route('admin.vendor.sequence.show', $tran->id).'">'.$tran->unique_id.'</a>
                            </td>
                            <td>
                                <span id="seqDeleteBtn" rid="'.$tran->id.'" class="btn btn-warning btn-xs seqDeleteBtn d-none" style="cursor:pointer">Delete</span>
                            </td>
                            <td>
                                <label class="form-checkbox  grid layout">';

                                    if($tran->checked == 1){
                                       $prop.=  '<input type="checkbox" name="checkbox-checked" class="custom-checkbox" data-vsid="'.$tran->id.'" checked disabled/>';
                                    }else{
                                       $prop.=  '<input type="checkbox" name="checkbox-checked" class="custom-checkbox checkedBtn" data-vsid="'.$tran->id.'"/>';
                                    }

                        $prop.= '</label>
                            </td>
                            <td>
                                <label class="form-checkbox  grid layout">';
                                
                                    if($tran->approved == 1){
                                       $prop.=  '<input type="checkbox" name="checkbox-checked" class="custom-checkbox" data-vsid="'.$tran->id.'" checked disabled/>';
                                    }else{
                                       $prop.=  '<input type="checkbox" name="checkbox-checked" class="custom-checkbox approvedBtn" data-vsid="'.$tran->id.'"/>';
                                    }

                        $prop.= '</label>
                                <div id="loader'.$tran->id.'" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </div>
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
        
        $vendor = Vendor::where('id', $vendorSequenceNumber->vendor_id)->first();
        $pdtls = ProgramDetail::where('vendor_sequence_number_id', $id)->get();

        $motherVasselIds = $pdtls->pluck('mother_vassel_id')->unique()->filter()->toArray();
        
        $motherVassels = MotherVassel::whereIn('id', $motherVasselIds)->get();


        // Group ProgramDetails by mother_vassel_id
        $data = ProgramDetail::where('vendor_sequence_number_id', $id)
            ->get()
            ->groupBy(function ($item) use ($motherVassels) {
            $motherVassel = $motherVassels->where('id', $item->mother_vassel_id)->first();
            return $motherVassel ? $motherVassel->name : 'Unknown';
            });
        
        $alldata = ProgramDetail::where('vendor_sequence_number_id', $id)->get();

        
        return view('admin.vendor.vendor_wise_program_list', compact('data','vendor','vendorSequenceNumber','alldata'));
    }


    public function addSequenceNumberApproved(Request $request)
    {
        $request->validate([
            'vsId' => 'required',
        ]);

        $data = VendorSequenceNumber::find($request->vsId);
        $data->approved = 1;
        $data->approved_date = date('Y-m-d');
        $data->approved_by = Auth::user()->id;
        $data->save();

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Sequence number approved successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }

    public function addSequenceNumberChecked(Request $request)
    {
        $request->validate([
            'vsId' => 'required',
        ]);

        $data = VendorSequenceNumber::find($request->vsId);
        $data->checked = 1;
        $data->checked_date = date('Y-m-d');
        $data->checked_by = Auth::user()->id;
        $data->save();

        $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Sequence number checked successfully.</b></div>";
        return response()->json(['status'=> 300,'message'=>$message]);
    }


    public function addWalletBalance(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
            'walletamount' => 'required',
        ]);

        $transaction = new Transaction();
        $transaction->amount =  $request->walletamount;
        $transaction->at_amount =  $request->walletamount;
        $transaction->tran_type = "Wallet";
        $transaction->description = "Add Wallet Balance";
        $transaction->note = "Add Wallet Balance";
        $transaction->payment_type = $request->payment_type;
        $transaction->table_type = "Expense";
        $transaction->vendor_id = $request->vendorId;
        $transaction->date = date('Y-m-d');
        $transaction->save();
        $transaction->tran_id = 'DP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        if ($transaction->save()) {
           
            $vendor = Vendor::where('id', $request->vendorId)->first();
            $vendor->balance += $request->walletamount;
            $vendor->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Vendor balance increased Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

        

        
    }

    public function getWalletTransaction($id)
    {
        $vendor = Vendor::where('id', $id)->first();
        $transactions = Transaction::where('vendor_id', $id)
            ->orderBy('id', 'DESC')
            ->get();


        $deposit = Transaction::where('vendor_id', $id)->where('tran_type', 'Wallet')->sum('amount');
        $expenses = Transaction::where('vendor_id', $id)->where('tran_type', 'Advance')->sum('amount');

        $balance = $deposit - $expenses;

        return view('admin.vendor.wallet_transaction', compact('transactions', 'vendor', 'balance'));
    }

    public function exportExcel(Request $request)
    {
        $tab = $request->input('tab'); // 'sequence' or 'all-data'
        $motherVessel = $request->input('mother_vessel', 'All Trips');
        $vendor = $request->input('vendor');
        $sequenceNumber = $request->input('sequence_number');

        // Fetch data based on tab
        if ($tab === 'sequence') {
            // Replace with your logic to fetch $data for the specific mother vessel
            $data = []; // Example: Fetch data for $motherVessel
        } else {
            // Replace with your logic to fetch $alldata
            $data = []; // Example: Fetch all data
        }

        $filename = 'Vendor_Trip_List_' . str_replace(' ', '_', $vendor) . '_' . $sequenceNumber . '_' . str_replace(' ', '_', $motherVessel) . '.xlsx';

        return Excel::download(new VendorTripExport($data, $vendor, $sequenceNumber, $motherVessel, $tab === 'sequence'), $filename);
    }


    // check duplicate or wrong data
    public function checkDuplicateWrongData(Request $request)
    {
        // Validate the file
        $request->validate([
            'vendor_report' => 'required|file|mimes:xlsx,xls,csv|max:2048', // Limit to 2MB
        ]);

        try {
            $file = $request->file('vendor_report');
            $vsID = $request->vendor_sequence_number_id;
            $vendorID = $request->vendor_id;

            // Log file details for debugging
            Log::info('Processing file: ' . $file->getClientOriginalName());
            Log::info('File size: ' . $file->getSize() . ' bytes');

            // Read Excel file
            $excelData = Excel::toArray([], $file);

            // Check if Excel data is empty or has no sheets
            if (empty($excelData) || !isset($excelData[0])) {
                Log::error('Excel file is empty or has no sheets');
                return redirect()->back()->withErrors(['vendor_report' => 'The uploaded Excel file is empty or invalid.']);
            }

            $excelData = $excelData[0]; // Get first sheet
            Log::info('Excel data rows: ' . count($excelData));

            // dd($excelData ); 
            // Check if the sheet is empty
            if (empty($excelData)) {
                Log::error('No data found in the first sheet');
                return redirect()->back()->withErrors(['vendor_report' => 'No data found in the Excel file.']);
            }

            // Check if the 9th column exists (0-based index: 8)
            if (!isset($excelData[0][8])) {
                Log::error('9th column (challan_no) not found in Excel file');
                return redirect()->back()->withErrors(['vendor_report' => 'The Excel file does not contain a 9th column (challan_no).']);
            }

            // Extract challan numbers from the 9th column, skipping the header row
            $excelChallanNos = array_column(array_slice($excelData, 1), 8); // Skip first row, get 9th column

            // Check if any challan numbers were extracted
            if (empty($excelChallanNos)) {
                Log::error('No valid challan numbers found in the 9th column');
                return redirect()->back()->withErrors(['vendor_report' => 'No valid challan numbers found in the 9th column.']);
            }

            // Log the extracted challan numbers
            Log::info('Extracted challan numbers: ' . json_encode($excelChallanNos));

            // Get challan numbers from ProgramDetails
            $programDetails = ProgramDetail::where('vendor_id', $vendorID)
                ->where('vendor_sequence_number_id', $vsID)
                ->pluck('challan_no')
                ->toArray();

            // Log database challan numbers
            Log::info('Database challan numbers: ' . json_encode($programDetails));

            // Find matching and non-matching challan numbers
            $matchingChallans = array_intersect($excelChallanNos, $programDetails);
            $nonMatchingChallans = array_diff($excelChallanNos, $programDetails);
            $missingInExcel = array_diff($programDetails, $excelChallanNos);

            dd($nonMatchingChallans, $missingInExcel, $matchingChallans);

            // Return view with data
            return view('vendor.challan_report', [
                'matching_challans' => array_values($matchingChallans),
                'non_matching_challans' => array_values($nonMatchingChallans),
                'missing_in_excel' => array_values($missingInExcel),
                'total_excel_records' => count($excelChallanNos),
                'total_db_records' => count($programDetails)
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error processing Excel file: ' . $e->getMessage());
            return redirect()->back()->withErrors(['vendor_report' => 'An error occurred while processing the Excel file: ' . $e->getMessage()]);
        }
    }

}
