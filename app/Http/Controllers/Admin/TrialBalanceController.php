<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MotherVassel;
use App\Models\Transaction;

class TrialBalanceController extends Controller
{
    public function trialBalance(Request $request)
    {
        $mvassels = MotherVassel::latest()->get();

        if ($request->isMethod('post')) {
          $motherVassel = MotherVassel::find($request->mv_id);
          $expenses = Transaction::with('chartOfAccount')
            ->where('mother_vassel_id', $request->mv_id)
            ->where('table_type', 'Expenses')
            ->whereNotNull('chart_of_account_id')
            ->get();

            // dd($expenses);

          return view('admin.accounts.trial_balance.index', compact('mvassels', 'motherVassel', 'expenses'));
        }

        return view('admin.accounts.trial_balance.index', compact('mvassels'));
    }
}
