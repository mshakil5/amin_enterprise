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
use App\Models\Account;
use Carbon\Carbon;

class VendorController extends Controller
{
    public function index()
    {
        if (!(in_array('8', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $data = Vendor::orderby('id','DESC')->get();
        $startDate = Carbon::parse('2025-07-20');
        $vendorSeqNums = VendorSequenceNumber::orderby('id', 'DESC')->where('created_at', '>', $startDate)->get();
        return view('admin.vendor.index', compact('data','vendorSeqNums'));
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
        $data->opening_balance = $request->opening_balance;
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

    public function generateUniqueCodeold($vendorName)
    {
        $words = explode(' ', $vendorName);
        $firstLetters = array_map(fn($word) => strtoupper($word[0]), $words);
        $code = implode('', $firstLetters);
        $uniqueCode = $code;

        return $uniqueCode;
    }

    public function generateUniqueCode($vendorName)
    {
        
        $words = explode(' ', trim($vendorName));
        $uniqueCode = strtoupper($words[0]);
        
        return $uniqueCode;
    }


    public function addSequenceNumber(Request $request)
    {
        // 1. Better Validation
        $validated = $request->validate([
            'vendorId'   => 'required|exists:vendors,id',
            'challanqty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($validated) {
            
            $vendor = Vendor::findOrFail($validated['vendorId']);
            $lastSequence = VendorSequenceNumber::where('vendor_id', $vendor->id)
                ->whereYear('created_at', date('Y'))
                ->lockForUpdate()
                ->max('sequence');

            $nextSequence = ($lastSequence ?? 0) + 1;
            $uniqueCode = $this->generateUniqueCode($vendor->name);

            $data = VendorSequenceNumber::create([
                'vendor_id'  => $vendor->id,
                'qty'        => $validated['challanqty'],
                'notmarkqty' => $validated['challanqty'],
                'sequence'   => $nextSequence,
                'unique_id'  => "{$uniqueCode}_{$nextSequence}_" . date('Y'),
                'date'       => now()->format('Y-m-d'),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'status'  => 200,
                'message' => '<div class="alert alert-success">Data stored successfully.</div>',
                'data'    => $data
            ]);
        });
    }


    public function getSequenceNumber(Request $request)
    {
        
        $vendor = Vendor::where('id', $request->vendorId)->first();

        $data = VendorSequenceNumber::where('vendor_id',$request->vendorId)->orderBy('id', 'DESC')->get();
        $i = 1;
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
                            <td class="d-none">' . $i++ . '</td>
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
                            <td>';
                                    if($programCount > 0){
                                       $prop.=  '<a class="btn btn-success btn-xs" href="'.route('admin.vendor.sequence.show', $tran->id).'">'.$tran->unique_id.'</a>';
                                    }else{
                                       $prop.=  '<span class="btn btn-danger btn-xs">'.$tran->unique_id.' (No data)</span>';
                                    }

                            
                            $prop.=  '</td>
                            <td><a class="btn btn-primary btn-xs" href="'.route('admin.vendor.sequence.ledger', $tran->id).'">Ledger</a>
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

                            <td>
                                <button class="btn btn-info btn-xs editQtyBtn" 
                                        data-id="' . $tran->id . '" 
                                        data-qty="' . $tran->qty . '" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editQtyModal">
                                    <i class="fas fa-edit"></i>
                                </button>
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

    // get Vendor Wise ProgramList
    public function getVendorWiseProgramList($id)
    {
        $vendorSequenceNumber = VendorSequenceNumber::where('id', $id)->first();

        $totalPaidTransaction = Transaction::where('vendor_sequence_number_id', $id)->latest()->get();
        // $totalPaidTransaction = Transaction::where('vendor_sequence_number_id', $id)->sum('amount');
        
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
        $clientId = $alldata->first()->client_id ?? null;
        
        return view('admin.vendor.vendor_wise_program_list', compact('data','vendor','vendorSequenceNumber','alldata', 'clientId', 'vendorSequenceNumber', 'totalPaidTransaction'));
    }


    public function addSequenceNumberApproved(Request $request)
    {
        $request->validate([
            'vsId' => 'required',
        ]);

        if (Auth::user()->role_id != 1) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Sorry, You do not have permission to approved this.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

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

        if (Auth::user()->role_id != 1) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Sorry, You do not have permission to checked this.</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

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

        $account = Account::find($request->account_id);
        
        if (!$account || $account->amount < $request->walletamount) {
            $message = "<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Insufficient Balance in Office..!</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        }

        $account->amount -= $request->walletamount;
        $account->save();

        $transaction = new Transaction();
        $transaction->amount =  $request->walletamount;
        $transaction->at_amount =  $request->walletamount;
        $transaction->tran_type = "Wallet";
        $transaction->description = "Add Wallet Balance";
        $transaction->payment_type = $request->payment_type;
        $transaction->table_type = "Expenses";
        $transaction->vendor_id = $request->vendorId;
        $transaction->account_id = $request->account_id;
        $transaction->date = $request->wallet_date ?? date('Y-m-d');
        $transaction->note = $request->note;
        $transaction->vendor_sequence_number_id = $request->vsequence;
        $transaction->created_by = Auth::user()->id;
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


    public function updateWalletBalance(Request $request)
    {
        $request->validate([
            'walletamount' => 'required',
        ]);


        $transaction = Transaction::find($request->tranid);

        $upvendor = Vendor::where('id', $transaction->vendor_id)->first();
        $upvendor->balance -= $request->walletamount;
        $upvendor->save();
        

        $transaction->amount =  $request->walletamount;
        $transaction->at_amount =  $request->walletamount;
        $transaction->payment_type = $request->payment_type;
        $transaction->account_id = $request->account_id;
        $transaction->date = $request->wallet_date ?? date('Y-m-d');
        $transaction->note = $request->note;
        $transaction->vendor_sequence_number_id = $request->vsequence;
        $transaction->updated_by = Auth::user()->id;
        $transaction->save();
        $transaction->tran_id = 'DP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        if ($transaction->save()) {
           
            $vendor = Vendor::where('id', $transaction->vendor_id)->first();
            $vendor->balance += $request->walletamount;
            $vendor->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Vendor balance update Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function reduceWalletBalance(Request $request)
    {
        $request->validate([
            'vendorId' => 'required',
            'walletamount' => 'required',
        ]);

        $account = Account::find($request->account_id);
        $account->amount += $request->walletamount;
        $account->save();

        $transaction = new Transaction();
        $transaction->amount =  $request->walletamount;
        $transaction->at_amount =  $request->walletamount;
        $transaction->tran_type = "Wallet";
        $transaction->description = "Reduce Wallet Balance";
        $transaction->payment_type = $request->payment_type;
        $transaction->table_type = "Income";
        $transaction->vendor_id = $request->vendorId;
        $transaction->account_id = $request->account_id;
        $transaction->date = $request->wallet_date ?? date('Y-m-d');
        $transaction->note = $request->note;
        $transaction->vendor_sequence_number_id = $request->vsequence;
        $transaction->created_by = Auth::user()->id;
        $transaction->save();
        $transaction->tran_id = 'DP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        if ($transaction->save()) {
           
            $vendor = Vendor::where('id', $request->vendorId)->first();
            $vendor->balance -= $request->walletamount;
            $vendor->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Vendor balance reduce Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }




    public function getWalletTransaction($id)
    {
        $vendor = Vendor::where('id', $id)->first();
        $transactions = Transaction::where('vendor_id', $id)
            ->whereIn('table_type', ['Expenses', 'Expense', 'Income'])
            ->where('tran_type', 'Wallet')
            ->orderBy('id', 'DESC')
            ->get();


        $deposit = Transaction::where('vendor_id', $id)->where('tran_type', 'Wallet')->sum('amount');
        $expenses = Transaction::where('vendor_id', $id)->where('tran_type', 'Advance')->sum('amount');

        $balance = $deposit - $expenses;
        
        $startDate = Carbon::parse('2025-07-20');
            
        $vendorSeqNums = VendorSequenceNumber::orderby('id', 'DESC')->where('vendor_id', $id)->where('created_at', '>', $startDate)->get();

        return view('admin.vendor.wallet_transaction', compact('transactions', 'vendor', 'balance','vendorSeqNums'));
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


    // getVendorWiseProgramList
    public function getVendorWiseProgramLedger($id)
    {
        $vendorSequenceNumber = VendorSequenceNumber::where('id', $id)->first();
        $vendor = Vendor::where('id', $vendorSequenceNumber->vendor_id)->first();
        
        $summary = ProgramDetail::where('vendor_sequence_number_id', $id)
                    ->selectRaw('
                        SUM(dest_qty) as total_dest_qty,
                        SUM(carrying_bill) as total_carrying_bill,
                        SUM(line_charge) as total_line_charge,
                        SUM(scale_fee) as total_scale_fee,
                        SUM(other_cost) as total_other_cost,
                        SUM(advance) as total_advance
                    ')
                    ->first();

        $advanceData = \App\Models\AdvancePayment::whereIn('program_detail_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('program_details')
                        ->where('vendor_sequence_number_id', $id);
                })->selectRaw('
                    SUM(cashamount) as total_cashamount,
                    SUM(fuelqty) as total_fuelqty,
                    SUM(fuelamount) as total_fuelamount
                ')->first();

        $totalPaidTransaction = Transaction::where('vendor_sequence_number_id', $id)->sum('amount');


        
        return view('admin.vendor.sequence_wise_program_ledger', compact('vendorSequenceNumber', 'totalPaidTransaction', 'summary','vendor','advanceData'));
    }

    public function updateQty(Request $request)
    {
        $request->validate([
            'vendor_sequence_id' => 'required',
            'qty' => 'required|numeric|min:0',
        ]);

        $vendorSequenceNumber = VendorSequenceNumber::find($request->vendor_sequence_id);
        $vendorSequenceNumber->qty = $request->qty;
        $vendorSequenceNumber->save();

        return response()->json([
            'status' => 200,
            'message' => 'Quantity updated successfully'
        ]);

    }

    
    public function getSequences($id)
    {
        $sequences = VendorSequenceNumber::where('vendor_id', $id)
                        ->where('status', 1)->orderby('id', 'DESC')
                        ->select('id', 'unique_id')
                        ->get();

        return response()->json($sequences);
    }

    public function getWithoutTripFuelBillAdjust($id)
    {
        
        $vendor = Vendor::where('id', $id)->first();
        $sequences = VendorSequenceNumber::where('vendor_id', $id)
                        ->where('status', 1)->orderby('id', 'DESC')
                        ->select('id', 'unique_id')
                        ->get();
        return view('admin.vendor.without_trip_fuelcost', compact('vendor','sequences', 'id'));
    }


}
