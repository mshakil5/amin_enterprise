<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\MotherVassel;
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
}
