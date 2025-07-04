<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\MotherVassel;
use App\Models\Program;
use App\Models\ProgramDetail;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class ReportController extends Controller
{
    public function challanPostingVendorReport(Request $request)
    {
        if (!(in_array('15', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->mv_id) {
            $data = ProgramDetail::selectRaw('
                                        vendor_id,
                                        COUNT(*) as total_records,
                                        SUM(CASE WHEN headerid IS NOT NULL THEN 1 ELSE 0 END) as challan_received,
                                        SUM(CASE WHEN headerid IS NULL THEN 1 ELSE 0 END) as challan_not_received,
                                        SUM(CASE WHEN dest_status = 0 THEN 1 ELSE 0 END) as challan_not_received_status
                                    ')
                                    ->where('mother_vassel_id', $request->mv_id)
                                    ->when($request->input('ghat_id'), function ($query) use ($request) {
                                        $query->where('ghat_id', $request->input('ghat_id'));
                                    })
                                    ->groupBy('vendor_id')
                                    ->get();

            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->orderby('id', 'DESC')->get();
            $mid = $request->mv_id;
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors','data','mid'));
        } else {
            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->orderby('id', 'DESC')->get();
            
            $mid = $request->mv_id ?? null;
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors','mid'));
        }
        
    }


    public function challanPostingReport($vid, $mid)
    {
        // $data = Program::with('programDetail','programDetail.programDestination','programDetail.programDestination.destinationSlabRate')->first();

        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('vendor_id', $vid)->where('mother_vassel_id', $mid)->whereNotNull('headerid')->get();

        $missingHeaderIds = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('vendor_id', $vid)->where('mother_vassel_id', $mid)->whereNull('headerid')->get();
        
        $vendor = Vendor::select('id','name','balance')->where('id',$vid)->first();
        $motherVesselName = MotherVassel::where('id', $mid)->first()->name;

        $duePaymentTransaction = Transaction::where('vendor_id', $vid)
                                  ->where('mother_vassel_id', $mid)
                                  ->where('description', 'Carrying Bill')
                                  ->where('tran_type', 'Due Payment')
                                  ->select('amount')
                                  ->sum('amount');

        return view('admin.report.challanPostingReport', compact('data','vendor','motherVesselName','missingHeaderIds', 'mid', 'vid', 'duePaymentTransaction'));
    }

    public function challanPostingDateReport($id)
    {
        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('mother_vassel_id', $id)->whereNotNull('headerid')->get();
        $data = $data->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });

    


        // dd($data);
        // $mdata = DB::table('program_details')
        //                 ->select(DB::raw('DATE_FORMAT(date, "%M-%Y") as month_year'), DB::raw('SUM(riyal_amount) as total'))
        //                 ->where('status', 2)
        //                 ->groupBy('month_year')
        //                 ->orderBy('date', 'DESC')
        //                 ->get();


        $motherVesselName = MotherVassel::where('id', $id)->first()->name;
        return view('admin.report.dailyposting', compact('data','motherVesselName','id'));
    }

    public function deleteProgramDetails($id)
    {
        DB::beginTransaction();
    
        $data = ProgramDetail::findOrFail($id);
        
        $transaction = Transaction::where('program_detail_id', $id)->first();
        $advance_payment = AdvancePayment::where('program_detail_id', $id)->first();
        
        if ($transaction) {
            $transaction->delete();
        }
    
        if ($advance_payment) {
            $advance_payment->delete();
        }
    
        $data->delete();

        DB::commit();
    
        return redirect()->back()->with('success', 'Record deleted successfully!');
    }

    public function storeDuePayment2(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);
        if ($vendor->balance < $request->due_amount) {
            return redirect()->back()->with('error', 'Insufficient balance in vendor wallet to make this due payment.');
        }

        $dueAmount = $request->input('due_amount');
        $transaction = new Transaction();
        $transaction->amount = $dueAmount;
        $transaction->tran_type = "Due Payment";
        $transaction->description = "Carrying Bill";
        $transaction->note = $request->comment;
        $transaction->payment_type = "Wallet";
        $transaction->table_type = "Due Payment";
        $transaction->mother_vassel_id  = $request->mother_vessel_id;
        $transaction->vendor_id = $request->vendor_id;
        $transaction->client_id = $request->client_id;
        $transaction->date = date('Y-m-d');
        $transaction->save();
        $transaction->tran_id = 'DP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        if ($transaction->save()) {
           
           $vendor->balance -= $dueAmount;
           $vendor->save();
            return redirect()->back()->with('success', 'Due payment submitted successfully!');
        }

    }
    public function storeDuePayment(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);
        $dueAmount = $request->input('due_amount');

        if ($dueAmount > 0) {
            // Due Payment
            if ($vendor->balance < $dueAmount) {
                return redirect()->back()->with('error', 'Insufficient balance in vendor wallet to make this due payment.');
            }

            $transaction = new Transaction();
            $transaction->amount = $dueAmount;
            $transaction->tran_type = "Due Payment";
            $transaction->description = "Carrying Bill";
            $transaction->note = $request->comment;
            $transaction->payment_type = "Wallet";
            $transaction->table_type = "Due Payment";
            $transaction->vendor_id = $request->vendor_id;
            $transaction->client_id = $request->client_id;
            $transaction->vendor_sequence_number_id = $request->vendor_sequence_number_id;
            $transaction->date = date('Y-m-d');
            $transaction->save();

            $transaction->tran_id = 'DP' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            $vendor->balance -= $dueAmount;
            $vendor->save();

            return redirect()->back()->with('success', 'Due payment submitted successfully!');
        } else {
            // Advance Adjustment
            $adjustAmount = abs($dueAmount);

            $transaction = new Transaction();
            $transaction->amount = $adjustAmount;
            $transaction->tran_type = "Advance Adjust";
            $transaction->description = "Overpayment Adjustment";
            $transaction->note = $request->comment;
            $transaction->payment_type = "Wallet";
            $transaction->table_type = "Advance Adjustment";
            $transaction->vendor_id = $request->vendor_id;
            $transaction->client_id = $request->client_id;
            $transaction->vendor_sequence_number_id = $request->vendor_sequence_number_id;
            $transaction->date = date('Y-m-d');
            $transaction->save();

            $transaction->tran_id = 'AA' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            $vendor->balance += $adjustAmount;
            $vendor->save();

            return redirect()->back()->with('success', 'Advance adjustment completed successfully!');
        }
    }

}
