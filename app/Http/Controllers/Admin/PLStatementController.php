<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PLStatementController extends Controller
{
    public function profitAndLossStatement(Request $request)
    {
        $data = Transaction::where('status', 1)->get();
        
        $totalReceive = Transaction::whereNotNull('mother_vassel_id')->where('tran_type', 'Received')->where('status', 1)->sum('amount');


        return view('admin.accounts.pl.index', compact('data','totalReceive'));
    }
}
