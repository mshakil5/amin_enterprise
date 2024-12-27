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
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function receivableLedger(Request $request)
    {
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
                            ->when($request->input('vendor_id'), function ($query) use ($request) {
                                $query->where("vendor_id",$request->input('vendor_id'));
                            })
                            ->sum('amount');



        return view('admin.accounts.ledger.advance', compact('data','vendors', 'mvassels', 'clients','drAmount','crAmount'));
        
    }

    public function payableLedger(Request $request)
    {
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

            $program = ProgramDetail::with('advancePayment')->where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->get();

            $pdids = $program->pluck('id')->toArray();

            $cashAdv = Transaction::whereIn('program_detail_id', $pdids)->where('payment_type', 'Cash')->sum('amount');
            $fuelAdv = Transaction::whereIn('program_detail_id', $pdids)->where('payment_type', 'Fuel')->sum('amount');
            $scalecost = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('scale_fee');
            $line_charge = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('line_charge');
            $carryingBill = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('carrying_bill');
            $carryingQty = ProgramDetail::where('vendor_id', $request->vendor_id)->where('mother_vassel_id', $request->mv_id)->sum('dest_qty');

            // dd($cashAdv);
            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->get();
            return view('admin.accounts.ledger.vendorVasselReport', compact('mvassels', 'vendors','fuelAdv','scalecost','carryingBill','carryingQty','line_charge','cashAdv'));
        }
        
    }



    public function showLedgerAccounts()
    {
        $chartOfAccounts = ChartOfAccount::select('id', 'account_head', 'account_name','status')->where('status', 1)
        ->get();
        return view('admin.accounts.ledger.accountname', compact('chartOfAccounts'));
    }

    public function asset($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Purchase', 'Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Sold', 'Deprication'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.asset', compact('data', 'totalBalance','accountName'));
    }

    public function expense($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Prepaid', 'Due Adjust'])->sum('at_amount');
        $totalBalance = $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.expense', compact('data', 'totalBalance','accountName'));
    }

    public function income($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Advance Adjust', 'Refund'])->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Refund'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Current', 'Advance Adjust'])->sum('at_amount');
        $totalBalance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.income', compact('data', 'totalBalance','accountName'));
    }

    public function liability($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Received'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Payment'])->sum('at_amount');
        $totalBalance = $totalDrAmount - $totalCrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.liability', compact('data', 'totalBalance','accountName'));
    }

    public function equity($id, Request $request)
    {
        $data = Transaction::where('chart_of_account_id', $id)->get();
        $totalDrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Payment'])->sum('at_amount');
        $totalCrAmount = Transaction::where('chart_of_account_id', $id)->whereIn('tran_type', ['Received'])->sum('at_amount');
        $totalBalance =  $totalCrAmount - $totalDrAmount;
        $accountName = ChartOfAccount::where('id', $id)->first()->account_name;
        return view('admin.accounts.ledger.equity', compact('data', 'totalBalance','accountName'));
    }


}
