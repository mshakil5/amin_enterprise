<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
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
        $data = Transaction::where('status', 1)->get();
        return view('admin.accounts.ledger.receivable', compact('data'));
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
}
