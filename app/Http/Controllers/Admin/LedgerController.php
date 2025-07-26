<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\MotherVassel;
use App\Models\ProgramDetail;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function receivableLedger(Request $request)
    {
        if (!(in_array('16', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();

        $data = Transaction::where('status', 1)
                            ->whereNull('table_type')
                            ->orderby('id','DESC')
                            ->when($request->input('client_id'), function ($query) use ($request) {
                                $query->where("client_id",$request->input('client_id'));
                            })
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->get();
        $drAmount = Transaction::where('status', 1)
                            ->whereNull('table_type')
                            ->where('tran_type','Received')
                            ->when($request->input('client_id'), function ($query) use ($request) {
                                $query->where("client_id",$request->input('client_id'));
                            })
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->sum('amount');
        $crAmount = Transaction::where('status', 1)
                            ->whereNull('table_type')
                            ->where('tran_type','Advance')
                            ->when($request->input('client_id'), function ($query) use ($request) {
                                $query->where("client_id",$request->input('client_id'));
                            })
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->sum('amount');



        return view('admin.accounts.ledger.receivable', compact('data','vendors', 'mvassels', 'clients','drAmount','crAmount'));
        
    }

    public function advanceLedger(Request $request)
    {
        if (!(in_array('16', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();

        $data = Transaction::where('status', 1)
                            ->whereNull('table_type')
                            ->orderby('id','DESC')
                            ->where('tran_type','Advance')
                            ->when($request->input('client_id'), function ($query) use ($request) {
                                $query->where("client_id",$request->input('client_id'));
                            })
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->when($request->input('vendor_id'), function ($query) use ($request) {
                                $query->where("vendor_id",$request->input('vendor_id'));
                            })
                            ->when($request->input('payment_type'), function ($query) use ($request) {
                                $query->where("payment_type",$request->input('payment_type'));
                            })
                            ->get();

        $crAmount = Transaction::where('status', 1)
                            ->whereNull('table_type')
                            ->where('tran_type','Advance')
                            ->when($request->input('client_id'), function ($query) use ($request) {
                                $query->where("client_id",$request->input('client_id'));
                            })
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->when($request->input('vendor_id'), function ($query) use ($request) {
                                $query->where("vendor_id",$request->input('vendor_id'));
                            })
                            ->when($request->input('payment_type'), function ($query) use ($request) {
                                $query->where("payment_type",$request->input('payment_type'));
                            })
                            ->sum('amount');


                            $mvid = $request->input('mv_id') ?? null;
                            $vendor_id = $request->input('vendor_id') ?? null;
                            $payment_type = $request->input('payment_type') ?? null;
                            $client_id = $request->input('client_id') ?? null;

        return view('admin.accounts.ledger.advance', compact('data','vendors', 'mvassels', 'clients','crAmount','mvid','vendor_id','payment_type','client_id'));
        
    }

    public function payableLedger(Request $request)
    {
        if (!(in_array('16', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();

        $data = ProgramDetail::where('status', 1)
                            ->orderby('id','DESC')
                            ->when($request->input('mv_id'), function ($query) use ($request) {
                                $query->where("mother_vassel_id",$request->input('mv_id'));
                            })
                            ->when($request->input('vendor_id'), function ($query) use ($request) {
                                $query->where("vendor_id",$request->input('vendor_id'));
                            })
                            ->get();

        return view('admin.accounts.ledger.payable', compact('data','vendors', 'mvassels', 'clients'));
        
    }

    public function vendorLedger(Request $request)
    {
        if (!(in_array('15', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        
        $clients = Client::orderby('id','DESC')->where('status', 1)->get();
        $mvassels = MotherVassel::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $vendors = Vendor::select('id','name')->orderby('id','DESC')->where('status',1)->get();
        $data = Transaction::where('status', 1)->get();
        return view('admin.accounts.ledger.vendor', compact('data','vendors', 'mvassels', 'clients'));
    }

    public function vendorVasselLedger(Request $request)
    {

        if (! $request->isMethod('POST')) {
            return route('vendorLedger');
        } else {

            // dd($request->all());

            $program = ProgramDetail::with('advancePayment')
                        ->where('vendor_id', $request->vendor_id)
                        ->where('mother_vassel_id', $request->mv_id)
                        ->when($request->input('ghat_id'), function ($query) use ($request) {
                            $query->where('ghat_id', $request->input('ghat_id'));
                        })
                        ->get();

            $pdids = $program->pluck('id')->toArray();
            $vendorSequenceIDs = $program->pluck('vendor_sequence_number_id')->toArray();

            $vendorSequence = VendorSequenceNumber::whereIn('id', $vendorSequenceIDs)->where('vendor_id', $request->vendor_id)->get();


            $cashAdv = AdvancePayment::whereIn('program_detail_id', $pdids)->sum('cashamount');
            $fuelAdv = AdvancePayment::whereIn('program_detail_id', $pdids)->sum('fuelamount');
            $fuelQty = AdvancePayment::whereIn('program_detail_id', $pdids)->sum('fuelqty');
            $tripCount = AdvancePayment::whereIn('program_detail_id', $pdids)->count();

            $scalecost = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('scale_fee');
            $line_charge = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('line_charge');
            $carryingBill = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('carrying_bill');
            $carryingQty = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('dest_qty');

            // dd($cashAdv);
            $vendors = Vendor::where('id', $request->vendor_id)->first();
            $mvassels = MotherVassel::where('id', $request->mv_id)->first();
            return view('admin.accounts.ledger.vendorVasselReport', compact('mvassels', 'vendors','fuelAdv','scalecost','carryingBill','carryingQty','line_charge','cashAdv','tripCount','fuelQty','vendorSequence'));
        }
        
    }



    public function showLedgerAccounts()
    {
        if (!(in_array('17', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name','status')->where('status', 1)
        ->get();
        return view('admin.accounts.ledger.accountname', compact('chartOfAccounts'));
    }

    // public function asset($id, Request $request)
    // {
    //     $data = Transaction::where('chart_of_account_id', $id)->get();
    //     $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Purchase', 'Payment'])->sum('at_amount');
    //     $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Sold', 'Deprication'])->sum('at_amount');
    //     $totalBalance = $totalDrAmount - $totalCrAmount;
    //     $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
    //     return view('admin.accounts.ledger.asset', compact('data', 'totalBalance','accountName'));
    // }

    public function asset($id, Request $request)
    {
        $query = Transaction::where('chart_of_account_id', $id);

        if ($request->filled('start_date')) {
            $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();
            $query->whereBetween('date', [$request->start_date, $endDate]);
        }

        $data = $query->get();

        $totalDrAmount = (clone $query)->whereIn('tran_type', ['Purchase', 'Payment'])->sum('at_amount');
        $totalCrAmount = (clone $query)->whereIn('tran_type', ['Sold', 'Deprication'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;

        $accountName = ChartOfAccount::find($id)?->account_name;

        return view('admin.accounts.ledger.asset', compact('data', 'totalBalance', 'accountName', 'id'));
    }

    // public function expense($id, Request $request)
    // {
    //     $data = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust'])->get();
    //     $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust'])->sum('at_amount');
    //     $totalBalance = $totalDrAmount;
    //     $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
    //     return view('admin.accounts.ledger.expense', compact('data', 'totalBalance','accountName'));
    // }
    public function expense($id, Request $request)
    {
        $query = Transaction::where('chart_of_account_id', $id)
                    ->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust']);

        if ($request->filled('start_date')) {
            $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();
            $query->whereBetween('date', [$request->start_date, $endDate]);
        }

        $data = $query->get();
        $totalDrAmount = (clone $query)->sum('at_amount');
        $totalBalance = $totalDrAmount;
        $accountName = ChartOfAccount::find($id)?->account_name;

        return view('admin.accounts.ledger.expense', compact('data', 'totalBalance', 'accountName', 'id'));
    }

    // public function income($id, Request $request)
    // {
    //     $data = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Advance Adjust', 'Refund'])->get();
    //     $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Refund'])->sum('at_amount');
    //     $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Advance Adjust'])->sum('at_amount');
    //     $totalBalance =  $totalCrAmount - $totalDrAmount;
    //     $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
    //     return view('admin.accounts.ledger.income', compact('data', 'totalBalance','accountName'));
    // }

    public function income($id, Request $request)
    {
        $query = Transaction::where('chart_of_account_id', $id)
                    ->whereIn('tran_type', ['Current', 'Advance Adjust', 'Refund']);

        if ($request->filled('start_date')) {
            $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();
            $query->whereBetween('date', [$request->start_date, $endDate]);
        }

        $data = $query->get();

        $totalDrAmount = (clone $query)->whereIn('tran_type', ['Refund'])->sum('at_amount');
        $totalCrAmount = (clone $query)->whereIn('tran_type', ['Current', 'Advance Adjust'])->sum('at_amount');
        $totalBalance = $totalCrAmount - $totalDrAmount;

        $accountName = ChartOfAccount::find($id)?->account_name;

        return view('admin.accounts.ledger.income', compact('data', 'totalBalance', 'accountName', 'id'));
    }

    // public function liability($id, Request $request)
    // {
    //     $data = Transaction::where('chart_of_account_id', $id)->get();
    //     $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Received'])->sum('at_amount');
    //     $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Payment'])->sum('at_amount');
    //     $totalBalance = $totalDrAmount - $totalCrAmount;
    //     $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
    //     return view('admin.accounts.ledger.liability', compact('data', 'totalBalance','accountName'));
    // }

    public function liability($id, Request $request)
    {
        $query = Transaction::where('chart_of_account_id', $id);

        if ($request->filled('start_date')) {
            $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();
            $query->whereBetween('date', [$request->start_date, $endDate]);
        }

        $data = $query->get();
        $totalDrAmount = (clone $query)->whereIn('tran_type', ['Received'])->sum('at_amount');
        $totalCrAmount = (clone $query)->whereIn('tran_type', ['Payment'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;

        $accountName = ChartOfAccount::find($id)?->account_name;

        return view('admin.accounts.ledger.liability', compact('data', 'totalBalance', 'accountName', 'id'));
    }

    // public function equity($id, Request $request)
    // {
    //     $data = Transaction::where('chart_of_account_id', $id)->get();
    //     $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Payment'])->sum('at_amount');
    //     $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Received'])->sum('at_amount');
    //     $totalBalance =  $totalCrAmount - $totalDrAmount;
    //     $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
    //     return view('admin.accounts.ledger.equity', compact('data', 'totalBalance','accountName'));
    // }

    public function equity($id, Request $request)
    {
        $query = Transaction::where('chart_of_account_id', $id);

        if ($request->filled('start_date')) {
            $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();
            $query->whereBetween('date', [$request->start_date, $endDate]);
        }

        $data = $query->get();
        $totalDrAmount = (clone $query)->whereIn('tran_type', ['Payment'])->sum('at_amount');
        $totalCrAmount = (clone $query)->whereIn('tran_type', ['Received'])->sum('at_amount');
        $totalBalance = $totalCrAmount - $totalDrAmount;

        $accountName = ChartOfAccount::find($id)?->account_name;

        return view('admin.accounts.ledger.equity', compact('data', 'totalBalance', 'accountName', 'id'));
    }

    public function vendor($id, Request $request)
    {
        $data = Transaction::where('vendor_id', $id)
            ->whereDate('date', '>=', '2025-06-28')
            ->when($request->start_date, function ($query) use ($request) {
                $query->whereDate('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->whereDate('date', '<=', $request->end_date);
            })
            ->orderBy('id', 'DESC')
            ->get();
        $totalDrAmount = $data->where('tran_type', 'Wallet')->sum('amount');
        $totalCrAmount = $data->whereIn('payment_type', ['Cash', 'Fuel', 'Wallet'])->sum('amount');
        $totalBalance =  $totalCrAmount - $totalDrAmount;

        $accountName = Vendor::find($id)->name ?? 'N/A';

        return view('admin.accounts.ledger.vendor2', compact('data', 'totalBalance', 'accountName', 'id'));
    }

}
