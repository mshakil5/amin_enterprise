<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function receivableLedger(Request $request)
    {
        $data = Transaction::where('status', 1)->get();
        return view('admin.accounts.ledger.receivable', compact('data'));
    }
}
