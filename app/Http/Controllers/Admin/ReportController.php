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
use App\Models\ReportNote;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function challanPostingVendorReport(Request $request)
    {
        if (!(in_array('15', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->mv_id) {
            $data = ProgramDetail::selectRaw('
                                        MIN(program_id) as program_id,
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

    public function storeReportNotes(Request $request)
    {
        $request->validate([
            'program_id' => 'required',
            'vendor_id' => 'required',
            'note' => 'required',
            'date' => 'required|date',
        ]);

        ReportNote::create([
            'program_id' => $request->program_id,
            'vendor_id' => $request->vendor_id,
            'mother_vassel_id' => null,
            'note' => $request->note,
            'date' => $request->date,
            'created_by' => Auth::id(),
        ]);

        $message = "<div class='alert alert-success'>Note added successfully.</div>";
        return response()->json(['status'=>300,'message'=>$message]);
    }

    public function updateNote(Request $request, ReportNote $note)
    {
        $request->validate([
            'date' => 'required|date',
            'note' => 'required',
        ]);

        $note->update([
            'date' => $request->date,
            'note' => $request->note,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status'=>300,'message'=>"<div class='alert alert-success'>Note updated successfully.</div>"]);
    }

    public function challanPostingReport($vid, $mid)
    {
        // Define common relationships to eager load (prevents N+1 queries)
        $relationships = [
            'vendor:id,name',
            'ghat:id,name',
            'destination:id,name',
            'advancePayment.petrolPump:id,name',
            'programDestination.destinationSlabRate',
        ];

        // Single query for posted records
        $data = ProgramDetail::with($relationships)
            ->where('vendor_id', $vid)
            ->where('mother_vassel_id', $mid)
            ->whereNotNull('headerid')
            ->orderByDesc('date')
            ->get();

        // Single query for missing header records
        $missingHeaderIds = ProgramDetail::with($relationships)
            ->where('vendor_id', $vid)
            ->where('mother_vassel_id', $mid)
            ->whereNull('headerid')
            ->orderByDesc('date')
            ->get();

        // Fetch vendor & vessel (use select for performance)
        $vendor       = Vendor::select('id', 'name', 'balance')->findOrFail($vid);
        $motherVesselName = MotherVassel::select('id', 'name')->findOrFail($mid)->name;

        // Sum due payment transactions
        $duePaymentTransaction = Transaction::where('vendor_id', $vid)
            ->where('mother_vassel_id', $mid)
            ->where('description', 'Carrying Bill')
            ->where('tran_type', 'Due Payment')
            ->sum('amount');

        // Pre-calculate summary (do it once, not in blade)
        $totalCarryingBill = $data->sum('carrying_bill');
        $totalAdvance      = $data->sum('advance');
        $totalLineCharge   = $data->sum('line_charge');
        $totalScaleFee     = $data->sum('scale_fee');
        $totalOtherCost    = $data->sum('other_cost');
        $totalDestQty      = $data->sum('dest_qty');
        $totalFuelQty      = $data->sum(fn($item) => $item->advancePayment->fuelqty ?? 0);

        $summary = [
            'total_qty'           => $totalDestQty,
            'total_carrying_bill' => $totalCarryingBill,
            'total_advance'       => $totalAdvance,
            'total_line_charge'   => $totalLineCharge,
            'total_scale_fee'     => $totalScaleFee,
            'total_other_cost'    => $totalOtherCost,
            'total_fuel_qty'      => $totalFuelQty,
            'due_payment'         => $duePaymentTransaction,
            'total_due'           => $totalCarryingBill - $totalAdvance - $duePaymentTransaction,
            'vendor_balance'      => $vendor->balance,
            'record_count'        => $data->count(),
            'missing_count'       => $missingHeaderIds->count(),
        ];

        return view('admin.report.challanPostingReport', compact(
            'data',
            'vendor',
            'motherVesselName',
            'missingHeaderIds',
            'mid',
            'vid',
            'summary'
        ));
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

        try {
            $data = ProgramDetail::findOrFail($id);
            
            // Get the authenticated user's name
            $deletedBy = auth()->user()->name;

            $transaction = Transaction::where('program_detail_id', $id)->first();
            $advance_payment = AdvancePayment::where('program_detail_id', $id)->first();
            
            if ($transaction) {
                $transaction->deleted_by = $deletedBy;
                $transaction->save();
                $transaction->delete();
            }

            if ($advance_payment) {
                $advance_payment->deleted_by = $deletedBy;
                $advance_payment->save();
                $advance_payment->delete();
            }

            $data->deleted_by = $deletedBy;
            $data->save();
            $data->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Record deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
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
